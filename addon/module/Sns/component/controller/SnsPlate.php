<?php

namespace addon\module\Sns\component\controller;

use app\common\controller\BaseDiyView;

/**
 * sns板块
 * 创建时间：2018年7月9日19:51:02
 *
 */
class SnsPlate extends BaseDiyView
{
	
	/**
	 * 前台输出
	 */
	public function parseHtml($attr)
	{
		return $this->fetch('sns_plate/module.html');
	}
	
	/**
	 * 后台编辑界面
	 */
	public function edit()
	{
		return $this->fetch("sns_plate/design.html");
	}
}