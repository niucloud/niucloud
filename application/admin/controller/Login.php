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

namespace app\admin\controller;

use app\common\controller\BaseController;
use app\common\model\User;

class Login extends BaseController
{
	/**
	 * 登录页面
	 */
	public function login()
	{
		$this->redirect(url('home/login/login'));
		if ($this->isLogin()) {
			$this->logout();
		}
		return $this->fetch('login/login');
	}
	
	/**
	 * 登录操作
	 */
	public function loginHandle()
	{
		$username = input('username', '');
		$password = input('password', '');
		$user_model = new User();
		$res = $user_model->login($username, $password);
		$res['data']['url'] = url('Index/index');
		
		return $res;
		
	}
	
	/**
	 * 退出操作
	 */
	public function logout()
	{
		if ($this->isLogin()) {
			$user_model = new User();
			$user_model->logout();
			$this->redirect('admin/Login/login', 5, '退出成功');
		} else {
			$this->redirect('admin/Login/login');
		}
	}
	
	/**
	 * 清理缓存
	 */
	public function clearCache()
	{
		\think\Cache::clear();
		$this->success("success", url('Login/login'));
		return [ 'success', '清理成功', url('Index/index') ];
	}
}