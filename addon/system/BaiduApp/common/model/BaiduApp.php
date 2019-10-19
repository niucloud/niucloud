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

namespace addon\system\BaiduApp\common\model;


use app\common\model\Site;

/**
 * 百度小程序配置
 */
class BaiduApp
{
	
	public $site_model;
	
	public function __construct()
	{
		$this->site_model = new Site();
	}
	
	/**
	 * 设置小程序开发配置
	 */
	public function setBaiduAppConfig($data)
	{
		$data["name"] = 'NC_BAIDUAPP_CONFIG';
		$res = $this->site_model->setSiteConfig($data);
		return $res;
	}
	
	/**
	 * 获取小程序开发配置
	 */
	public function getBaiduAppConfigInfo($site_id)
	{
		$config = $this->site_model->getSiteConfigInfo([ 'site_id' => $site_id, 'name' => 'NC_BAIDUAPP_CONFIG' ]);
		$value = [];
		if (!empty($config["data"]["value"])) {
			$value = json_decode($config["data"]["value"], true);
		}
		$config["data"]["value"] = $value;
		return $config;
	}
}