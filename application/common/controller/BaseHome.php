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

namespace app\common\controller;

class BaseHome extends BaseController
{
	public function __construct()
	{
		//执行父类构造函数
		parent::__construct();
		//检测登录
		$this->checkLogin();
	}
	
	/**
	 * 验证登录
	 */
	private function checkLogin()
	{
		//验证登录
		if (!UID) {
			//没有登录跳转到登录页面
			$this->redirect('home/Login/login');
		}
	}
}