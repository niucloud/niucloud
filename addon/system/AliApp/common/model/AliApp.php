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

namespace addon\system\AliApp\common\model;


use app\common\model\Site;

/**
 * 支付宝小程序配置
 */
class AliApp
{
	
	public $site_model;
	
	public function __construct()
	{
		$this->site_model = new Site();
	}
	
	/**
	 * 设置小程序开发配置
	 */
	public function setAliAppConfig($data)
	{
		$data["name"] = 'NC_ALIAPP_CONFIG';
		$res = $this->site_model->setSiteConfig($data);
		return $res;
	}
	
	/**
	 * 获取小程序开发配置
	 */
	public function getAliAppConfigInfo($site_id)
	{
		$config = $this->site_model->getSiteConfigInfo([ 'site_id' => $site_id, 'name' => 'NC_ALIAPP_CONFIG' ]);
		$value = [];
		if (!empty($config["data"]["value"])) {
			$value = json_decode($config["data"]["value"], true);
		}
		$config["data"]["value"] = $value;
		return $config;
	}
}