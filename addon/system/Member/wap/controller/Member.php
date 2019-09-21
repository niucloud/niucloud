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

namespace addon\system\Member\wap\controller;

use app\common\controller\BaseSite;
use think\Cookie;

/**
 * 会员信息 控制器
 * 创建时间：2018年8月31日16:55:48
 */
class Member extends BaseSite
{
	
	protected $replace = [];
	
	public function __construct()
	{
		parent::__construct();
		$this->replace = [
			'ADDON_NC_WAP_ACCOUNT_CSS' => __ROOT__ . '/addon/system/Member/wap/view/' . $this->wap_style . '/public/css',
			'ADDON_NC_WAP_ACCOUNT_JS' => __ROOT__ . '/addon/system/Member/wap/view/' . $this->wap_style . '/public/js',
			'ADDON_NC_WAP_ACCOUNT_IMG' => __ROOT__ . '/addon/system/Member/wap/view/' . $this->wap_style . '/public/img',
			'ADDON_NC_WAP_MEMBER_VIEW' => __ROOT__ . '/addon/system/Member/wap/view/' . $this->wap_style
		];
	}
	
	/**
	 * 个人信息
	 */
	public function member()
	{
		$this->assign("title", "个人信息");
		return $this->fetch($this->wap_style . '/member/member_info', [], $this->replace);
	}
	
	/**
	 * 用户昵称编辑
	 */
	public function nickname()
	{
		$this->assign("title", "我的昵称");
		return $this->fetch($this->wap_style . '/member/member_nickname', [], $this->replace);
	}
	
	/**
	 * 用户手机号编辑
	 */
	public function mobile()
	{
		$this->assign("title", "手机号");
		return $this->fetch($this->wap_style . '/member/member_mobile', [], $this->replace);
	}
	
	/**
	 * 用户姓名编辑
	 */
	public function realname()
	{
		$this->assign("title", "我的姓名");
		return $this->fetch($this->wap_style . '/member/member_realname', [], $this->replace);
	}
	
	/**
	 * 我的头像编辑
	 */
	public function headimg()
	{
		$this->assign("title", "我的头像");
		return $this->fetch($this->wap_style . '/member/member_headimg', [], $this->replace);
	}
	
	/**
	 *
	 * @return string[]
	 */
	public function loginOut()
	{
		$this->access_token = Cookie::set("access_token_" . request()->siteid(), '');
		$this->redirect(url("wap/login/login"));
	}
	
}