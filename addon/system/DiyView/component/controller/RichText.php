<?php

namespace addon\system\DiyView\component\controller;

use app\common\controller\BaseDiyView;

/**
 * 富文本·组件
 * 创建时间：2018年7月9日12:13:35
 *
 */
class RichText extends BaseDiyView
{
	
	/**
	 * 前台输出
	 */
	public function parseHtml($attr)
	{
		return $this->fetch('rich_text/rich_text.html');
	}
	
	/**
	 * 后台编辑界面
	 */
	public function edit()
	{
		$this->assign("unique_random", unique_random());
		return $this->fetch("rich_text/design.html");
	}
}