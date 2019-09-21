<?php

namespace addon\system\DiyView\component\controller;

use app\common\controller\BaseDiyView;

/**
 * 底部菜单·组件
 * 创建时间：2018年9月13日16:31:06
 *
 */
class BottomMenu extends BaseDiyView
{
	
	/**
	 * 前台输出
	 */
	public function parseHtml($attr)
	{
		return $this->fetch('bottom_menu/bottom_menu.html');
	}
	
	/**
	 * 后台编辑界面
	 */
	public function edit()
	{
		return $this->fetch("bottom_menu/design.html");
	}
}