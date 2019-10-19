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

namespace addon\module\Article\wap\controller;

use app\common\controller\BaseSite;

/**
 * 文章 控制器
 * 创建时间：2018年8月31日16:55:48
 */
class Article extends BaseSite
{
	/**
	 * 文章 列表
	 * 创建时间：2018年8月31日17:22:45
	 */
	public function index()
	{
		return $this->getDiyView([ 'name' => 'NC_ARTICLE_H5_LIST', 'data' => [] ]);
	}
	
	/**
	 * 文章 详情
	 */
	public function detail()
	{
		$article_id = input('article_id', 0);
		return $this->getDiyView([ 'name' => 'NC_ARTICLE_H5_DETAIL', 'data' => [ 'article_id' => $article_id ] ]);
	}
	
}