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

namespace addon\app\NcApplet\wap\controller;

use app\common\controller\BaseSite;

/**
 * 会员 控制器
 * 创建时间：2018年9月8日11:15:02
 */
class Member extends BaseSite
{
	//会员中心
	public function index()
	{
		if (!empty($this->access_token)) {
			return $this->getDiyView([ "name" => "DIYVIEW_MEMBER" ]);
		} else {
			$this->redirect(url('wap/login/login'));
		}
	}
	
}