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

use think\Cache;

/**
 * 自定义模板Model·外部公开
 * 创建时间：2018年7月24日11:20:44
 */
class DiyView
{
	
	/**
	 * 根据条件查询插件中的自定义模板数据
	 * 创建时间：2018年7月24日11:24:44
	 *
	 * @param array $condition
	 * @param string $filed
	 */
	public function getDiyViewAddonsInfo($condition, $filed = '*')
	{
		$res = model('nc_diy_view_temp')->getInfo($condition, $filed);
		return success($res);
	}
	
	/**
	 * 获取插件中的自定义模板集合
	 * 创建时间：2018年7月24日11:42:44
	 *
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 * @return multitype:string mixed
	 */
	public function getDiyViewAddonsList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$res = model('nc_diy_view_temp')->getList($condition, $field, $order, '', '', '', $limit);
		return success($res);
	}
	
	/**
	 * 获取自定义模板分页数据集合
	 * 创建时间：2018年7月24日14:41:08
	 *
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getSiteDiyViewPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = 'nsdv.*,ndva.addon_name as addon_name_temp')
	{
		$alias = "nsdv";
		$join = [
			[
				'nc_diy_view_temp ndva',
				'nsdv.name=ndva.name',
				'left'
			]
		];
		
		$res = model('nc_site_diy_view')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
		return success($res);
	}
	
	/**
	 * 获取自定义模板组件集合
	 * 创建时间：2018年7月24日16:04:26
	 *
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 * @return multitype:string mixed
	 */
	public function getDiyViewUtilList($condition = [], $field = '*', $order = 'sort asc', $limit = null)
	{
		$res = model('nc_diy_view_util')->getList($condition, $field, $order, '', '', '', $limit);
		return success($res);
	}
	
	/**
	 * 获取自定义模板链接集合
	 * 创建时间：2018年7月24日16:07:36
	 *
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 * @return multitype:string mixed
	 */
	public function getDiyLinkList($condition = [], $field = 'ncl.id,ncl.addon_name,nca.title as addon_title,ncl.name,ncl.title,ncl.design_url,ncl.h5_url,ncl.web_url,ncl.weapp_url,ncl.aliapp_url,ncl.baiduapp_url,ncl.type,ncl.icon,nca.icon as addon_icon', $order = 'nca.id desc', $alias = 'ncl', $join = [ [ 'nc_addon nca', 'ncl.addon_name=nca.name', 'left' ] ], $group = '', $limit = null)
	{
		$res = model('nc_link')->getList($condition, $field, $order, $alias, $join, $group, $limit);
		return success($res);
	}
	
	/**
	 * 获取自定义链接分页集合
	 * @param int $page
	 * @param int $page_size
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @return \multitype
	 */
	public function getDiyLinkPageList($condition = [], $field = 'ncl.id,ncl.addon_name,ncl.name,ncl.title,ncl.design_url,ncl.h5_url,ncl.web_url,ncl.weapp_url,ncl.aliapp_url,ncl.baiduapp_url,ncl.type,nca.title as addon_title', $order = 'ncl.id desc', $limit = null)
	{
		$alias = 'ncl';
		$join = [
			[
				'nc_addon nca',
				'ncl.addon_name=nca.name',
				'left'
			]
		];
//		$group = "group by ncl";
		$res = model('nc_link')->getList($condition, $field, $order, $alias, $join, '', $limit);
		return success($res);
	}
	
	/**
	 * 根据条件查询自定义模板
	 * 创建时间：2018年7月24日16:15:12
	 *
	 * @param array $condition
	 * @param string $filed
	 * @return multitype:string mixed
	 */
	public function getSiteDiyViewInfo($condition, $filed = '*')
	{
		$res = model('nc_site_diy_view')->getInfo($condition, $filed);
		
		return success($res);
	}
	
	/**
	 * 获取自定义模板详细信息
	 */
	public function getSiteDiyViewDetail($condition = [])
	{
		$alias = 'nsdv';
		$join = [
			[
				'nc_diy_view_temp ndva',
				'nsdv.name=ndva.name',
				'left'
			]
		];
		$field = 'nsdv.id,nsdv.site_id,nsdv.name,nsdv.title,nsdv.value,nsdv.type,nsdv.create_time,nsdv.update_time,nsdv.show_type,ndva.addon_name,nsdv.show_type';
		
		$info = model('nc_site_diy_view')->getInfo($condition, $field, $alias, $join);
		return success($info);
	}
	
	/**
	 * 获取站点的自定义模块
	 * @param unknown $site_id
	 */
	public function getSiteDiyViewModule($site_id)
	{
		$site = new Site();
		$site_info = $site->getSiteInfo([ 'site_id' => $site_id ]);
		$addon_name_array = model('nc_site_diy_view')->query("SELECT addon_name FROM nc_diy_view_temp GROUP BY addon_name");
		$addon_module = [];
		foreach ($addon_name_array as $k => $v) {
			
			if (strpos($site_info['data']['addon_modules'] . ',', $v['addon_name'] . ',') !== false) {
				$addon_module[] = $v['addon_name'];
			}
		}
		return $addon_module;
	}
	
	/**
	 * 获取插件的自定义模块
	 * @param unknown $site_id
	 */
	public function getSiteDiyViewTempList($site_id, $show_type = 'H5')
	{
		$cache = Cache::tag("site_" . $site_id)->get("getSiteDiyViewTempList" . $site_id . '_' . $show_type);
		if (!empty($cache)) {
			return $cache;
		}
		$site = new Site();
		$site_info = $site->getSiteInfo([ 'site_id' => $site_id ]);
		$addon_mudule_array = $this->getSiteDiyViewModule($site_id);
		//排除当前站点应用中的自定义模板
		foreach ($addon_mudule_array as $k => $v) {
			if ($v == $site_info['data']['addon_app']) {
				array_splice($addon_mudule_array, $k, 1);
			}
		}
		$addon_modules = implode(",", $addon_mudule_array);
		$diyview_addons = model("nc_diy_view_temp")->getList([ 'addon_name' => [ 'in', $addon_modules ], 'type' => $show_type ]);
		$module_array = [];
		$addon = new Addon();
		foreach ($addon_mudule_array as $k_module => $v_module) {
			$addon_info = $addon->getAddonInfo([ 'name' => $v_module ]);
			$module_array[ $k_module ]['addon_info'] = $addon_info['data'];
			
			$module_array[ $k_module ]['view_list'] = [];
			foreach ($diyview_addons as $k => $v) {
				
				if ($v['addon_name'] == $v_module) {
					$module_array[ $k_module ]['view_list'][] = $v;
				}
			}
		}
		Cache::tag("site_" . $site_id)->set("getSiteDiyViewTempList" . $site_id . '_' . $show_type, $module_array);
		return $module_array;
		
	}
	
	/**
	 * 添加自定义模板
	 * 创建时间：2018年7月24日16:21:58
	 *
	 * @param array $data
	 */
	public function addSiteDiyView($data)
	{
		$res = model('nc_site_diy_view')->add($data);
		if ($res) {
			return success($res);
		} else {
			return error($res);
		}
	}
	
	/**
	 * 添加多条自定义模板数据
	 * @param $data
	 * @return \multitype
	 */
	public function addSiteDiyViewList($data)
	{
		$res = model('nc_site_diy_view')->addList($data);
		if ($res) {
			return success($res);
		} else {
			return error($res);
		}
	}
	
	/**
	 * 修改自定义模板
	 * 创建时间：2018年7月24日16:23:39
	 *
	 * @param array $data
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function editSiteDiyView($data, $condition)
	{
		$res = model('nc_site_diy_view')->update($data, $condition);
		if ($res) {
			return success($res);
		} else {
			return error($res);
		}
	}
	
	/**
	 * 删除站点微页面
	 *
	 * @param array $condition
	 */
	public function deleteSiteDiyView($condition = [])
	{
		$res = model('nc_site_diy_view')->delete($condition);
		if ($res) {
			return success($res);
		} else {
			return error($res);
		}
	}
	
	/**
	 * 获取当前站点支持的插件列表
	 * 创建时间：2018年9月1日15:38:12
	 *
	 * @param $site_info
	 * @return array
	 */
	public function getDiyViewSupportAddonsList($site_info, $type)
	{
		//预置插件
		$addons_info = model("nc_addon")->getInfo([ 'name' => $site_info['addon_app'] ], 'name,preset_addon, icon');
		if (!empty($addons_info)) {
			
			//查询当前app支持的插件
			$addons_list = model("nc_addon")->getList([ 'support_addon' => [ 'like', '%' . $addons_info['name'] . '%' ] ]);
			$app_array = array();
			array_push($app_array, $addons_info['name']);
			
			foreach ($addons_list as $k => $v) {
				array_push($app_array, $v['name']);
			}
			
			//用户购买的插件
//			$user_addons_list = model("nc_user_addon")->getList([ 'status' => 1, 'uid' => $site_info['uid'] ]);
			
			$new_app_array = array();
			$new_app_array = array_merge($app_array, $new_app_array);
//			if (!empty($user_addons_list)) {
//				foreach ($user_addons_list as $k => $v) {
//					if (in_array($v['addon_name'], $app_array)) {
//						$new_app_array[] = $v['addon_name'];
//					}
//				}
//			}
			
			$str = implode(",", $new_app_array);
			$str .= "," . $addons_info['preset_addon'];
			$diy_view_addons_condition = [
				'ndva.addon_name' => [ 'in', 'System,' . $str ],
				'ndva.type' => $type
			];
			
			$alias = 'ndva';
			$join = [
				[
					'nc_addon na',
					'ndva.addon_name = na.name',
					'left'
				]
			];
			
			$res = model('nc_diy_view_temp')->getList($diy_view_addons_condition, 'ndva.addon_name,ndva.name,ndva.title,na.title as addon_title,na.description,ndva.icon,ndva.create_time', 'ndva.id asc', $alias, $join);
			
		}
		
		return success($res);
	}
	
}