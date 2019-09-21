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
namespace addon\system\SmsNiucloud;

use addon\system\SmsNiucloud\common\model\SmsNiucloudConfig;
use app\common\controller\BaseSmsAddon;
use util\api\WebClient;


/**
 * 牛云平台短信插件
 */
class SmsNiucloudAddon extends BaseSmsAddon
{
	public $info = array(
		'name' => 'SmsNiucloud',
		'title' => '牛云平台短信',
		'description' => '牛云平台短信',
		'status' => 1,
		'author' => '',
		'version' => '1.0',
		'visble' => 0,
		'type' => 'ADDON_SYSTEM',
		'category' => 'SYSTEM',
		'content' => 'this is a file!',
		//预置插件，多个用英文逗号分开
		'preset_addon' => 'Sms',
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
	 * @return ['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function install()
	{
		return success();
	}
	
	/**
	 * 卸载
	 * @return ['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function uninstall()
	{
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
		return success();
	}
	
	/**
	 * 发送短信
	 * @param array $param
	 */
	public function sendSmsMessage($param = [])
	{
		if ($param['addon_name'] != $this->info['name']) return;
		
		$config = $this->getSmsConfig($param);
		if (!$config['config_info']['data']['status']) return;
		try {
			$auth = getAuth();
			$client = new WebClient($auth['app_key'], $auth['app_secret']);
			unset($param['app_key']);
			$res = $client->post('Sms.smsSend', $param);
		} catch (\Exception $e) {
			return error('', $e->getMessage());
		}
		return $res;
	}
	
	/**
	 * 获取短信配置
	 * @param integer $site_id
	 * Returns:['info' => [], 'site_config' => []]
	 */
	public function getSmsConfig($param = [])
	{
		$niuyun_config = new SmsNiucloudConfig();
		$config_info = $niuyun_config->getNiuCloudSmsConfigInfo($param['site_id']);
		$this->info["url"] = addon_url('smsniucloud/admin/index/config', [ 'site_id' => $param['site_id'] ]);
		$this->info['icon'] = __ROOT__ . './addon/system/' . $this->info['name'] . '/icon.png';
		return [
			'info' => $this->info,
			'config' => $config_info["data"]
		];
	}
	
	/**
	 * 关闭短信
	 * @param array $param
	 */
	public function closeSms($param = [])
	{
		$niuyun_config_model = new SmsNiucloudConfig();
		$data = array(
			"status" => 0,
			"site_id" => $param['site_id'],
			"update_time" => time()
		);
		$res = $niuyun_config_model->setNiuCloudSmsConfig($data);
		return $res;
	}
	
}