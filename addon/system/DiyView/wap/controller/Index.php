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

namespace addon\system\DiyView\wap\controller;


use app\common\controller\BaseSite;

class Index extends BaseSite
{
	
	/**
	 * 微页面推广链接
	 */
	public function page()
	{
		$name = input("name", "");
		if (empty($name)) {
			return "缺少参数";
		}
		return $this->getDiyView([ "name" => $name ]);
	}
}