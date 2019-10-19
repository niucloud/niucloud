<?php

namespace addon\system\DiyView\component\controller;

use app\common\controller\BaseDiyView;

/**
 * 图文导航·组件
 * 创建时间：2018年7月9日12:13:35
 */
class GraphicNav extends BaseDiyView
{
	
	/**
	 * 前台输出
	 */
	public function parseHtml($attr)
	{
		return $this->fetch('graphic_nav/graphic_nav.html');
	}
	
	/**
	 * 后台编辑界面
	 */
	public function edit()
	{
		return $this->fetch("graphic_nav/design.html");
	}
}