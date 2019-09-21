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
namespace addon\system\Wechat\sitehome\controller;

use addon\system\Wechat\common\model\Wechat;

/**
 * 微信自定义菜单控制器
 */
class Menu extends Base
{
	/**
	 * 微信自定义菜单配置
	 */
	public function index()
	{
		if (IS_AJAX) {
			$wechat_config_model = new Wechat();
			$menu_info = $wechat_config_model->getWechatMenuInfo(SITE_ID);
			return $menu_info;
		} else {
			$this->assign('siteInfo', $this->siteInfo);
			return $this->fetch('Menu/index', [], $this->replace);
		}
	}
	
	/**
	 * 修改微信自定义菜单
	 */
	public function edit()
	{
		if (IS_AJAX) {
			$menu_value = input('value', '');
			$menu_json = input('json_data', '');
			$wechat_config_model = new Wechat();
			$res = $wechat_config_model->setWechatMenu(SITE_ID, $menu_value);
			if ($res['code'] != 0) {
				return $res;
			}
			$res = $this->sendWeixinMenu($menu_json);
			return $res;
		}
	}
	
	/**
	 * 公众号同步更新微信菜单
	 */
	public function sendWeixinMenu($menu_json)
	{
		$wechat_config_model = new Wechat();
		$res = $wechat_config_model->sendWechatMenu(SITE_ID, $menu_json);
		return $res;
	}
}