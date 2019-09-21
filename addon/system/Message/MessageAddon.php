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
namespace addon\system\Message;

use addon\system\Message\common\model\Message;
use app\common\controller\BaseAddon;
use addon\system\Message\common\model\SiteMessage;

/**
 * 消息管理
 */
class MessageAddon extends BaseAddon
{
	public $info = array(
		'name' => 'Message',
		'title' => '消息管理',
		'description' => '消息管理',
		'status' => 1,
		'author' => '',
		'version' => '1.0',
		'visble' => 1,
		'type' => 'ADDON_SYSTEM',
		'category' => 'SYSTEM',
		'content' => '消息管理',
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
		return success();
		return error('', 'System addon can not be uninstalled!');
	}
	
	/**
	 * 初始化站点数据，在添加站点的时候用
	 * @param integer $site_id
	 * @return boolean
	 */
	public function addToSite($site_id)
	{
		$message_model = new Message();
		$list = $message_model->getMessageTypeList([ 'addon' => 'system' ], "title,keyword,port,addon");
		
		$new_list = [];
		if (!empty($list['data'])) {
			foreach ($list['data'] as $k => $v) {
				$item = [
					'site_id' => $site_id,
					'site_keyword' => $v['keyword'],
					'create_time' => time(),
					'addon' => $v['addon']
				];
				$new_list[] = $item;
			}
			if (!empty($new_list)) {
				$site_message_model = new SiteMessage();
				$res = $site_message_model->addSiteMessageTypeList($new_list);
			} else {
				$res = success();
			}
		} else {
			$res = success();
		}
		
		
		return $res;
	}
	
	/**
	 * 删除站点数据--删除站点时调用
	 * @param integer $site_id
	 * @return boolean
	 */
	public function delFromSite($site_id)
	{
		$message_model = new SiteMessage();
		$message_model->deleteSiteMessageType([ 'site_id' => $site_id ]);
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
		$msgtpl_model = new SiteMessage();
		$data = $msgtpl_model->getSiteMessageTypeList([ 'site_id' => $site_id ]);
		$new_data = [];
		if ($data['data']) {
			foreach ($data['data'] as $k => $v) {
				$item = $v;
				unset($item['id']);
				$item['site_id'] = $new_site_id;
				$new_data[] = $item;
			}
			$res = $msgtpl_model->addSiteMessageTypeList($new_data);
		}
		return success();
	}
	
	/**
	 * 发送消息
	 * @param array $param
	 */
	public function sendMessage($param = [])
	{
		$message_model = new Message();
		$type_info = $message_model->getMessageTypeInfo([ "keyword" => $param["keyword"] ]);
		if (empty($type_info["data"]))
			return error('', '无效的消息类型!');
		
		$param["addon"] = $type_info["data"]["addon"];
		$result = hook("messageTemplate", $param);
		if (empty($result))
			return error('', '无效的消息类型!');
		
		return $result[0];
	}
}