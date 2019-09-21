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

namespace addon\system\SmsNiucloud\admin\controller;

use app\common\controller\BaseAdmin;
use addon\system\SmsNiucloud\common\model\SmsNiucloudConfig;

class Index extends BaseAdmin
{
	protected $replace = [];
	
	public function __construct()
	{
		parent::__construct();
		$this->replace = [
			'ADDON_SMS_NIUCLUD_IMG' => __ROOT__ . '/addon/system/SmsNiucloud/admin/view/public/image',
			'ADDON_SMS_NIUCLUD_JS' => __ROOT__ . '/addon/system/SmsNiucloud/admin/view/public/js'
		];
	}
	
	/**
	 * 牛云短信配置
	 */
	public function config()
	{
		$site_id = 0;
		$niucloud_config = new SmsNiucloudConfig();
		if (IS_AJAX) {
			$value = input('value', '');
			$status = input('status', 1);
			$data = array(
				"value" => $value,
				"site_id" => $site_id,
				"status" => $status,
				"update_time" => time(),
			);
			$res = $niucloud_config->setNiuCloudSmsConfig($data);
			return $res;
		} else {
			$get_sms_config = $niucloud_config->getNiuCloudSmsConfigInfo($site_id);
			$this->assign('info', $get_sms_config['data']['value']);
			return $this->fetch('index/config',[],$this->replace);
		}
	}
}