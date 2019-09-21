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

namespace addon\system\OAuthLogin\sitehome\controller;

use app\common\controller\BaseSiteHome;
use app\common\model\Site;

/**
 * 第三方登录控制器
 */
class Loginconfig extends BaseSiteHome
{
	protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'
	
	public function __construct()
	{
		parent::__construct();
		$this->replace = [
			'ADDON_NS_OAUTH_LOGIN_IMG' => __ROOT__ . '/addon/system/OAuthLogin/sitehome/view/public/img',
		];
	}
	
	public function lists()
	{
		if (IS_AJAX) {
			$list = hook('getOAuthLoginConfig', [ 'site_id' => $this->siteId ]);
			$res = error();
			if (!empty($list)) {
				$res = success();
				$res['data'] = [
					'count' => !empty($list) ? count($list) : 0,
					'list' => $list
				];
			}
			return $res;
		}
		return $this->fetch('login_config/lists', [], $this->replace);
	}
	
	/**
	 * 修改登录插件的跳转
	 */
	public function config()
	{
		$addon_name = input('addon_name', '');
		hook('oauthLoginConfig', [ 'name' => $addon_name ]);
	}
	
	/**
	 * 修改登录插件状态
	 * @return multitype:string mixed
	 */
	public function setOAuthLoginStatus()
	{
		if (IS_AJAX) {
			$site_id = $this->siteId;
			$name = input('name', '');
			$status = input('status', 1);
			$site_model = new Site();
			$res = $site_model->setSiteConfig([ "status" => $status, 'site_id' => $site_id, "name" => $name ]);
			return $res;
		}
	}
	
}