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
namespace addon\system\Sms;

use app\common\controller\BaseAddon;
use addon\system\Sms\common\model\SmsSend;
use addon\system\Sms\common\model\MessageRecords;
use addon\system\Message\common\model\SiteMessage;
use addon\system\Sms\common\model\Message;

/**
 * 短信管理插件
 */
class SmsAddon extends BaseAddon
{
	public $info = array(
		'name' => 'Sms',
		'title' => '短信管理',
		'description' => '短信组件',
		'status' => 1,
		'author' => '',
		'version' => '1.0',
		'visble' => 1,
		'type' => 'ADDON_SYSTEM',
		'category' => 'SYSTEM',
		'content' => 'this is a SMS!',
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
	 * Returns:['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function install()
	{
		return success();
	}
	
	/**
	 * 卸载
	 * Returns:['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function uninstall()
	{
		//return error('', 'System addon can not be uninstalled!');
		return success();
	}
	
	/**
	 * 初始化站点数据，在添加站点的时候用
	 * @param integer $site_id
	 * Returns:['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function addToSite($site_id)
	{
		
		return success();
	}
	
	/**
	 * 删除站点数据--删除站点时调用
	 * @param integer $site_id
	 * Returns:['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function delFromSite($site_id)
	{
	    $message = new Message();
	    $message->deleteSite($site_id);
		return success();
	}
	
	/**
	 * 复制站点数据--复制站点时调用
	 * @param integer $site_id
	 * Returns:['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function copyToSite($site_id, $new_site_id)
	{
		
		return success();
	}
	
	/**
	 * 短信管理 (变量解析)
	 * @param array $param
	 */
	public function smsMessage($params = [])
	{
		$sms_send = new SmsSend();
		$addon_result = $sms_send->getSmsAddon([ "site_id" => $params["site_id"] ]);//获取可用的短信
		
		if ($addon_result["code"] != 0)
			return error('', '站点没有开启状态的短信发送方式');
		
		$addon_name = $addon_result["data"];//正启用的短信方式
		
		$message_model = new SiteMessage();
		$type_info = $message_model->getSiteMessageTypeViewInfo([ "keyword" => $params["keyword"], "site_id" => $params["site_id"] ]);
		if (((!empty($params["support_type"]) && stripos($params["support_type"], "Sms") !== false) || empty($params["support_type"])) && stripos($type_info["data"]["port"], "Sms") !== false && $type_info["data"]["sms_is_open"] == 1) {
		    $message_records_model = new MessageRecords();
			//是否即时发送短信
			$data = array(
				"site_id" => $params["site_id"],
				"account" => $params["account"],
				"keyword" => $params["keyword"],
				"var_parse" => $params["var_parse"],
				"create_time" => time()
			);
			if ($type_info["data"]["is_instant"] == 1) {
				$params["addon_name"] = $addon_name;
				$result = $sms_send->send($params);
				$data["status"] = -1;
				if ($result["code"] == 0) {
					$data["addon_name"] = $addon_name;//记录发送方式
                    $data["code"] = $result["data"]["code"];//模板编号
                    $data["sign"] = $result["data"]["sign"];//签名
                    $data["type_name"] = $result["data"]["type_name"];//发送方式名称
					$data["send_time"] = time();//记录发送时间
					$data["status"] = 1;
				} else {
					$data["result"] = $result["message"];
				}
			}
			$res = $message_records_model->addSmsMessageReocrds($data);//添加发送记录
			if (!empty($result)) {
				return $result;
			} else {
				return success();
			}
		} else {
			return error();
		}
		
	}
	
	/**
	 * 延时发送消息
	 * @param array $param
	 */
	public function cronMessageSend($param = [])
	{
		$message_records_model = new MessageRecords();
		$message_records_list = $message_records_model->getSmsMessageReocrdsList([ "status" => 0 ]);
		if (!empty($message_records_list["data"])) {
			foreach ($message_records_list["data"] as $k => $v) {

                $sms_send = new SmsSend();
                $addon_result = $sms_send->getSmsAddon(["site_id" => $v["site_id"]]);//获取可用的短信
                if ($addon_result["code"] != 0)
                    return error('', '站点没有开启状态的短信发送方式');

                $addon_name = $addon_result["data"];//正启用的短信方式


				$params = array(
					"site_id" => $v["site_id"],
					"keyword" => $v["keyword"],
					"var_parse" => $v["var_parse"],
					"account" => $v["account"],
					"addon_name" => $addon_name
				);
				$result = $sms_send->send($params);
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
				
				$message_records_model->editSmsMessageReocrds($data, $condition);
			}
		}
	}

    /**
     * 获取启用的短信发送方式
     * @param $param
     */
	public function getSiteSmsType($param){
        $sms_send = new SmsSend();
        $addon_result = $sms_send->getSmsAddon([ "site_id" => $param["site_id"] ]);//获取可用的短信
        return $addon_result;
    }
}