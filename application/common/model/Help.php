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

namespace app\common\model;


/**
 * 帮助中心管理
 * @author Administrator
 *
 */
class Help
{
	/**
	 * 添加帮助文章
	 * @param array $data
	 */
	public function addHelpArticle($data)
	{
		$help_id = model('nc_web_config_help_article')->add($data);
		return success($help_id);
	}
	
	/**
	 * 修改帮助文章
	 * @param array $data
	 */
	public function editHelpArticle($data)
	{
		$res = model('nc_web_config_help_article')->update($data, [ 'id' => $data['id'] ]);
		return success($res);
	}
	
	/**
	 * 获取帮助文章详情
	 * @param int $discount_id
	 * @return multitype:string mixed
	 */
	public function getHelpArticleInfo($held_id)
	{
		$res = model('nc_web_config_help_article')->getInfo([ 'id' => $held_id ]);
		return success($res);
	}
	
	/**
	 * 获取帮助文章分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getHelpArticlePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$list = model('nc_web_config_help_article')->pageList($condition, $field, $order, $page, $page_size);
		foreach ($list['list'] as $k => $v) {
			$categoryInfo = $this->getHelpArticleCategoryInfo([ 'class_id' => $v['class_id'] ]);
			$list['list'][ $k ]['class_name'] = $categoryInfo['data']['class_name'];
		}
		return success($list);
	}
	
	/**
	 * 删除文章
	 * @param unknown $coupon_type_id
	 */
	public function deleteHelpArticle($condition)
	{
		$res = model('nc_web_config_help_article')->delete($condition);
		return success($res);
	}
	
	/**
	 * 添加帮助文章分类
	 * @param array $data
	 */
	public function addHelpArticleCategory($data)
	{
		
		$model = model('nc_web_config_help_category');
		$res = $model->add($data);
		if ($res) {
			return success($res);
		} else {
			return error($res);
		}
	}
	
	/**
	 * 修改帮助文章分类
	 * @param array $data
	 * @param array $condition
	 */
	public function editHelpArticleCategory($data, $condition)
	{
		
		$model = model('nc_web_config_help_category');
		$res = $model->update($data, $condition);
		if ($res) {
			return success($res);
		} else {
			return error($res);
		}
	}
	
	/**
	 * 获取帮助文章分类详情
	 * @param array $condition
	 * @param string $field
	 */
	public function getHelpArticleCategoryInfo($condition, $field = '*')
	{
		
		$model = model('nc_web_config_help_category');
		$res = $model->getInfo($condition);
		return success($res);
	}
	
	
	/**
	 * 获取帮助文章分类分页列表
	 * @param array $condition
	 * @param number $page
	 * @param number $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getHelpArticleCategoryPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		
		$model = model('nc_web_config_help_category');
		$res = $model->pageList($condition, $field, $order, $page, $page_size);
		return success($res);
	}
	
	
	/**
	 * 获取帮助文章分类列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param number $limit
	 */
	public function getHelpArticleCategoryList($condition = [], $field = '*', $order = '', $limit = null)
	{
		
		$model = model('nc_web_config_help_category');
		$res = $model->getList($condition, $field, $order, $alias = 'a', $join = [], $group = '', $limit);
		return success($res);
	}
	
	/**
	 * 获取帮助分类统计
	 * @param array $condition
	 */
	public function getHelpArticleCategoryCount($condition)
	{
		
		$model = model('nc_web_config_help_category');
		$res = $model->getCount($condition);
		return success($res);
	}
	
	/**
	 * 删除帮助文章分类
	 * @param array $condition
	 */
	public function deleteHelpArticleCategory($condition)
	{
		
		$model = model('nc_web_config_help_category');
		$res = $model->delete($condition);
		if ($res) {
			return success($res);
		} else {
			return error($res);
		}
	}
	
	/**
	 * 获取帮助分类下的文章列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getHelpArticlePageListByCaregory($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		if ($condition['class_id'] > 0) {
			$article_category_array = model('nc_web_config_help_category')->getList([ "class_id" => $condition['class_id'] ]);
			
			$new_article_category_array = array();
			foreach ($article_category_array as $v) {
				$new_article_category_array[] = $v["class_id"];
			}
			$condition["class_id"] = array( "in", $new_article_category_array );
		}
		
		$list = model('nc_web_config_help_article')->pageList($condition, $field, $order, $page, $page_size);
		foreach ($list['list'] as $k => $v) {
			$categoryInfo = $this->getHelpArticleCategoryInfo([ 'class_id' => $v['class_id'] ]);
			$list['list'][ $k ]['class_name'] = $categoryInfo['data']['class_name'];
		}
		return success($list);
	}
	
}
