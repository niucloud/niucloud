<?php

namespace addon\module\Article\component\controller;

use app\common\controller\BaseDiyView;

class ArticleList extends BaseDiyView
{
	
	/**
	 * 前台输出
	 */
	public function parseHtml($attr)
	{
		$this->assign("unique_random", unique_random());
		return $this->fetch('article_list/article_list.html');
	}
	
	/**
	 * 后台编辑界面
	 */
	public function edit()
	{
		return $this->fetch("article_list/design.html");
	}
}