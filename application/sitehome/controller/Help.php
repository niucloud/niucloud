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
namespace app\sitehome\controller;

use app\common\controller\BaseSiteHome;
use app\common\model\Help as HelpModel;

class Help extends BaseSiteHome
{
	
	public $help_article_model;
	protected $replace = [];
	
	public function __construct()
	{
		parent::__construct();
		$this->replace = [];
		$this->help_article_model = new HelpModel();
	}
	
	/**
	 * 帮助中心
	 * @return \think\mixed
	 */
	public function index()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$condition['site_id'] = $this->siteId;
			$order = 'sort asc';
			$list = $this->help_article_model->getHelpArticlePageList($condition, $page, $limit, $order);
			foreach ($list['data']['list'] as $k => $v) {
				$class_info = $this->help_article_model->getHelpArticleCategoryInfo([ 'class_id' => $v['class_id'] ]);
				$list['data']['list'][ $k ]['class_name'] = $class_info['data']['class_name'];
			}
			return $list;
		}
		return $this->fetch('help/help_article_list', [], $this->replace);
	}
	
	/**
	 * 添加帮助文章
	 * @return \think\mixed
	 */
	public function addHelpArticle()
	{
		if (IS_AJAX) {
			$class_id = input('class_id', "");
			$title = input('title', "");
			$content = input('content', "");
			$sort = input('sort', "");
			
			$data = array(
				'site_id' => $this->siteId,
				'title' => $title,
				'class_id' => $class_id,
				'content' => $content,
				'sort' => $sort,
			
			);
			$res = $this->help_article_model->addHelpArticle($data);
			return $res;
		}
		$list_tree = $this->help_article_model->getHelpArticleCategoryList([ 'site_id' => $this->siteId ]);
		$this->assign('list_tree', $list_tree['data']);
		
		return $this->fetch('help/add_help_article', [], $this->replace);
	}
	
	/**
	 * 修改帮助文章
	 * @return array|mixed
	 */
	public function editHelpArticle()
	{
		if (IS_AJAX) {
			$id = input('id', "");
			$class_id = input('class_id', "");
			$title = input('title', "");
			$content = input('content', "");
			$sort = input('sort', "");
			$data = array(
				'site_id' => $this->siteId,
				'title' => $title,
				'class_id' => $class_id,
				'content' => $content,
				'sort' => $sort,
				'id' => $id,
			);
			$res = $this->help_article_model->editHelpArticle($data);
			return $res;
		}
		$id = input('id', "");
		$help_info = $this->help_article_model->getHelpArticleInfo($id);
		$this->assign('help_info', $help_info['data']);
		
		$list_tree = $this->help_article_model->getHelpArticleCategoryList([ 'site_id' => $this->siteId ]);
		$this->assign('list_tree', $list_tree['data']);
		return $this->fetch('help/edit_help_article', [], $this->replace);
	}
	
	
	/**
	 * 删除帮助文章
	 */
	public function deleteHelpArticle()
	{
		if (IS_AJAX) {
			$id = input('id', "");
			$condition = array(
				'id' => $id
			);
			$res = $this->help_article_model->deleteHelpArticle($condition);
			return $res;
		}
	}
	
	/**
	 * 修改帮助排序
	 */
	public function sortHelpArticle()
	{
		if (IS_AJAX) {
			$id = input('id', "");
			$sort = input('sort', "");
			$data = array(
				'id' => $id,
				'sort' => $sort
			);
			$res = $this->help_article_model->editHelpArticle($data);
			return $res;
		}
	}
	
	/**
	 * 帮助类型
	 * @return mixed
	 */
	public function helpCategory()
	{
		if (IS_AJAX) {
			
			$site_id = $this->siteId;
			$condition['site_id'] = $site_id;
			$list = $this->help_article_model->getHelpArticleCategoryList($condition, '*', 'sort asc');
			foreach ($list['data'] as $key => $val) {
				$where['site_id'] = $site_id;
				$where['class_id'] = $val['class_id'];
				$list['data'][ $key ]['child_num'] = $this->help_article_model->getHelpArticleCategoryCount($where)['data'];
			}
			
			$res['code'] = $list['code'];
			$res['message'] = $list['message'];
			$res['data'] = [
				'count' => !empty($list['data']) ? count($list['data']) : 0,
				'list' => $list['data']
			];
			return $res;
		} else {
			return $this->fetch('help/category_list', [], $this->replace);
		}
	}
	
	/**
	 * 添加帮助分类
	 */
	public function helpCategoryAdd()
	{
		if (IS_AJAX) {
			$class_name = input("class_name", '');
			$icon = input("icon", '');
			$sort = input("sort", 0);
			
			$data = [
				'class_name' => $class_name,
				'icon' => $icon,
				'sort' => $sort,
				'site_id' => $this->siteId
			];
			$res = $this->help_article_model->addHelpArticleCategory($data);
			return $res;
		}
	}
	
	/**
	 * 修改帮助分类
	 */
	public function helpCategoryEdit()
	{
		if (IS_AJAX) {
			$class_name = input("class_name", '');
			$icon = input("icon", '');
			$sort = input("sort", 0);
			$class_id = input("class_id", 0);
			$data = [
				'class_name' => $class_name,
				'icon' => $icon,
				'sort' => $sort,
				'site_id' => $this->siteId
			];
			$res = $this->help_article_model->editHelpArticleCategory($data, [ 'class_id' => $class_id ]);
			return $res;
		}
	}
	
	/**
	 * 帮助文章分类删除
	 */
	public function helpCategoryDelete()
	{
		
		$class_ids = input('class_ids', '');
		$condition['class_id'] = [ 'in', $class_ids ];
		
		$res = $this->help_article_model->deleteHelpArticleCategory($condition);
		return $res;
	}
	
	/**
	 * 帮助文章分类详情
	 */
	public function helpCategoryInfo()
	{
		
		$class_id = input('class_id', 0);
		$condition['class_id'] = $class_id;
		$res = $this->help_article_model->getHelpArticleCategoryInfo($condition);
		
		return $res;
	}
	
	/**
	 * 修改排序
	 */
	public function sortHelpArticleCategory()
	{
		if (IS_AJAX) {
			$class_id = input('class_id', "");
			$sort = input('sort', "");
			$res = $this->help_article_model->editHelpArticleCategory([ 'sort' => $sort ], [ 'class_id' => $class_id ]);
			return $res;
		}
	}
	
}