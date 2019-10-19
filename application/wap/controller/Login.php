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
use think\Session;

/**
 * 登录注册
 *
 */
class Login extends BaseSite
{
	protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'
	
	public function __construct()
	{
		parent::__construct();
		
	}
	
	/**
	 * 登录
	 */
	public function login()
	{
		if (IS_AJAX) {
			
			$username = input("username", "");
			$password = input("password", "");
			$res = api("System.Login.login", [ 'username' => $username, 'password' => $password ]);
			if ($res['code'] == 0) {
				Session::set("access_token_" . request()->siteid(), $res['data']['access_token']);
				$res['data']['redirect_login_url'] = Session::get("redirect_login_url");
			}
			return $res;
		} else {
			$res = hook("login", [ 'addon' => request()->siteAddon() ]);
			if (!empty($res)) {
				return $res[0];
			}
			return $this->fetch("style/" . $this->wap_style . '/login/login', [], $this->replace);
		}
		
	}
	
	/**
	 * 注册
	 */
	public function register()
	{
		if (IS_AJAX) {
			
			$username = input("username", "");
			$mobile = input("mobile", "");
			$email = input("email", "");
			$password = input("password", "");
			$tag = input("tag", "");//第三方登录标识
			
			$register_res = api("System.Login.register", [ 'username' => $username, 'mobile' => $mobile, 'email' => $email, 'password' => $password ]);
			if ($register_res['code'] == 0) {
				$res = api("System.Login.login", [ 'username' => $username, 'password' => $password ]);
				if ($res['code'] == 0) {
					Session::set("access_token_" . request()->siteid(), $res['data']['access_token']);
					$res['data']['redirect_login_url'] = Session::get("redirect_login_url");
					
					//第三方登录
					if (!empty($tag)) {
						$openid = input("openid", "");
						$nick_name = input("nick_name", "");
						$head_img = input("head_img", "");
						$res = api("System.Login.bindAccount", [ 'username' => $username, 'password' => $password, 'tag' => $tag, 'openid' => $openid, 'nick_name' => $nick_name, 'head_img' => $head_img ]);
					}
					
				}
				return $res;
			} else {
				return $register_res;
			}
			
		} else {
			$res = hook("register", [ 'addon' => request()->siteAddon() ]);
			if (!empty($res)) {
				return $res[0];
			}
			$register_config = api("System.Login.registerConfig", [ 'site_id' => SITE_ID ]);
			$register_config = $register_config['data'];
			if (!isset($register_config['is_allow_register'])) {
				$this->error('站点未启用注册功能');
			}
			
			$this->assign("register_config", $register_config);
			return $this->fetch("style/" . $this->wap_style . '/login/register', [], $this->replace);
		}
		
	}
	
	//忘记密码
	public function findPwd()
	{
		$this->assign("title", "忘记密码");
		return $this->fetch("style/" . $this->wap_style . '/login/find_pwd', [], $this->replace);
	}
	
	/**
	 * 注册协议
	 */
	public function agreement()
	{
		return $this->fetch("style/" . $this->wap_style . '/login/agreement', [], $this->replace);
	}
	
	/**
	 * 完善信息或绑定已有账号
	 */
	public function perfectInfoOrBindAccount()
	{
		$params = input('data', '');
		$tag = input('tag', '');
		$data = !empty($params) ? json_decode(urldecode($params), true) : [];
		$type_name_arr = [
			'qq_openid' => 'QQ账号',
			'wx_unionid' => '微信账号'
		];
		$data['name'] = $type_name_arr[ $tag ];
		
		$this->assign('data', $data);
		$this->assign('tag', $tag);
		return $this->fetch("style/" . $this->wap_style . '/login/perfectInfo_or_bindaccount', [], $this->replace);
	}
	
}