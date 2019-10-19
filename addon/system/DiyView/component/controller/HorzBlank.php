<?php

namespace addon\system\DiyView\component\controller;

use app\common\controller\BaseDiyView;

/**
 * 辅助空白·组件
 * 创建时间：2018年7月9日12:13:35
 */
class HorzBlank extends BaseDiyView
{
	
	/**
	 * 前台输出
	 */
	public function parseHtml($attr)
	{
		return $this->fetch('horz_blank/horz_blank.html');
	}
	
	/**
	 * 后台编辑界面
	 */
	public function edit()
	{
		return $this->fetch("horz_blank/design.html");
	}
}