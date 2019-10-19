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

namespace app\wap\controller;

use app\common\controller\BaseSite;

/**
 * 首页
 */
class Index extends BaseSite
{
	
	//主页
	public function index()
	{
		hook("index");
		return $this->getDiyView([ "name" => "DIYVIEW_SITE", 'addon_name' => $this->site_info['addon_app'] ]);
	}
	
}