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
use think\db;

/**
 * 权限
 * 权限管理相关，包括用户组，菜单，站点菜单相关设置与缓存
 * group，menu， site_menu
 */
class Auth
{
	/*****************************************用户组管理开始******************************************************************************/
	/**
	 * 获取管理组列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 * @return multitype:string mixed
	 */
	public function getGroupList($condition = [], $field = '*', $order = '', $limit = null)
	{
		
		$site_id = isset($condition['site_id']) ? $condition['site_id'] : '';
		if ($site_id === '') {
			return error('', '缺少必须参数site_id');
		}
		$data = json_encode([ $condition, $field, $order, $limit ]);
		$cache = Cache::tag("group_" . $site_id)->get("getGroupList_" . $site_id . '_' . $data);
		if (!empty($cache)) {
			return success($cache);
		}
		$list = model('nc_group')->getList($condition, $field, $order, '', '', '', $limit);
		Cache::tag("group_" . $site_id)->set("getGroupList_" . $site_id . '_' . $data, $list);
		return success($list);
	}
	
	/**
	 * 获取管理组分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getGroupPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		
		$site_id = isset($condition['site_id']) ? $condition['site_id'] : '';
		if ($site_id === '') {
			return error('', '缺少必须参数site_id');
		}
		$data = json_encode([ $condition, $page, $page_size, $order, $field ]);
		$cache = Cache::tag("group_" . $site_id)->get("getGroupPageList_" . $site_id . '_' . $data);
		if (!empty($cache)) {
			return success($cache);
		}
		$list = model('nc_group')->pageList($condition, $field, $order, $page, $page_size);
		Cache::tag("group_" . $site_id)->set("getGroupPageList_" . $site_id . '_' . $data, $list);
		return success($list);
	}
	
	/**
	 * 添加用户组
	 * @param array $data
	 * @return multitype:string mixed
	 */
	public function addGroup($data)
	{
		
		$site_id = isset($data['site_id']) ? $data['site_id'] : '';
		if ($site_id === '') {
			return error('', '缺少必须参数site_id');
		}
		Cache::clear("group_" . $site_id);
		//预留验证
		$res = model('nc_group')->add($data);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 修改用户组
	 * @param array $data
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function editGroup($data, $condition)
	{
		
		$site_id = isset($condition['site_id']) ? $condition['site_id'] : '';
		if ($site_id === '') {
			return error('', '缺少必须参数site_id');
		}
		//判断默认管理员不可编辑(默认增加is_system = 0)
        $condition["is_system"] = 0;
		Cache::clear("group_" . $site_id);
		$res = model('nc_group')->update($data, $condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
		
	}
	
	/**
	 * 获取用户组详情
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function getGroupInfo($condition)
	{
		$site_id = isset($condition['site_id']) ? $condition['site_id'] : '';
		if ($site_id === '') {
			return error('', '缺少必须参数site_id');
		}
		$data = json_encode([ $condition, 1 ]);
		$cache = Cache::tag("group_" . $site_id)->get("getGroupInfo_" . $site_id . '_' . $data);
		if (!empty($cache)) {
			return success($cache);
		}
		$info = model('nc_group')->getInfo($condition);
		Cache::tag("group_" . $site_id)->set("getGroupInfo_" . $site_id . '_' . $data, $info);
		return success($info);
	}
	
	/**
	 * 删除用户组
	 * @param array $group_id
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function deleteGroup($condition)
	{
		
		$site_id = isset($condition['site_id']) ? $condition['site_id'] : '';
		if ($site_id === '') {
			return error('', '缺少必须参数site_id');
		}
		Cache::clear("group_" . $site_id);
		$res = model('nc_group')->delete($condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 修改用户组状态
	 * @param array $data
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function modifyGroupStatus($data, $condition)
	{
		
		$site_id = isset($condition['site_id']) ? $condition['site_id'] : '';
		if ($site_id === '') {
			return error('', '缺少必须参数site_id');
		}
		Cache::clear("group_" . $site_id);
		$res = model('nc_group')->update($data, $condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 清理站点用户组缓存
	 * @param unknown $site_id
	 */
	public function clearCroup($site_id)
	{
		Cache::clear("group_" . $site_id);
	}
	
	/*****************************************用户组管理结束****************************************************************************/
	/***************************************** 系统菜单开始*****************************************************************************/
	/**
	 * 获取菜单列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getMenuList($condition = [], $field = '*', $order = '', $limit = null)
	{
		
		$data = json_encode([ $condition, $field, $order, $limit ]);
		$cache = Cache::tag("menu")->get("getMenuList_" . $data);
		if (!empty($cache)) {
			return success($cache);
		}
		$list = model('nc_menu')->getList($condition, $field, $order, '', '', '', $limit);
		Cache::tag("menu")->set("getMenuList_" . $data, $list);
		
		return success($list);
	}
	
	/**
	 * 获取菜单树
	 * @param number $level 层级 0不限层级
	 */
	public function menuTree($level = 0, $menu_type = 1)
	{
		$condition = [];
		if ($level > 0) {
			$condition = [
				'level' => [ 'elt', $level ]
			];
		}
		$list = $this->getMenuList($condition, '*', 'sort asc');
		$tree = list_to_tree($list['data'], 'menu_id', 'menu_pid', 'child_list');
		return success($tree);
	}
	
	/**
	 * 通过主键获取菜单信息
	 * @param unknown $menu_id
	 * @return multitype:string mixed
	 */
	public function getMenuInfo($condition, $field = '*')
	{
		
		$data = json_encode([ $condition, $field ]);
		$cache = Cache::tag("menu")->get("getMenuInfo_" . $data);
		if (!empty($cache)) {
			return success($cache);
		}
		$menu_info = model('nc_menu')->getInfo($condition);
		Cache::tag("menu")->set("getMenuInfo_" . $data, $menu_info);
		return success($menu_info);
	}
	
	/**
	 * 获取菜单信息通过url路径
	 * @param string $url
	 * @return multitype:string mixed
	 */
	public function getMenuInfoByUrl($url)
	{
		
		$cache = Cache::tag("menu")->get("getMenuInfoByUrl_" . $url);
		if (!empty($cache)) {
			return success($cache);
		}
		$list = model('nc_menu')->getList([ 'url' => $url ], '*', 'level desc', 1);
		if (empty($list)) {
			return error();
		}
		
		$info = $list[0];
		Cache::tag("menu")->set("getMenuInfoByUrl_" . $url, $info);
		return success($info);
	}
	/***************************************** 系统菜单结束******************************************************************************/
	/***************************************** 站点菜单开始******************************************************************************/
	/**
	 * 获取站点菜单列表
	 * @param unknown $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getSiteMenuList($condition = [], $field = '*', $order = '', $limit = null)
	{
		
		$site_id = isset($condition['site_id']) ? $condition['site_id'] : '';
		if ($site_id === '') {
			return error('', '缺少必须参数site_id');
		}
		$data = json_encode([ $condition, $field, $order, $limit ]);
		$cache = Cache::tag("site_menu_" . $site_id)->get("getSiteMenuList_" . $site_id . '_' . $data);
		if (!empty($cache)) {
			return success($cache);
		}
		$list = model('nc_site_menu')->getList($condition, $field, $order, '', '', '', $limit);
		Cache::tag("site_menu_" . $site_id)->set("getSiteMenuList_" . $site_id . '_' . $data, $list);
		return success($list);
	}
	
	/**
	 * 修改菜单
	 * @param unknown $menu_id
	 * @param unknown $value
	 * @param unknown $type parentLevel:上级 sort:排序 title:菜单名称 is_menu:是否菜单
	 */
	public function editSiteMenu($menu_id, $site_id, $data)
	{
		Cache::clear("site_menu_" . $site_id);
		if (!empty($data['menu_pid'])) {
			$parent_menu_info = model('nc_site_menu')->getInfo([ 'name' => $data['menu_pid'], 'site_id' => $site_id ], 'level');
			$data['level'] = $parent_menu_info['level'] + 1;
		} else {
			$data['level'] = 1;
		}
		$res = model('nc_site_menu')->update($data, [ 'menu_id' => $menu_id, 'site_id' => $site_id ]);
		return success($res);
	}
	
	/**
	 * 修改菜单基础信息
	 * @param unknown $menu_id
	 * @param unknown $type
	 * @param unknown $value
	 */
	public function editMenuBaseInfo($menu_id, $type, $value, $site_id)
	{
		
		Cache::clear("site_menu_" . $site_id);
		if ($type == 'sort') {
			$data = [
				'sort' => $value
			];
		}
		$res = model('nc_site_menu')->update($data, [ 'menu_id' => $menu_id, 'site_id' => $site_id ]);
		return success($res);
	}
	
	
	/**
	 * 获取站点菜单信息
	 *
	 * @param array $condition
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getSiteMenuInfo($condition, $field = '*')
	{
		$site_id = isset($condition['site_id']) ? $condition['site_id'] : '';
		if ($site_id === '') {
			return error('', '缺少必须参数site_id');
		}
		$data = json_encode([ $condition, $field ]);
		$cache = Cache::tag("site_menu_" . $site_id)->get("getSiteMenuInfo_" . $site_id . '_' . $data);
		if (!empty($cache)) {
			return success($cache);
		}
		$info = model('nc_site_menu')->getInfo($condition, $field);
		Cache::tag("site_menu_" . $site_id)->set("getSiteMenuInfo_" . $site_id . '_' . $data, $info);
		return success($info);
	}
	
	/**
	 * 获取一条站点菜单信息
	 * @param $condition
	 * @param string $field
	 * @param string $order
	 * @return \multitype
	 */
	public function getFirstSiteMenu($condition, $field = '*', $order = "")
	{
		$site_id = isset($condition['site_id']) ? $condition['site_id'] : '';
		if ($site_id === '') {
			return error('', '缺少必须参数site_id');
		}
		$data = json_encode([ $condition, $field ]);
		$cache = Cache::tag("site_menu_" . $site_id)->get("getFirstDataSiteMenuInfo" . $site_id . '_' . $data);
		if (!empty($cache)) {
			return success($cache);
		}
		$info = model('nc_site_menu')->getFirstData($condition, $field, $order);
		Cache::tag("site_menu_" . $site_id)->set("getFirstDataSiteMenuInfo" . $site_id . '_' . $data, $info);
		return success($info);
	}
	
	/**
	 * 清理站点菜单缓存
	 * @param unknown $site_id
	 */
	public function clearSiteMenu($site_id)
	{
		Cache::clear("site_menu_" . $site_id);
	}
	
	/**
	 * 清理站点权限缓存
	 * @param int $site_id
	 */
	public function clearSiteAuth($site_id)
	{
		Cache::clear("site_menu_" . $site_id);
		Cache::clear("group_" . $site_id);
	}
	/***************************************** 站点菜单结束******************************************************************************/
	/********************************************************权限管理*******************************************************************/
	/**
	 *
	 * @param int $site_id //站点id
	 * @param $group_info //权限组信息
	 * @param $url //当前访问的url_module
	 */
	public function checkAuth($site_id, $group_info, $url)
	{
		if ($group_info['is_system'] == 1) {
			return true;
		}
		if ($site_id == 0) {
			$menu_info = $this->getMenuInfo([ 'url' => $url ]);
		} else {
			$menu_info = $this->getSiteMenuInfo([ 'site_id' => $site_id, 'url' => $url ], 'name');
		}

		if (!empty($menu_info['data'])) {
			//权限组
			if (empty($group_info)) {
				return false;
			}

			if (strpos(',' . $group_info['array'] . ',', ',' . $menu_info['data']['name'] . ',') !== false) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
		
		
	}

    /**
     * 验证应用权限
     * @param $addon_name
     * @param $group_info
     */
	public function checkModuleAuth($addon_name, $group_info, $field = "addon_array"){
	    if($group_info["is_system"] == 1){
            return true;
        }
        $temp_array = !empty($group_info[$field]) ? explode(",", $group_info[$field]) : [];
        if(in_array($addon_name, $temp_array)){
            return true;
        }else{
            return false;
        }
    }
	
}
