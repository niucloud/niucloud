<?php

namespace app\api\controller;

use app\common\model\Help as HelpModel;
use app\common\controller\BaseApi;

class Help extends BaseApi
{
	
	public $help_article_model;
	
	public function __construct($params)
	{
		parent::__construct($params);
		$this->help_article_model = new HelpModel();
	}
	
	/**
	 * 帮助中心列表
	 * @return \think\mixed
	 */
	public function helpList($params)
	{
		$order = isset($params['order']) ? $params['order'] : 'sort desc';
		return $this->help_article_model->getHelpArticleCategoryList([ 'site_id' => $params['site_id'] ], '', $order);
	}
	
	/**
	 * 帮助详情
	 * @param array $params
	 */
	public function detail($params)
	{
		return $this->help_article_model->getHelpArticleInfo($params['help_id']);
	}
	
	public function helpCategoryTreeList($params)
	{
		$page = isset($params['page']) ? $params['page'] : 1;
		$page_size = isset($params['page_size']) ? $params['page_size'] : PAGE_LIST_ROWS;
		$order = isset($params['order']) ? $params['order'] : 'sort asc';
		$category_list = $this->help_article_model->getHelpArticleCategoryList([ 'site_id' => $params['site_id'] ], '', $order);
		foreach ($category_list['data'] as $k => $v) {
			$list = $this->help_article_model->getHelpArticlePageListByCaregory([ 'class_id' => $v['class_id'], 'site_id' => $params['site_id'] ], $page, $page_size, $order);
			$category_list['data'][ $k ]['help_list'] = $list['data']['list'];
		}
		return $category_list;
	}
	
}