<?php

namespace addon\system\DiyView\sitehome\controller;

use addon\system\DiyView\common\model\Advertisement as AdvModel;


class Advertisement extends BaseView
{
	
	public $adv_model;
	
	public function __construct()
	{
		parent::__construct();
		$this->adv_model = new AdvModel();
	}
	
	/**
	 * 广告中心
	 * @return \think\mixed
	 */
	public function index()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$condition = [
				'site_id' => $this->siteId,
				'adv_type' => 2
			];
			$order = 'sort asc';
			$list = $this->adv_model->getAdvPageList($condition, $page, $limit, $order);
			return $list;
		}
		return $this->fetch('advertisement/adv_list', [], $this->replace);
	}
	
	/**
	 * 编辑广告
	 */
	public function edit()
	{
		$adv_id = input('adv_id', '');
		if (IS_AJAX) {
			$adv_title = input("adv_title", "");
			$adv_display = input("adv_display", "");
			$adv_layout = input("adv_layout", "");
			$sort = input("sort", "");
			$adv_json = input("adv_json", "");
			$is_show = input("is_show", 0);
			$adv_width = input("adv_width", "");
			$adv_height = input("adv_height", "");
			$keywords = input("keywords", "");
			$data = [
				'site_id' => $this->siteId,
				'adv_title' => $adv_title,
				'adv_type' => 2,
				'adv_display' => $adv_display,
				'adv_width' => $adv_width,
				'adv_height' => $adv_height,
				'adv_layout' => $adv_layout,
				'sort' => $sort,
				'adv_json' => $adv_json,
				'is_show' => $is_show,
			];
			
			if ($adv_id > 0) {
				$data['modify_time'] = time();
				$res = $this->adv_model->editAdv($data, $adv_id);
			} else {
				$data['keywords'] = $keywords;
				$data['create_time'] = time();
				$res = $this->adv_model->addAdv($data);
			}
			return $res;
		}
		$info = $this->adv_model->getAdvInfo([ 'adv_id' => $adv_id ]);
		$this->assign('info', $info['data']);
		$this->assign('adv_id', $adv_id);
		return $this->fetch('advertisement/edit_adv', [], $this->replace);
	}
	
	/**
	 * 删除
	 */
	public function deleteAdv()
	{
		if (IS_AJAX) {
			$adv_id = input('adv_id', "");
			$condition = array( 'adv_id' => $adv_id );
			$res = $this->adv_model->deleteAdv($condition);
			return $res;
		}
	}
	
	/**
	 * 修改排序
	 */
	public function sortAdv()
	{
		if (IS_AJAX) {
			$id = input('adv_id', "");
			$sort = input('sort', "");
			$data = array(
				'sort' => $sort
			);
			$res = $this->adv_model->editAdv($data, $id);
			return $res;
		}
	}
	
	public function keywordsIsExist()
	{
		if (IS_AJAX) {
			$keywords = input('keywords', "");
			$condition = [
				'keywords' => $keywords,
				'site_id' => $this->siteId,
				'adv_type' => 2
			];
			$res = $this->adv_model->getAdvInfo($condition);
			return $res;
		}
	}
	
}