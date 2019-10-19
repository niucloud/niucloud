<?php

namespace addon\module\Article\component\controller;

use app\common\controller\BaseDiyView;

/**
 * 文章赞赏·组件
 * 创建时间：2018年8月9日17:35:23 星期四
 *
 */
class ArticleReward extends BaseDiyView
{
	
	/**
	 * 前台输出
	 */
	public function parseHtml($attr)
	{
		return $this->fetch('article_reward/article_reward.html');
	}
	
	/**
	 * 后台编辑界面
	 */
	public function edit()
	{
		return $this->fetch("article_reward/design.html");
	}
}