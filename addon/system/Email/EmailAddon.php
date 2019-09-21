<?php
// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+
namespace addon\system\Email;

use app\common\controller\BaseAddon;
use addon\system\Email\common\model\EmailConfig;
use addon\system\Email\common\model\Email;
use addon\system\Email\common\model\MessageRecords;
use addon\system\Email\common\model\Message;

/**
 * 邮箱管理插件
 */
class EmailAddon extends BaseAddon
{
	public $info = array(
		'name' => 'Email',
		'title' => '邮箱管理',
		'description' => '管理邮箱的设置发送等功能',
		'status' => 1,
		'author' => '',
		'version' => '1.0',
		'visble' => 1,
		'type' => 'ADDON_SYSTEM',
		'category' => 'SYSTEM',
		'content' => '管理邮箱的设置发送等功能',
		//预置插件，多个用英文逗号分开
		'preset_addon' => '',
		'support_addon' => '',
	    'support_app_type' => 'wap,weapp'
	);
	public $config;
	
	public function __construct()
	{
		parent::__construct();
		$this->config = $this->config_info;
	}
	
	/**
	 * 安装
	 */
	public function install()
	{
		return success();
	}
	
	/**
	 * 卸载
	 */
	public function uninstall()
	{
		return error('', 'System addon can not be uninstalled!');
	}
	
	/**
	 * 初始化站点数据，在添加站点的时候用
	 * @param integer $site_id
	 * @return boolean
	 */
	public function addToSite($site_id)
	{
	    return success();
	}
	
	/**
	 * 删除站点数据--删除站点时调用
	 * @param integer $site_id
	 * @return boolean
	 */
	public function delFromSite($site_id)
	{
		$email_config = new EmailConfig();
		$res = $email_config->deleteSiteConfig([ 'site_id' => $site_id, 'name' => 'EMAIL_CONFIG' ]);
		$message = new Message();
		$message->deleteSite($site_id);
		return success();
	}
	
	/**
	 * 复制站点数据--复制站点时调用
	 * @param integer $site_id
	 * @param integer $new_site_id
	 * @return boolean
	 */
	public function copyToSite($site_id, $new_site_id)
	{
		$email_config = new EmailConfig();
		$config_info = $email_config->getEmailConfig($site_id);
		if (!empty($config_info['data'])) {
			$config_info['data']['site_id'] = $new_site_id;
			unset($config_info['data']['id']);
			$email_config->setEmailConfig($new_site_id, json_encode($config_info['data']['value']));
		}
		return success();
	}
	
	/**
	 * 编辑消息模板
	 * @param array $param
	 */
	public function doEditMessage($param = [])
	{
		if ($param["name"] == "Email") {
			$this->redirect(addon_url('Email://sitehome/config/edit', [ 'keyword' => $param['keyword'] ]));
		}
	}
	
	/**
	 * 邮箱发送
	 * @param array $param
	 */
	public function emailMessage($param = [])
	{
		$emailSendModel = new Email();
		$res = $emailSendModel->send($param);
		return $res;
	}
	
	/**
	 * 邮箱消息延时发送
	 * @param array $param
	 */
	public function cronMessageSend($param = [])
	{
		$message_records_model = new MessageRecords();
		$message_records_list = $message_records_model->getEmailMessageRecordsList([ "status" => 0 ]);
		$emailSendModel = new Email();
		if (!empty($message_records_list["data"])) {
			foreach ($message_records_list["data"] as $k => $v) {
				$params = array(
					"site_id" => $v["site_id"],
					"account" => $v["account"],
					"title" => $v["title"],
					"content" => $v["content"]
				);
				$result = $emailSendModel->emailSend($params);
				
				$data = array();
				$condition = array(
					"id" => $v["id"],
					"site_id" => $v["site_id"]
				);
				if ($result["code"] == 0) {
					$data["send_time"] = time();
					$data["stauts"] = 1;
				} else {
					$data["stauts"] = -1;
					$data["result"] = $result["message"];
				}
				
				$message_records_model->editEmailMessageRecords($data, $condition);
			}
		}
	}
	
}