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

namespace addon\system\DiyView\sitehome\controller;

use app\common\controller\BaseSiteHome;

class BaseView extends BaseSiteHome
{
	
	protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'
	
	public function __construct()
	{
		parent::__construct();
		$this->replace = [
			'DIYVIEW_CSS' => __ROOT__ . '/addon/system/DiyView/sitehome/view/public/css',
			'DIYVIEW_JS' => __ROOT__ . '/addon/system/DiyView/sitehome/view/public/js',
			'DIYVIEW_IMG' => __ROOT__ . '/addon/system/DiyView/sitehome/view/public/img',
		];
	}
}