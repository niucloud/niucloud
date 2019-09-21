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

use app\common\controller\BaseSiteHome;

/**
 * 微信控制器基类
 */
class Base extends BaseSiteHome
{
	protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'
	
	public function __construct()
	{
		parent::__construct();
		$this->replace = [
			'ADDON_WECHAT_CSS' => __ROOT__ . '/addon/system/Wechat/sitehome/view/public/css',
			'ADDON_WECHAT_JS' => __ROOT__ . '/addon/system/Wechat/sitehome/view/public/js',
			'ADDON_WECHAT_IMG' => __ROOT__ . '/addon/system/Wechat/sitehome/view/public/img',
		];
	}
	
}