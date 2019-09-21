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

namespace app\home\controller;


use app\common\controller\BaseHome;

/**
 * 前台页面
 * @author Administrator
 */
class Index extends BaseHome
{
	/**
	 * 首页（当前用户下的app页面）
	 */
	public function index()
	{
		$this->redirect(url('admin/index/index'));
	}
}