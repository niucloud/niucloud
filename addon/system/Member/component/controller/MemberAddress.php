<?php

namespace addon\system\Member\component\controller;

use app\common\controller\BaseDiyView;

/**
 * 会员地址·组件
 * 创建时间：2018年9月8日10:46:34 星期六
 *
 */
class MemberAddress extends BaseDiyView
{
	
	/**
	 * 前台输出
	 */
	public function parseHtml($attr)
	{
		return $this->fetch('member_address/member_address.html');
	}
	
	/**
	 * 后台编辑界面
	 */
	public function edit()
	{
		return $this->fetch("member_address/design.html");
	}
}