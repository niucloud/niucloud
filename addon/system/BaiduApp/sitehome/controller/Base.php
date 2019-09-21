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
namespace addon\system\BaiduApp\sitehome\controller;

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
			'NC_BAIDU_CSS' => __ROOT__ . '/addon/system/BaiduApp/sitehome/view/public/css',
			'NC_BAIDU_JS' => __ROOT__ . '/addon/system/BaiduApp/sitehome/view/public/js',
			'NC_BAIDU_IMG' => __ROOT__ . '/addon/system/BaiduApp/sitehome/view/public/img',
			'NC_BAIDU_SVG' => __ROOT__ . '/addon/system/BaiduApp/sitehome/view/public/svg',
		];
	}
	
}