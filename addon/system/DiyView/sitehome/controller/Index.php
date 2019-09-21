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

namespace addon\system\DiyView\sitehome\controller;


use app\common\model\DiyView;

class Index extends BaseView
{
	
	/**
	 * 编辑自定义模板
	 * 创建时间：2018年7月24日16:27:03
	 */
	public function editDiyView()
	{
		$diy_view = new DiyView();
		$res = 0;
		$data = array();
		$id = input("post.id", 0);
		$name = input("post.name", "");
		$title = input("title", "");
		$show_type = input("show_type", "H5");//检查
		$value = input("value", "");
		$addon_name = input("addon_name", '');
		if (!empty($name) && !empty($title) && !empty($value)) {
			$data['site_id'] = request()->siteid();
			$data['name'] = $name;
			$data['title'] = $title;
			$data['show_type'] = $show_type;
			$data['value'] = $value;
			$data['addon_name'] = $addon_name;
			if ($id == 0) {
				$data['create_time'] = time();
				$data['type'] = "DEFAULT";
				$res = $diy_view->addSiteDiyView($data);
			} else {
				$data['update_time'] = time();
				$res = $diy_view->editSiteDiyView($data, [
					'id' => $id
				]);
			}
		}
		
		return $res;
	}
}