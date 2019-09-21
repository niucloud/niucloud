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

namespace addon\system\OAuthLoginQQ\sitehome\controller;

use addon\system\OAuthLoginQQ\common\model\QQLoginConfig;
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
		if (request()->isAjax()) {
			$app_key = input("app_key", '');
			$app_secret = input("app_secret", '');
			$status = input("app_status", 0);
			$value = json_encode([ 'app_key' => $app_key, 'app_secret' => $app_secret ]);
			$config_model = new QQLoginConfig();
			
			$data = array(
				"site_id" => $this->siteId,
				"value" => $value,
				"status" => $status
			);
			$res = $config_model->setQQLoginConfig($data);
			return $res;
		} else {
			$config_model = new QQLoginConfig();
			$config = $config_model->getQQLoginConfig($this->siteId);
			
			$this->assign("config_info", $config["data"]["value"]);
			$this->assign("status", $config["data"]["status"]);
			return $this->fetch('index/config');
		}
	}
	
}