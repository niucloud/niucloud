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

namespace addon\system\SmsQcloud\common\model;

use app\common\model\Site;

/**
 * 腾讯云短信
 * @author lzw
 */
class SmsQcloudConfig
{
	
	public $site_model;
	
	public function __construct()
	{
		$this->site_model = new Site();
	}
	
	/**
	 * 根据站点id设置该腾讯云短信配置
	 * @param number $site_id
	 * @param string $value
	 * @param number $status
	 */
	public function setSmsQcloudConfig($data)
	{
		//如果开启当前短信,则关闭其他短信
		if ($data["status"] == 1) {
			hook("closeSms", [ "site_id" => $data['site_id'] ]);
		}
		$data["name"] = 'NC_SMS_QCLOUD_CONFIG';
		$condition = array(
			"name" => 'NC_SMS_QCLOUD_CONFIG'
		);
		$res = $this->site_model->setSiteConfig($data, $condition);
		return $res;
	}
	
	/**
	 * 根据站点id获取该腾讯云短信配置
	 * @param number $site_id
	 */
	public function getSmsQcloudConfigInfo($site_id)
	{
		$config = $this->site_model->getSiteConfigInfo([ "site_id" => $site_id, "name" => "NC_SMS_QCLOUD_CONFIG" ]);
		$value = [];
		if (!empty($config["data"]["value"])) {
			$value = json_decode($config["data"]["value"], true);
		}
		$config["data"]["value"] = $value;
		return $config;
	}
	
	/**
	 * 删除该站点腾讯云短信配置
	 * @param number $site_id
	 * Returns:['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function delSmsQcloudConfig($site_id)
	{
		$res = $this->site_model->deleteSiteConfig([ 'name' => 'NC_SMS_QCLOUD_CONFIG', 'site_id' => $site_id ]);
		return $res;
	}
}