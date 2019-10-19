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
 * 会员中心
 */
class Member extends BaseSite
{
	protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'
	
	public function __construct()
	{
		parent::__construct();
	}
	
	//个人中心
	public function index()
	{
		$check = check_auth();
		if ($check['code'] == 0) {
			return $this->getDiyView([ "name" => "DIYVIEW_MEMBER", 'addon_name' => $this->site_info['addon_app'] ]);
		} else {
			$this->redirect($check['data']);
		}
	}
	
	/**
	 * 个人信息
	 */
	public function member()
	{
		$this->assign("title", "个人信息");
		return $this->fetch("style/" . $this->wap_style . '/member/member_info', [], $this->replace);
	}
	
	/**
	 * 用户昵称编辑
	 */
	public function nickname()
	{
		$this->assign("title", "我的昵称");
		return $this->fetch("style/" . $this->wap_style . '/member/member_nickname', [], $this->replace);
	}
	
	/**
	 * 用户手机号编辑
	 */
	public function mobile()
	{
		$this->assign("title", "手机号");
		return $this->fetch("style/" . $this->wap_style . '/member/member_mobile', [], $this->replace);
	}
	
	/**
	 * 用户姓名编辑
	 */
	public function realname()
	{
		$this->assign("title", "我的姓名");
		return $this->fetch("style/" . $this->wap_style . '/member/member_realname', [], $this->replace);
	}
	
	/**
	 * 我的头像编辑
	 */
	public function headimg()
	{
		$this->assign("title", "我的头像");
		return $this->fetch("style/" . $this->wap_style . '/member/member_headimg', [], $this->replace);
	}
	
	/**
	 * 余额账户
	 */
	public function balance()
	{
		$this->assign("title", "余额明细");
		return $this->fetch("style/" . $this->wap_style . '/member/balance', [], $this->replace);
	}
	
	/**
	 * 积分账户
	 */
	public function integral()
	{
		$this->assign("title", "积分明细");
		return $this->fetch("style/" . $this->wap_style . '/member/integral', [], $this->replace);
	}
	
	/**
	 * 退出登录
	 * @return string[]
	 */
	public function loginOut()
	{
		$this->access_token = Session::set("access_token_" . request()->siteid(), '');
		$this->redirect(url("wap/login/login"));
	}
	
}