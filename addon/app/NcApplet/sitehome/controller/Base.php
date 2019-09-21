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
namespace addon\app\NcApplet\sitehome\controller;

use app\common\controller\BaseSiteHome;

/**
 * 控制器基类
 */
class Base extends BaseSiteHome
{
	protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'
	
	public function __construct()
	{
		parent::__construct();
		$this->replace = [
			'NIU_APPLET_CSS' => __ROOT__ . '/addon/app/NcApplet/sitehome/view/public/css',
			'NIU_APPLET_JS' => __ROOT__ . '/addon/app/NcApplet/sitehome/view/public/js',
			'NIU_APPLET_IMG' => __ROOT__ . '/addon/app/NcApplet/sitehome/view/public/img',
		];
	}
	
}