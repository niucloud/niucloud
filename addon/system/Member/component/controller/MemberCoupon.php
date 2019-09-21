<?php

namespace addon\system\Member\component\controller;

use app\common\controller\BaseDiyView;

/**
 * 会员账户·组件
 * 创建时间：2018年9月8日10:33:02 星期六
 *
 */
class MemberCoupon extends BaseDiyView
{
	
	/**
	 * 前台输出
	 */
	public function parseHtml($attr)
	{
		return $this->fetch('member_coupon/member_coupon.html');
	}
	
	/**
	 * 后台编辑界面
	 */
	public function edit()
	{
		return $this->fetch("member_coupon/design.html");
	}
}