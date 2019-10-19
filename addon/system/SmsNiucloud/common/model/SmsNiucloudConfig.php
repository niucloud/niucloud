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

namespace addon\system\SmsNiucloud\common\model;

use app\common\model\Site;

/**
 * 牛云短信配置
 * @author lzw
 */
class SmsNiucloudConfig
{
	
	public $site_model;
	
	public function __construct()
	{
		$this->site_model = new Site();
	}
	
	/**
	 * 设置牛云短信配置
	 */
	public function setNiuCloudSmsConfig($data)
	{
		//如果开启当前短信,则关闭其他短信
		if ($data["status"] == 1) {
			hook("closeSms", [ "site_id" => $data['site_id'] ]);
		}
		$data["name"] = 'NC_SMS_NIUCLOUD_CONFIG';
		$condition = array(
			"name" => 'NC_SMS_NIUCLOUD_CONFIG'
		);
		$res = $this->site_model->setSiteConfig($data, $condition);
		return $res;
	}
	
	/**
	 * 获取该站点牛云短信配置
	 * @param number $site_id
	 */
	public function getNiuCloudSmsConfigInfo($site_id)
	{
		$config = $this->site_model->getSiteConfigInfo([ "site_id" => $site_id, "name" => "NC_SMS_NIUCLOUD_CONFIG" ]);
		$value = [];
		if (!empty($config["data"]["value"])) {
			$value = json_decode($config["data"]["value"], true);
		}
		$config["data"]["value"] = $value;
		return $config;
	}
}