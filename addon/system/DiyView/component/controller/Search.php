<?php

namespace addon\system\DiyView\component\controller;

use app\common\controller\BaseDiyView;

/**
 * 顶部搜索·组件
 * 创建时间：2018年9月19日17:20:38
 */
class Search extends BaseDiyView
{
	
	/**
	 * 前台输出
	 */
	public function parseHtml($attr)
	{
		return $this->fetch('search/search.html');
	}
	
	/**
	 * 后台编辑界面
	 */
	public function edit()
	{
		return $this->fetch("search/design.html");
	}
}