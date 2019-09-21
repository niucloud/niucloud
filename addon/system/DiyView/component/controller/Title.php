<?php

namespace addon\system\DiyView\component\controller;

use app\common\controller\BaseDiyView;

/**
 * 顶部标题·组件
 * 创建时间：2018年9月13日11:49:05
 */
class Title extends BaseDiyView
{
	
	/**
	 * 前台输出
	 */
	public function parseHtml($attr)
	{
		return $this->fetch('title/title.html');
	}
	
	/**
	 * 后台编辑界面
	 */
	public function edit()
	{
		return $this->fetch("title/design.html");
	}
}