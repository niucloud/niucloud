<?php

namespace addon\system\Member\component\controller;

use app\common\controller\BaseDiyView;

/**
 * 会员中心·组件
 * 创建时间：2018年7月9日12:13:35
 */
class Member extends BaseDiyView
{
	
	/**
	 * 前台输出
	 * 创建时间：2018年7月9日12:12:47
	 */
	public function parseHtml($attr)
	{
		return $this->fetch('member/member.html');
	}
	
	/**
	 * 后台编辑界面
	 * 创建时间：2018年7月9日12:12:55
	 */
	public function edit()
	{
		return $this->fetch("member/design.html");
	}
}