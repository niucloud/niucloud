<?php

namespace addon\system\DiyView\sitehome\controller;

use addon\system\DiyView\common\model\Navigation as navModel;
use app\common\model\DiyView;

class Navigation extends BaseView
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 导航管理
	 * @return \think\mixed
	 */
	public function index()
	{
		return $this->fetch('navigation/navigation_list', [], $this->replace);
	}
	
	public function lists()
	{
		if (IS_AJAX) {
			$page = input("page", 1);
			$is_show = input('is_show', 'all');
			$type = input('type', 'all');
			$page_size = input('limit', PAGE_LIST_ROWS);
			$search_text = input('title', '');
			
			$condition = [
				'site_id' => SITE_ID,
				'type' => 2
			];
			
			if ($is_show != "all") {
				$condition["is_show"] = $is_show;
			}
			
			if ($type != "all") {
				$condition["type"] = $type;
			}
			
			if ($search_text != "") {
				$condition["title"] = [ 'like', '%' . $search_text . '%' ];
			}
			$nav = new navModel();
			$list = $nav->getPageList($condition, $page, $page_size, 'sort desc', '*');
			return $list;
		}
	}
	
	/**
	 * 添加
	 */
	public function add()
	{
		$nav = new navModel();
		if (IS_AJAX) {
			$data = input('data', '');
			$data = json_decode($data, true);
			$data['site_id'] = SITE_ID;
			$res = $nav->add($data);
			return $res;
		}
		$model = new DiyView();
		$link = $model->getDiyLinkList([ "ncl.addon_name" => $this->siteInfo["addon_app"] ]);
		$this->assign('link', $link);
		
		return $this->fetch('navigation/navigation_add');
		
	}
	
	/**
	 * 修改
	 */
	public function edit()
	{
		$id = input('id', '');
		$nav = new navModel();
		if (IS_AJAX) {
			$data = input('data', '');
			$data = json_decode($data, true);
			$id = $data['id'];
			unset($data['id']);
			$res = $nav->edit($data, $id);
			return $res;
		}
		$info = $nav->getInfo($id);
		$this->assign('info', $info['data']);
		$model = new DiyView();
		$link = $model->getDiyLinkList([ "ncl.addon_name" => $this->siteInfo["addon_app"] ]);
		$this->assign('link', $link);
		return $this->fetch('navigation/navigation_edit');
	}
	
	/**
	 * 删除
	 */
	public function del()
	{
		if (IS_AJAX) {
			$id = input('id', '');
			if ($id == '') {
				return error();
			}
			$nav = new navModel();
			$res = $nav->delete([ 'id' => [ 'in', $id ], 'site_id' => SITE_ID ]);
			return $res;
		}
	}
	
	/**
	 * 修改排序
	 */
	public function navSort()
	{
		if (IS_AJAX) {
			$id = input('id', '');
			$sort = input('sort', '0');
			if ($id == '') {
				return [];
			}
			$nav = new navModel();
			$res = $nav->edit([ 'sort' => $sort ], $id);
			return $res;
		}
	}
	
}