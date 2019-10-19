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
namespace addon\system\DiyView\api\controller;

use app\common\controller\BaseApi;
use app\common\model\DiyView;

/**
 * 自定义模板控制器
 * 创建时间：2018年9月11日18:36:32
 */
class Diy extends BaseApi
{
	
	public function getDiyView($params)
	{
		$data = [];
		if (!empty($params['data'])) {
			$data = json_decode($params['data'], true);
		}
		return hook("diyFetch", [ 'name' => $params['name'],'addon_name' => $params['addon_name'], 'data' => $data ], [], true);
	}
	
	/**
	 * 获取自定义模板数据(JSON格式)
	 * @param $params
	 * @return false|string
	 */
	public function getDiyViewData($params)
	{
		$diy_view = new DiyView();
		$diy_view_info = array();
		if (!empty($params)) {
			if (!empty($params['id'])) {
				$diy_view_info = $diy_view->getSiteDiyViewDetail([
					'nsdv.site_id' => request()->siteid(),
					'id' => $params['id']
				]);
			} elseif (!empty($param['addon_name']) && !empty($params['name'])) {
				$diy_view_info = $diy_view->getSiteDiyViewDetail([
					'nsdv.name' => $params['name'],
					'nsdv.addon_name' => $param['addon_name'],
					'site_id' => request()->siteid(),
				]);
			} elseif (!empty($params['name'])) {
				$diy_view_info = $diy_view->getSiteDiyViewDetail([
					'nsdv.name' => $params['name'],
					'site_id' => request()->siteid(),
				]);
			}
		}
		return json_encode($diy_view_info, JSON_UNESCAPED_UNICODE);
	}
}