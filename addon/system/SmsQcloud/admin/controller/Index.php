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

namespace addon\system\SmsQcloud\admin\controller;

use addon\system\SmsQcloud\common\model\SmsQcloudConfig;
use app\common\controller\BaseAdmin;

class Index extends BaseAdmin
{
	protected $replace = [];
	
	public function __construct()
	{
		parent::__construct();
		$this->siteId = 0;
		$this->replace = [
			'ADDON_NS_SMS_QCLOUD_IMG' => __ROOT__ . '/addon/system/SmsQcloud/admin/view/public/img',
			'ADDON_NS_SMS_QCLOUD_JS' => __ROOT__ . '/addon/system/SmsQcloud/admin/view/public/js',
		];
	}
	
	/**
	 * 腾讯云短信配置
	 */
	public function config()
	{
		
		$qcloud_config = new SmsQcloudConfig();
		$site_id = 0;
		if (IS_AJAX) {
			$app_key = input('app_key', '');
			$secret_key = input('secret_key', '');
			$signature = input('signature', '');
			$status = input('status', 0);
			$value = array(
				"app_key" => $app_key,
				"secret_key" => $secret_key,
				"signature" => $signature,
			);
			$value_json = json_encode($value);
			$data = array(
				"value" => $value_json,
				"site_id" => $site_id,
				"status" => $status,
				"update_time" => time(),
			);
			$res = $qcloud_config->setSmsQcloudConfig($data);
			return $res;
		} else {
			$get_sms_config = $qcloud_config->getSmsQcloudConfigInfo($site_id);
			$this->assign('status', $get_sms_config['data']['status']);
			$this->assign('list', $get_sms_config['data']['value']);
			return $this->fetch('index/config', [], $this->replace);
		}
		
	}
}