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

namespace addon\system\OAuthLoginWechat\sitehome\controller;

use addon\system\OAuthLoginWechat\common\model\WechatLoginConfig;
use app\common\controller\BaseSiteHome;

/**
 * 首页 控制器
 */
class Index extends BaseSiteHome
{
	/**
	 * QQ互联登录配置查询
	 */
	public function config()
	{
		if (IS_AJAX) {
			
			$app_key = input("app_key", '');
			$app_secret = input("app_secret", '');
			$status = input("status", '');
			$value = json_encode([ 'app_key' => $app_key, 'app_secret' => $app_secret ]);
			$data = array(
				"site_id" => $this->siteId,
				"status" => $status,
				"value" => $value,
			);
			$config_model = new WechatLoginConfig();
			$res = $config_model->setWechatLoginConfig($data);
			return $res;
		} else {
			$config_model = new WechatLoginConfig();
			$config = $config_model->getWechatLoginConfig($this->siteId);
			
			$this->assign("config_info", $config["data"]["value"]);
			$this->assign("status", $config["data"]["status"]);
			return $this->fetch('index/config');
		}
	}
	
}