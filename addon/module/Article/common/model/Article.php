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

namespace addon\module\Article\common\model;

use think\Db;

/**
 * 文章管理
 * @author Administrator
 *
 */
class Article
{
	/**
	 * 添加文章
	 * @param array $data
	 */
	public function addArticle($data)
	{
		$article_id = model('nc_article')->add($data);
		return success($article_id);
	}
	
	/**
	 * 修改文章
	 * @param array $data
	 */
	public function editArticle($data)
	{
		$res = model('nc_article')->update($data, [ 'article_id' => $data['article_id'] ]);
		return success($res);
	}
	
	/**
	 * 获取文章详情
	 * @param int $discount_id
	 * @return multitype:string mixed
	 */
	public function getArticleInfo($condition = [], $field = true, $alias = 'nca', $join = null, $data = null)
	{
		$res = model('nc_article')->getInfo($condition, $field, $alias, $join, $data);
		return success($res);
	}
	
	/**
	 * 获取文章分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getArticlePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$list = model('nc_article')->pageList($condition, $field, $order, $page, $page_size);
		foreach ($list['list'] as $k => $v) {
			$categoryInfo = $this->getArticleCategoryInfo([ 'category_id' => $v['category_id'] ]);
			$list['list'][ $k ]['category_name'] = $categoryInfo['data']['category_name'];
		}
		return success($list);
	}
	
	/**
	 * 删除文章
	 * @param unknown $coupon_type_id
	 */
	public function deleteArticle($condition)
	{
		$res = model('nc_article')->delete($condition);
		return success($res);
	}
	
	/**
	 * 添加文章文类
	 * @param array $data
	 */
	public function addArticleCategory($data)
	{
		$model = model('nc_article_category');
		$res = $model->add($data);
		if ($res) {
			return success($res);
		} else {
			return error($res);
		}
	}
	
	/**
	 * 修改文章分类
	 * @param array $data
	 * @param array $condition
	 */
	public function editArticleCategory($data)
	{
		$condition = array(
			"site_id" => $data["site_id"],
			"category_id" => $data["category_id"],
		);
		$model = model('nc_article_category');
		$res = $model->update($data, $condition);
		if ($res) {
			return success($res);
		} else {
			return error($res);
		}
	}
	
	/**
	 * 获取文章分类详情
	 * @param array $condition
	 * @param string $field
	 */
	public function getArticleCategoryInfo($condition, $field = '*')
	{
		$model = model('nc_article_category');
		$res = $model->getInfo($condition);
		return success($res);
	}
	
	/**
	 * 获取文章分类树
	 *
	 * @param int $site_id
	 * @return multitype:string mixed
	 */
	public function getArticleCategoryTree($site_id)
	{
		$list = model('nc_article_category')->getList([ 'site_id' => $site_id ]);
		$tree = list_to_tree($list, 'category_id', 'p_id', 'child_list');
		return success($tree);
	}
	
	/**
	 * 获取文章分类分页列表
	 * @param array $condition
	 * @param number $page
	 * @param number $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getArticleCategoryPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$model = model('nc_article_category');
		$res = $model->pageList($condition, $field, $order, $page, $page_size);
		return success($res);
	}
	
	/**
	 * 获取文章分类列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param number $limit
	 */
	public function getArticleCategoryList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$model = model('nc_article_category');
		$res = $model->getList($condition, $field, $order, $alias = 'a', $join = [], $group = '', $limit);
		return success($res);
	}
	
	/**
	 * 获取分类统计
	 * @param array $conditionde
	 */
	public function getArticleCategoryCount($condition)
	{
		$model = model('nc_article_category');
		$res = $model->getCount($condition);
		return success($res);
	}
	
	/**
	 * 删除文章分类
	 * @param array $condition
	 */
	public function deleteArticleCategory($condition)
	{
		$model = model('nc_article_category');
		$res = $model->delete($condition);
		if ($res) {
			return success($res);
		} else {
			return error($res);
		}
	}
	
	/**
	 * 获取分类下的文章列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getArticlePageListByCaregory($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		if ($condition['category_id'] > 0) {
			$article_category_array = model('nc_article_category')->getList([ "category_id|p_id" => $condition['category_id'] ]);
			$new_article_category_array = array();
			foreach ($article_category_array as $v) {
				$new_article_category_array[] = $v["category_id"];
			}
			$condition["category_id"] = array( "in", $new_article_category_array );
		}
		$list = model('nc_article')->pageList($condition, $field, $order, $page, $page_size);
		foreach ($list['list'] as $k => $v) {
			$categoryInfo = $this->getArticleCategoryInfo([ 'category_id' => $v['category_id'] ]);
			$list['list'][ $k ]['category_name'] = $categoryInfo['data']['category_name'];
		}
		return success($list);
	}
	
	/**
	 * 获取文章列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param number $limit
	 */
	public function getArticleList($condition = [], $field = '*', $order = '', $limit = null, $alias = 'a', $join = [], $group = '')
	{
		$model = model('nc_article');
		$res = $model->getList($condition, $field, $order, $alias, $join, $group, $limit);
		return success($res);
	}
	
	/**
	 * 增加点击量
	 * @param unknown $condition
	 */
	public function gainArticleClicks($condition)
	{
		$retval = Db::name('nc_article')->where($condition)->setInc('click', 1);
		return success($retval);
	}
	
	/**
	 * 增加点击量
	 * @param unknown $condition
	 */
	public function gainArticleCommentCount($condition)
	{
		$retval = Db::name('nc_article')->where($condition)->setInc('comment_count', 1);
		return success($retval);
	}
	/**
	 * 删除站点
	 * @param unknown $site_id
	 */
	public function deleteSite($site_id)
	{
	    model('nc_article')->delete(['site_id' => $site_id]);
	    model('nc_article_category')->delete(['site_id' => $site_id]);
	    model('nc_article_comment')->delete(['site_id' => $site_id]);
	    model('nc_article_reward_list')->delete(['site_id' => $site_id]);
	    return success();
	}

    /**
     * 获取文章数量
     * @param $condition
     */
	public function getArticleCount($condition){
        $count = model('nc_article')->getCount($condition);
        return success($count);
    }
}