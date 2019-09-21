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
	    hook("login");
		return $this->fetch("style/".$this->wap_style.'/login/login', [], $this->replace);
	}
	
	/**
	 * 注册
	 */
	public function register()
	{
        hook("login");
		$register_config = api("System.Login.registerConfig", [ 'site_id' => SITE_ID ]);
		$register_config = $register_config['data'];
		if (!isset($register_config['is_allow_register'])) {
			$this->error('站点未启用注册功能');
		}
		$this->assign("register_config",$register_config);
		return $this->fetch("style/".$this->wap_style.'/login/register', [], $this->replace);
	}
	
	//忘记密码
	public function findPwd()
	{
		$this->assign("title", "忘记密码");
		return $this->fetch("style/".$this->wap_style.'/login/find_pwd', [], $this->replace);
	}
	
	/**
	 * 注册协议
	 */
	public function agreement()
	{
		return $this->fetch("style/".$this->wap_style.'/login/agreement', [], $this->replace);
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
		return $this->fetch("style/".$this->wap_style.'/login/perfectInfo_or_bindaccount', [], $this->replace);
	}
	
}