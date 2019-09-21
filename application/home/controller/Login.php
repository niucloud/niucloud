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

use app\common\controller\BaseController;
use app\common\model\User;
use app\common\model\Config as ConfigModel;

class Login extends BaseController
{
	public function index()
	{
		$res = hook("HomeUserLoginRedirect", []);
		if (!empty($res)) {
			$this->redirect($res[0]);
		}
		$this->redirect('home/Login/login');
	}
	
	/**
	 * 登录
	 */
	public function login()
	{
		$res = hook("HomeUserLoginRedirect", []);
		if (!empty($res)) {
			$this->redirect($res[0]);
		}
		if (UID) {
			$default_site_id = cookie(DOMAIN . "default_site_id_" . UID);
			if ($default_site_id) {
				request()->siteid($default_site_id);
			}
			$this->redirect(url('sitehome/index/index'));
		}
		if (IS_AJAX) {
			$username = input('username', '');
			$password = input('password', '');
			
			$check_result = $this->checkCaptcha();
			if ($check_result['code'] != 0) return $check_result;
			
			$user_model = new User();
			$res = $user_model->login($username, $password);
			if ($res['code'] == 0) {
				
				if ($res['data']['is_admin'] == 0) {
					
					//检测站点是否关闭，系统管理员不需要控制
					$config_model = new ConfigModel();
					$config_info = $config_model->getConfigInfo([ 'name' => 'SYSTEM_SITE_CONFIG' ]);
					$system_site_config = empty($config_info['data']['value']) ? [] : json_decode($config_info['data']['value'], true);
					if (!empty($system_site_config) && $system_site_config['close_site_status'] == 1) {
						$user_model->logout();
						$res = error(1, $system_site_config['reasons_for_closing']);
						return $res;
					}
				}
				
				$default_site_id = cookie(DOMAIN . "default_site_id_" . $res['data']['uid']);
				if ($default_site_id) {
					request()->siteid($default_site_id);
					$res['data']['url'] = url('sitehome/index/index');
				} else {
					request()->siteid(0);
					$res['data']['url'] = url('admin/index/index');
				}
				
				//验证记住密码
				$remember = input('remember', '');
				if ($remember == 'on') {
					
					cookie(DOMAIN . 'username', $username, 60 * 60 * 24 * 31);
					cookie(DOMAIN . 'password', $password, 60 * 60 * 24 * 31);
				}
				
			}
			return $res;
		}
		
		//获取记住密码信息
		$user_name = cookie(DOMAIN . 'username');
		$password = cookie(DOMAIN . 'password');
		$this->assign('username', empty($user_name) ? 'admin' : $user_name);
		$this->assign('password', empty($password) ? '123456' : $password);
		
		//备案信息
		$config_model = new ConfigModel();
		$config_info = $config_model->getConfigInfo([ 'name' => 'SYSTEM_SITE_CONFIG' ]);
		$system_site_config_info = empty($config_info['data']['value']) ? [] : json_decode($config_info['data']['value'], true);
		$this->assign("system_site_config_info", $system_site_config_info);
		return $this->fetch('login/login');
	}
	
	/**
	 * 注册
	 */
	public function register()
	{
		
		if (IS_AJAX) {
			
			$check_result = $this->checkCaptcha();
			if ($check_result['code'] != 0) return $check_result;
			
			$param = input();
			$user_model = new User();
			$retval = $user_model->register($param);
			
			if ($retval['code'] == 0) {
				$retval['url'] = url('sitehome/index/index');
			}
			
			return $retval;
		}
		return $this->fetch('login/register');
	}
	
	/**
	 * 退出操作
	 */
	public function logout()
	{
		if ($this->isLogin()) {
			$user_model = new User();
			$user_model->logout();
			
		}
		$this->redirect('home/Login/login');
	}
	
}