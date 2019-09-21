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
namespace addon\module\Article\api\controller;

use addon\module\Article\common\model\Article as ArticleModel;
use app\common\controller\BaseApi;

/**
 * 控制器
 */
class Article extends BaseApi
{
	
	/**
	 * 文章分类
	 * @param array $params
	 */
	public function categoryList($params)
	{
		$article = new ArticleModel();
		$condition = json_decode($params['condition'], true);
		$condition['site_id'] = $params['site_id'];
		if (!$condition['p_id']) {
			$condition['p_id'] = 0;
		}
		$field = isset($params['field']) ? $params['field'] : '*';
		$order = isset($params['order']) ? $params['order'] : '';
		$limit = isset($params['limit']) ? $params['limit'] : '';
		$list = $article->getArticleCategoryList($condition, $field, $order, $limit);
		return $list;
	}
	
	/**
	 * 分类下文章列表
	 * @param array $params
	 */
	public function getArticlePageList($params)
	{
		$article = new ArticleModel();
		$condition = json_decode($params['condition'], true);
		$condition['site_id'] = $params['site_id'];
		$page = isset($params['page']) ? $params['page'] : '1';
		$page_size = isset($params['page_size']) ? $params['page_size'] : PAGE_LIST_ROWS;
		$order = isset($params['order']) ? $params['order'] : '';
		$list = $article->getArticlePageListByCaregory($condition, $page, $page_size, $order);
		return $list;
	}
	
	/**
	 * 分类下文章详情
	 * @param array $params
	 */
	public function getArticleDetail($params)
	{
		if (!isset($params['article_id'])) {
			return error('request parameter article_id');
		}
		if (empty($params['article_id'])) {
			return error('request parameter article_id');
		}
		$article = new ArticleModel();
		$field = 'nca.*, ncac.p_id';
		$join = [
			[
				'nc_article_category ncac',
				'ncac.category_id = nca.category_id',
				'left'
			]
		];
		$condition['article_id'] = $params['article_id'];
		$article_detail = $article->getArticleInfo($condition, $field, 'nca', $join);
		return $article_detail;
	}
	
	/**
	 * 文章分类树
	 * @param array $params
	 */
	public function getCategoryTree($params)
	{
		$article = new ArticleModel();
		$site_id = $params['site_id'];
		$list = $article->getArticleCategoryTree($site_id);
		return $list;
	}
	
	/**
	 * 分类下文章列表
	 * @param array $params
	 */
	public function getArticleList($params)
	{
		$article = new ArticleModel();
		$condition = json_decode($params['condition'], true);
		$condition['site_id'] = $params['site_id'];
		$order = isset($params['order']) ? $params['order'] : '';
		$list = $article->getArticlePageListByCaregory($condition, 1, 0, $order);
		return $list;
	}
	
	/**
	 * 分类下文章详情
	 * @param array $params
	 */
	public function articleCategoryInfo($params = [])
	{
		$article = new ArticleModel();
		$condition = json_decode($params['condition'], true);
		$field = isset($params['field']) ? $params['field'] : '*';
		$condition['site_id'] = $params['site_id'];
		$list = $article->getArticleCategoryInfo($condition, $field);
		return $list;
	}
	
	/**
	 * 获取文章列表
	 */
	public function articleList($params = [])
	{
		$article = new ArticleModel();
		$condition = json_decode($params['condition'], true);
		$field = 'nca.*, ncac.category_name';
		$join = [
			[
				'nc_article_category ncac',
				'nca.category_id = ncac.category_id',
				'left'
			]
		];
		$condition['nca.site_id'] = $params['site_id'];
		$limit = isset($params['limit']) ? $params['limit'] : '';
		$article_list = $article->getArticleList($condition, $field, '', $limit, 'nca', $join);
		return $article_list;
	}
	
	/**
	 * 增加点击量
	 * @param array $params
	 */
	public function gainClicks($params = [])
	{
		$article = new ArticleModel();
		$condition = array(
			"site_id" => $params["site_id"],
			"article_id" => $params["article_id"]
		);
		$result = $article->gainArticleClicks($condition);
		return $result;
	}
	
	/**
	 * 获取文章分类
	 * @param array $params
	 */
	public function getArticleCategoryList($params = [])
	{
		$article = new ArticleModel();
		$list = $article->getArticleCategoryList([ "site_id" => $params["site_id"] ], "*", "sort asc");
		return $list;
	}
	
}