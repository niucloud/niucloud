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
use think\Db;
use util\file\Write;
use util\upgrade\Upgrade;

/**
 * 插件表
 */
class Addon
{
	
	protected $upgrade;
	
	protected $addon;
	
	protected $site_ids;
	
	/**************************初始化插件内容*******************************/
	/**
	 * 获取系统所有插件类,命名空间信息，用于系统初始化或者配置信息查询
	 */
	public function getAddons()
	{
		$cache = Cache::tag("Addon")->get("getAddons");
		if (!empty($cache)) {
			return $cache;
		}
		$addons = $this->getAddonList([], 'name,type');
		$data = [];
		$data['addons'] = [];
		$data['addon_class'] = [];
		$data['addon_class_path'] = [];
		$data['addon_path'] = [];
		foreach ($addons['data'] as $k => $v) {
			$name = strtolower($v['name']);
			$data['addons'][ $name ] = $v['name'];
			if ($v['type'] == 'ADDON_APP') {
				$data['addon_class'][ $v['name'] ] = "addon\\app\\" . $v['name'] . "\\" . $v['name'] . "Addon";
				$data['addon_class_path'][ $v['name'] ] = "addon\\app\\" . $v['name'] . "\\";
				$data['addon_path'][ $v['name'] ] = "addon/app/" . $v['name'] . "/";
			} elseif ($v['type'] == 'ADDON_SYSTEM') {
				$data['addon_class'][ $v['name'] ] = "addon\\system\\" . $v['name'] . "\\" . $v['name'] . "Addon";
				$data['addon_class_path'][ $v['name'] ] = "addon\\system\\" . $v['name'] . "\\";
				$data['addon_path'][ $v['name'] ] = "addon/system/" . $v['name'] . "/";
			} else {
				$data['addon_class'][ $v['name'] ] = "addon\\module\\" . $v['name'] . "\\" . $v['name'] . "Addon";
				$data['addon_class_path'][ $v['name'] ] = "addon\\module\\" . $v['name'] . "\\";
				$data['addon_path'][ $v['name'] ] = "addon/module/" . $v['name'] . "/";
			}
		}
		Cache::tag("Addon")->set("getAddons", $data);
		return $data;
	}
	
	/********************************************************************基础查询开始******************************************************/
	/**
	 * 根据条件查询当前未安装的插件或者应用列表
	 * @param string $type
	 * @param number $page
	 * @param number $limit
	 * @param string $addon_dir
	 * @return boolean|multitype:string mixed
	 */
	public function getAddonUninstallList($type = 'ADDON_APP', $page = 1, $limit = 15, $addon_dir = '')
	{
		if (!$addon_dir) {
			$addon_dir = $type == 'ADDON_APP' ? ADDON_APP_PATH : ADDON_MODULE_PATH;
		}
		$dirs = array_map('basename', glob($addon_dir . '*', GLOB_ONLYDIR));
		if ($dirs === false || !file_exists($addon_dir)) {
			$this->error = '插件目录不可读或者不存在';
			return false;
		}
		$addon_names = model('nc_addon')->getColumn([], 'name');
		$addons = [];
		foreach ($dirs as $key => $value) {
			if (!in_array($value, $addon_names)) {
				$class = get_addon_class($value);
				if (!class_exists($class)) { // 实例化插件失败忽略执行
					\Think\Log::record('插件' . $value . '的入口文件不存在！');
					continue;
				}
				$obj = new $class();
				$info = $obj->info;
				$info['status'] = -1;
				if ($info['type'] == $type) {
					$addons[] = $info;
				}
			}
		}
		$count = count($addons);
		$start = ($page - 1) * $limit; // 计算每次分页的开始位置
		$data = array_slice($addons, $start, $limit);
		return success([
			'count' => $count,
			'list' => $data
		]);
	}
	
	/**
	 * 获取后台插件列表（本地已安装+官网授权的）
	 */
	public function getAdminAddonList($page, $limit = 0, $where = [])
	{
		$addon_type = config("addon_type");
		$list = [];
		$item = [
			'title' => '',
			'icon' => '',
			'name' => '',
			'type' => '',
			'version' => '',
			'status' => '', // 1 已安装 0-未安装
			'install_time' => '',
			'validity_time' => '',
			'preset_addon' => '',
			'is_buy' => '',
			'is_install' => 0,
			'description' => ''
		];
		
		if ($where['install_type'] == '' || $where['install_type'] == 'installed') {
			
			$field = 'id,icon,name,title,version,type,create_time,preset_addon,description,support_app_type';
			// 本地已安装列表
			$condition = [];
			if (!empty($where['type'])) {
				$condition['type'] = $where['type'];
			}
			if (!empty($where['addon_name'])) {
				$condition['title'] = [ 'like', '%' . $where['addon_name'] . '%' ];
			}
			$local_install_list = model('nc_addon')->getList($condition, $field);
			
			foreach ($local_install_list as $k => $v) {
				$item['title'] = $v['title'];
				$item['icon'] = $v['icon'];
				$item['name'] = $v['name'];
				$item['type'] = $v['type'];
				$item['type_name'] = $addon_type[ $v['type'] ];
				$item['version'] = $v['version'];
				$item['status'] = 1;
				$item['install_time'] = time_to_date($v['create_time'], 'Y-m-d');
				$item['validity_time'] = '--';
				$item['preset_addon'] = $v['preset_addon'];
				$item['is_buy'] = 0;
				$item['is_install'] = 1;
				$item['description'] = $v['description'];
				$item['support_app_type'] = $v['support_app_type'];
				array_push($list, $item);
			}
		}
		
		if ($where['install_type'] == '' || $where['install_type'] == 'notinstalled') {
			
			// 本地未安装的列表
			$local_uninstall_list = $this->getAdminUninstallList([ 'type' => $where['type'] ]);
			
			foreach ($local_uninstall_list as $k => $v) {
				
				$path = "./addon/system/" . $v['name'] . "/icon.png";
				$icon = $path;
				if (!file_exists($icon)) {
					$icon = "./addon/module/" . $v['name'] . "/icon.png";
				}
				if (!file_exists($icon)) {
					$icon = "./addon/app/" . $v['name'] . "/icon.png";
				}
				
				$item['title'] = $v['title'];
				$item['icon'] = $icon;
				$item['name'] = $v['name'];
				$item['type'] = $v['type'];
				$item['type_name'] = $addon_type[ $v['type'] ];
				$item['version'] = $v['version'];
				$item['status'] = 0;
				$item['install_time'] = '--';
				$item['validity_time'] = '--';
				$item['preset_addon'] = isset($v['preset_addon']) ? $v['preset_addon'] : '';
				$item['is_buy'] = 0;
				$item['is_install'] = 0;
				$item['description'] = $v['description'];
				
				if ((!empty($where['addon_name']) && strpos($v['title'], $where['addon_name']) !== false) || empty($where['addon_name'])) {
					array_push($list, $item);
				}
			}
		}
		
		$name_sort = [];
		// 重新排序
		foreach ($list as $key => $v) {
			$name_sort[ $key ] = $v['name'];
		}
		
		array_multisort($name_sort, $list);
		// 计算总量并分页
		$count = count($list);
		if ($limit > 0) {
			$start = ($page - 1) * $limit; // 计算每次分页的开始位置
			$data = array_slice($list, $start, $limit);
		} else {
			$data = $list;
		}
		
		return success([
			'count' => $count,
			'list' => $data
		]);
	}
	
	/**
	 * 获取某插件下 预置插件的列表 递归
	 *
	 * @param unknown $new_array
	 * @param unknown $data
	 */
	protected function getPresetAddonList($new_array, $data, $level = 1)
	{
		if (isset($data['preset_addon']) && $data['preset_addon'] != '') {
			$items = explode(',', $data['preset_addon']);
			foreach ($items as $vi) {
				$item = $new_array[ $vi ];
				$item['parent_name'] = $data['name'];
				$item['level'] = $level;
				$preset_addon_list[] = $item;
				if (isset($item['preset_addon']) && $item['preset_addon'] != '') {
					$list = $this->getPresetAddonList($new_array, $item, $level + 1);
					$preset_addon_list = array_merge($preset_addon_list, $list);
				}
			}
		} else {
			$preset_addon_list = [];
		}
		return $preset_addon_list;
	}
	
	/**
	 * 获取插件列表
	 *
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getAddonList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$data = json_encode([ $condition, $field, $order, $limit ]);
		$cache = Cache::tag("addon")->get("getAddonList_" . $data);
		if (!empty($cache)) {
			return success($cache);
		}
		$addon_list = model('nc_addon')->getList($condition, $field, $order, '', '', '', $limit);
		Cache::tag("addon")->set("getAddonList_" . $data, $addon_list);
		return success($addon_list);
	}
	
	/**
	 * 获取单条插件信息
	 *
	 * @param array $condition
	 * @param string $field
	 */
	public function getAddonInfo($condition, $field = "*")
	{
		$data = json_encode([ $condition, $field ]);
		$cache = Cache::tag("addon")->get("getAddonInfo_" . $data);
		if (!empty($cache)) {
			return success($cache);
		}
		$addon_info = model('nc_addon')->getInfo($condition, $field);
		Cache::tag("addon")->set("getAddonInfo_" . $data, $addon_info);
		return success($addon_info);
	}
	
	/**
	 * 获取插件某列数据
	 *
	 * @param array $condition
	 * @param string $field
	 * @param string $key
	 * @return multitype:string mixed
	 */
	public function getAddonColumn($condition = [], $field = '', $key = '')
	{
		$data = json_encode([ $condition, $field, $key ]);
		$cache = Cache::tag("addon")->get("getAddonColumn_" . $data);
		if (!empty($cache)) {
			return success($cache);
		}
		$list = model('nc_addon')->getColumn($condition, $field, $key);
		Cache::tag("addon")->set("getAddonColumn_" . $data, $list);
		return success($list);
	}
	
	/**
	 * 获取插件分页列表
	 *
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getAddonPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$data = json_encode([ $condition, $page, $page_size, $order, $field ]);
		$cache = Cache::tag("addon")->get("getAddonPageList_" . $data);
		if (!empty($cache)) {
			return success($cache);
		}
		$list = model('nc_addon')->pageList($condition, $field, $order, $page, $page_size);
		Cache::tag("addon")->set("getAddonPageList_" . $data, $list);
		return success($list);
	}
	
	/**
	 * 获取插件列表，包括
	 *
	 * @return Ambigous <number, unknown>
	 */
	public function getUserAddonList($uid)
	{
		$sql = "SELECT na.name, na.icon, na.title, na.description,(SELECT nm.url FROM nc_menu nm WHERE nm.module = na.name LIMIT 1) AS url, na.price, na.try_time FROM nc_addon na WHERE na.type = 'ADDON_MODULE' AND na.visble = 1;";
		$addons_list = model('nc_addon')->query($sql);
		
		$user_addons = model('nc_user_addon')->getColumn([
			'uid' => $uid
		], 'status', 'addon_name');
		foreach ($addons_list as $k => $v) {
			// 状态为-1时，是没有购买
			$addons_list[ $k ]['status'] = array_key_exists($v['name'], $user_addons) ? $user_addons[ $v['name'] ] : -1;
		}
		
		return success($addons_list);
	}
	
	/**
	 * 获取站点插件列表
	 *
	 * @param int $site_id
	 */
	public function getSiteAddonList($site_id)
	{
		$site_info = model('nc_site')->getInfo([
			'site_id' => $site_id
		]);
		$site_module = explode(",", $site_info['addon_modules']);
		
		$site_module_str = implode(array_map(function ($var) {
			return "'" . $var . "'";
		}, $site_module), ',');
		
		$sql = "SELECT * FROM nc_addon na WHERE ((FIND_IN_SET('" . $site_info['addon_app'] . "', na.support_addon) AND na.type='ADDON_MODULE') OR na.type='ADDON_SYSTEM' OR na.name in ($site_module_str)) and visble = 1;";
		$addons_list = model('nc_addon')->query($sql);
		foreach ($addons_list as $k => $v) {
			
			// 判断是否站点已存在
			if (in_array($v["name"], $site_module)) {
				$addons_list[ $k ]['status_data'] = [
					'status' => 1,
					'title' => '点击进入'
				];
				$site_menu_info = model('nc_site_menu')->getInfo([
					'site_id' => $site_id,
					'port' => 'SITEHOME',
					'module' => $v['name']
				], 'url');
				$addons_list[ $k ]['redirect_url'] = !empty(addon_url($site_menu_info['url'])) ? addon_url($site_menu_info['url']) : "";
			} else {
				
				$addons_list[ $k ]['status_data'] = [
					'status' => 0
				];
				$addons_list[ $k ]['redirect_url'] = '#';
			}
		}
		return success($addons_list);
	}
	
	/**
	 * 获取站点业务模块列表
	 * @param $site_id
	 * @return \multitype
	 */
	public function getSiteAddonModuleList($site_id)
	{
		$site_info = model('nc_site')->getInfo([
			'site_id' => $site_id
		], "addon_app,addon_modules");
		
		$site_module = explode(",", $site_info['addon_modules']);
		
		$site_module_str = implode(array_map(function ($var) {
			return "'" . $var . "'";
		}, $site_module), ',');
		
		$sql = "SELECT name,icon,title,description,status FROM nc_addon na WHERE (na.type='ADDON_MODULE' and na.name in ($site_module_str) and visble = 1) or na.support_addon like '%" . $site_info['addon_app'] . "%';";
		$addons_list = model('nc_addon')->query($sql);
		
		if (empty($addons_list)) {
			return error([]);
		}
		
		//检测业务插件是否已安装
		foreach ($addons_list as $k => $v) {
			$addons_list[ $k ]['status'] = in_array($v['name'], $site_module) ? 1 : 0;
		}
		
		return success($addons_list);
	}
	
	/**
	 * 获取用户插件详情
	 *
	 * @param array $condition
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getUserAddonInfo($condition, $field = '*')
	{
		$info = model('nc_user_addon')->getInfo($condition, $field);
		return success($info);
	}
	
	public function getAddonCategoryList()
	{
		$list = model('nc_addon_category')->getList();
		return success($list);
	}
	
	/**
	 * 获取会员插件列表
	 *
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getUserAddonPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*', $alias = 'nua', $join = [])
	{
		$list = model('nc_user_addon')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
		return success($list);
	}
	
	/**
	 * 添加钩子
	 */
	public function addHooks($data)
	{
		$res = model('nc_hook')->add($data);
		if ($res == false) {
			return error();
		}
		Cache::tag("hook")->clear();
		return success($res);
	}
	
	/**
	 * 修改钩子
	 */
	public function updateHooks($data, $condition)
	{
		$res = model('nc_hook')->update($data, $condition);
		if ($res == false) {
			return error();
		}
		Cache::tag("hook")->clear();
		return success($res);
	}
	
	/**删除钩子
	 * @param $condition
	 */
	public function deleteHooks($condition)
	{
		$res = model('nc_hook')->delete($condition);
		if ($res == false) {
			return error();
		}
		Cache::tag("hook")->clear();
		return success($res);
	}
	
	/**
	 * 获取钩子分页列表
	 *
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getHookPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$data = json_encode([ $condition, $page, $page_size, $order, $field ]);
		$cache = Cache::tag("hook")->get("getHookPageList_" . $data);
		if (!empty($cache)) {
			return success($cache);
		}
		$list = model('nc_hook')->pageList($condition, $field, $order, $page, $page_size);
		Cache::tag("hook")->set("getHookPageList_" . $data, $list);
		return success($list);
	}
	
	/**
	 * 获取钩子列表
	 *
	 * @param array $condition
	 * @param number $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getHookList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$data = json_encode([ $condition, $field, $order, $limit ]);
		$cache = Cache::tag("hook")->get("getHookList_" . $data);
		if (!empty($cache)) {
			return success($cache);
		}
		$list = model('nc_hook')->getList($condition, $field, $order, '', '', '', $limit);
		Cache::tag("hook")->set("getHookList_" . $data, $list);
		return success($list);
	}
	
	/**
	 * 获取钩子某列数据
	 *
	 * @param array $condition
	 * @param string $field
	 * @param string $key
	 */
	public function getHookColumn($condition = [], $field = '', $key = '')
	{
		$data = json_encode([ $condition, $field, $key ]);
		$cache = Cache::tag("hook")->get("getHookColumn_" . $data);
		if (!empty($cache)) {
			return success($cache);
		}
		$list = model('nc_hook')->getColumn($condition, $field, $key);
		Cache::tag("hook")->set("getHookColumn_" . $data, $list);
		return success($list);
	}
	
	/**
	 * 获取钩子信息
	 *
	 * @param array $condition
	 * @param string $field
	 */
	public function getHookInfo($condition, $field = '*')
	{
		$data = json_encode([ $condition, $field ]);
		$cache = Cache::tag("hook")->get("getHookInfo_" . $data);
		if (!empty($cache)) {
			return success($cache);
		}
		$info = model('nc_hook')->getInfo($condition, $field);
		Cache::tag("hook")->set("getHookInfo_" . $data, $info);
		return success($info);
	}
	
	
	/********************************************************************基础查询结束******************************************************/
	
	/*******************************************************************内部方法开始*******************************************************/
	/**
	 * 查询所有未安装的插件或者应用
	 */
	private function getAdminUninstallList($condition = [])
	{
		$addon_app = ADDON_APP_PATH;
		$addon_module = ADDON_MODULE_PATH;
		$addon_system = ADDON_SYSTEM_PATH;
		if (empty($condition['type'])) {
			
			$app_dirs = array_map('basename', glob($addon_app . '*', GLOB_ONLYDIR));
			$module_dirs = array_map('basename', glob($addon_module . '*', GLOB_ONLYDIR));
			$system_dirs = array_map('basename', glob($addon_system . '*', GLOB_ONLYDIR));
			$dirs = array_merge($app_dirs, $module_dirs, $system_dirs);
		} elseif ($condition['type'] == 'ADDON_APP') {
			$dirs = array_map('basename', glob($addon_app . '*', GLOB_ONLYDIR));
		} elseif ($condition['type'] == 'ADDON_MODULE') {
			$dirs = array_map('basename', glob($addon_module . '*', GLOB_ONLYDIR));
		} elseif ($condition['type'] == 'ADDON_SYSTEM') {
			$dirs = array_map('basename', glob($addon_system . '*', GLOB_ONLYDIR));
		}
		
		if ($dirs === false || !file_exists($addon_app) || !file_exists($addon_module) || !file_exists($addon_system)) {
			$this->error = '插件目录不可读或者不存在';
			return false;
		}
		$addon_names = model('nc_addon')->getColumn([], 'name');
		
		$addons = [];
		foreach ($dirs as $key => $value) {
			if (!in_array($value, $addon_names)) {
				$class = get_addon_class($value);
				if (!class_exists($class)) { // 实例化插件失败忽略执行
					\Think\Log::record('插件' . $value . '的入口文件不存在！');
					continue;
				}
				$obj = new $class();
				$info = $obj->info;
				$addons[] = $info;
			}
		}
		return $addons;
	}
	
	/**
	 * 更新插件里的所有钩子对应的插件
	 */
	private function updateHooksAll($addons_name)
	{
		$public = [
			'__construct',
			'install',
			'uninstall',
			'addToSite',
			'delFromSite',
			'copyToSite',
			'backupSql',
			'coverSql',
			'recoverSql',
			'checkInfo',
			'initSiteDiyViewData'
		];
		$addons_class = get_addon_class($addons_name); // 获取插件名
		if (!class_exists($addons_class)) {
			return false;
		}
		$methods = get_class_methods($addons_class);
		$hooks = model('nc_hook')->getColumn([], 'name');
		$methods_hooks = array_diff($methods, $public);
		foreach ($methods_hooks as $hook) {
			if (in_array($hook, $hooks)) {
				$flag = $this->updateAddons($hook, array(
					$addons_name
				));
				if (false === $flag) {
					$this->removeHooks($addons_name);
					return false;
				}
			} else {
				$flag = model('nc_hook')->add([
					'name' => $hook,
					'update_time' => time(),
					'addons' => $addons_name
				]);
				if (false === $flag) {
					return false;
				}
			}
		}
		return true;
	}
	
	/**
	 * 更新单个钩子处的插件
	 */
	private function updateAddons($hook_name, $addons_name)
	{
		$o_addons = model('nc_hook')->getValue([
			'name' => $hook_name
		], 'addons');
		if ($o_addons) {
			$o_addons = explode(',', $o_addons);
		}
		if ($o_addons) {
			$addons = array_merge($o_addons, $addons_name);
			$addons = array_unique($addons);
		} else {
			$addons = $addons_name;
		}
		$flag = model('nc_hook')->setFieldValue([
			'name' => $hook_name
		], 'addons', implode(',', $addons));
		if (false === $flag) {
			model('nc_hook')->setFieldValue([
				'name' => $hook_name
			], 'addons', implode(',', $o_addons));
		}
		return $flag;
	}
	
	/**
	 * 去除插件所有钩子里对应的插件数据
	 */
	private function removeHooks($addons_name)
	{
		$addons_class = get_addon_class($addons_name);
		if (!class_exists($addons_class)) {
			return false;
		}
		$methods = get_class_methods($addons_class);
		$hooks = model('nc_hook')->getColumn([], 'name');
		$common = array_intersect($hooks, $methods);
		if ($common) {
			foreach ($common as $hook) {
				$flag = $this->removeAddons($hook, array(
					$addons_name
				));
				if (false === $flag) {
					return false;
				}
			}
		}
		return true;
	}
	
	/**
	 * 去除单个钩子里对应的插件数据
	 */
	private function removeAddons($hook_name, $addons_name)
	{
		$res = model('nc_hook')->delete([
			'addons' => $addons_name[0]
		]);
		if ($res === false) {
			return false;
		}
		$o_addons = model('nc_hook')->getValue([
			'name' => $hook_name
		], 'addons');
		$o_addons = explode(',', $o_addons);
		if ($o_addons) {
			$addons = array_diff($o_addons, $addons_name);
		} else {
			return true;
		}
		$flag = model('nc_hook')->setFieldValue([
			'name' => $hook_name
		], 'addons', implode(',', $addons));
		if (false === $flag) {
			model('nc_hook')->setFieldValue([
				'name' => $hook_name
			], 'addons', implode(',', $o_addons));
		}
		return $flag;
	}
	/*******************************************************************内部方法结束*******************************************************/
	
	/*******************************************************************插件安装方法开始****************************************************/
	/**
	 * 插件安装
	 *
	 * @param string $addon_name
	 */
	public function install($addon_name)
	{
		$addon_class = get_addon_class($addon_name);
		if (!class_exists($addon_class)) {
			return error('ADDON_NOT_EXIST');
		}
		$this->addon = new $addon_class();
		
		// 检测插件的正确性
		$res1 = $this->installBeforeCheck($addon_name);
		if ($res1['code'] != 0) {
			return $res1;
		}
		Db::startTrans();
		try {
			// 插件预安装
			$res2 = $this->preInstall();
			if ($res2['code'] != 0) {
				Db::rollback();
				return $res2;
			}
			
			// 安装菜单
			$res3 = $this->installMenu();
			
			if ($res3['code'] != 0) {
				Db::rollback();
				return $res3;
			}
			
			// 安装自定义模板
			$res4 = $this->installDiyView();
			if ($res4['code'] != 0) {
				Db::rollback();
				return $res4;
			}
			$res5 = $this->installMsgTpl();
			if ($res5['code'] != 0) {
				Db::rollback();
				return $res5;
			}
			// 添加插件入表
			$addons_model = model('nc_addon');
			$addon_info = $this->addon->info;
			if (!isset($addon_info['config'])) {
				$addon_info['config'] = json_encode($addon_info);
				$addon_info['create_time'] = time();
			}
			if ($addon_info['type'] == 'ADDON_APP') {
				$addon_type_dir_name = 'app';
			} elseif ($addon_info['type'] == 'ADDON_SYSTEM') {
				$addon_type_dir_name = 'system';
			} else {
				$addon_type_dir_name = 'module';
			}
			$addon_info['icon'] = '/addon/' . $addon_type_dir_name . '/' . $addon_name . '/icon.png';
			
			//针对插件分类处理
			$addon_category = isset($addon_info['category']) ? $addon_info['category'] : 'OTHER';
			$addon_categorys = $this->getAddonCategoryName();
			if (strpos($addon_categorys, $addon_category) === false) {
				//新插件处理
				$category_info = $this->addon->config['addon_category'];
				if (!empty($category_info)) {
					model("nc_addon_category")->add($category_info);
				}
				
			}
			
			$data = $addons_model->add($addon_info);
			
			if (!$data) {
				Db::rollback();
				return error($data, 'ADDON_ADD_CATEGORY_FAIL', []);
			}
			// 插件绑定到钩子上
			$hooks_update = $this->updateHooksAll($addon_name);
			if (!$hooks_update) {
				Db::rollback();
				return error($hooks_update, 'ADDON_UPDATE_HOOK_FAIL');
			}
			// 给admin用户添加永久的期限 插件安装上的时候默认给admin用户权限
			//暂时没有用到这个表，2019年8月29日10:09:34
//			model('nc_user_addon')->add([
//				'uid' => 1,
//				'addon_name' => $addon_name,
//				'status' => 1,
//				'create_time' => time(),
//				'validity_time' => 0
//			]);
			// 清理缓存
			Cache::clear();
			
			Db::commit();
			return success();
		} catch (\Exception $e) {
			// 清理缓存
			Cache::clear();
			Db::rollback();
			return error('', 'ADDON_INSTALL_FAIL', [
				$e->getMessage()
			]);
		}
	}
	
	/**
	 * 安装插件前检测
	 */
	private function installBeforeCheck($addon_name)
	{
		$info = $this->addon->info;
		if (!$info || !$this->addon->checkInfo()) { // 检测信息的正确性
			return error('', 'ADDON_INFO_ERROR');
		}
		// 检测预置插件是否已经安装
		if (isset($info['preset_addon']) && $info['preset_addon']) {
			$preset_addon = $info['preset_addon'];
			$installed_addon = model('nc_addon')->getColumn([
				'name' => [
					'in',
					$preset_addon
				]
			], 'name');
			$diff = array_diff(explode(',', $preset_addon), $installed_addon);
			if (!empty($diff)) {
				foreach ($diff as $k => $v) {
					$result = $this->install($v);
					if ($result['code'] != 0) {
						return error($v, 'PRESET_ADDON_NOT_INSTALL', [
							$v
						]);
					}
				}
				$addon_class = get_addon_class($addon_name);
				if (!class_exists($addon_class)) {
					return error('ADDON_NOT_EXIST');
				}
				$this->addon = new $addon_class();
			}
		}
		return success();
	}
	
	/**
	 * 插件预安装
	 */
	private function preInstall()
	{
		$res = $this->addon->install();
		if ($res['code'] != 0) {
			return error('', 'ADDON_PRE_INSTALL_ERROR', [
				$res['message']
			]);
		}
		return success();
	}
	
	/**
	 * 安装插件菜单
	 */
	private function installMenu()
	{
		if (!isset($this->addon->config['menu']) && !isset($this->addon->config['admin_menu'])) {
			return success();
		}
		$menu_home_array = isset($this->addon->config['menu']) ? $this->addon->config['menu'] : [];
		$menu_admin_array = isset($this->addon->config['admin_menu']) ? $this->addon->config['admin_menu'] : [];
		// 菜单树转成 列表
		$menu_home_list = $this->getAddonMenuList($menu_home_array, 'SITEHOME');
		$menu_admin_list = $this->getAddonMenuList($menu_admin_array, 'ADMIN');
		$data = array_merge($menu_home_list, $menu_admin_list);
		if (empty($data)) {
			return success();
		}
		// 拿到所有的name
		$name_list = array_column($data, 'name');
		// 判断表中有没有已经存在的name
		$menu_model = model('nc_menu');
		$result = model('nc_menu')->addList($data);
		if (!$result) {
			$menu_model->delete([
				'module' => $this->addon->info['name']
			]);
			return error($result, 'ADDON_INSTALL_MENU_FAIL', [
				$menu_model->getError()
			]);
		}
		return success();
	}
	
	/**
	 * 安装自定义页面
	 */
	private function installDiyView()
	{
		if (!isset($this->addon->config['diy'])) {
			return success();
		}
		$diy_view = $this->addon->config['diy'];
		
		// 自定义模板
		if (isset($diy_view['view'])) {
			$diy_view_addons_data = [];
			foreach ($diy_view['view'] as $k => $v) {
				$addons_item = [
					'addon_name' => isset($v['addon_name']) ? $v['addon_name'] : $this->addon->info['name'],
					'name' => $v['name'],
					'title' => $v['title'],
					'value' => $v['value'],
					'type' => $v['type'],
					'icon' => $v['icon'],
					'create_time' => time()
				];
				$diy_view_addons_data[] = $addons_item;
			}
			if ($diy_view_addons_data) {
				model('nc_diy_view_temp')->addList($diy_view_addons_data);
			}
		}
		// 自定义链接
		if (isset($diy_view['link'])) {
			$diy_view_link_data = [];
			foreach ($diy_view['link'] as $k => $v) {
				$link_item = [
					'addon_name' => isset($v['addon_name']) ? $v['addon_name'] : $this->addon->info['name'],
					'name' => $v['name'],
					'title' => $v['title'],
					'design_url' => isset($v['design_url']) ? $v['design_url'] : '',
					'web_url' => isset($v['web_url']) ? $v['web_url'] : '',
					'h5_url' => isset($v['h5_url']) ? $v['h5_url'] : '',
					'weapp_url' => isset($v['weapp_url']) ? $v['weapp_url'] : '',
					'aliapp_url' => isset($v['aliapp_url']) ? $v['aliapp_url'] : '',
					'baiduapp_url' => isset($v['baiduapp_url']) ? $v['baiduapp_url'] : '',
					'icon' => isset($v['icon']) ? $v['icon'] : '',
				];
				$diy_view_link_data[] = $link_item;
			}
			if ($diy_view_link_data) {
				model('nc_link')->addList($diy_view_link_data);
			}
		}
		// 自定义模板组件
		if (isset($diy_view['util'])) {
			$diy_view_util_data = [];
			foreach ($diy_view['util'] as $k => $v) {
				$util_item = [
					'name' => $v['name'],
					'title' => $v['title'],
					'type' => $v['type'],
					'controller' => $v['controller'],
					'value' => $v['value'],
					'sort' => $v['sort'],
					'support_diy_view' => $v['support_diy_view'],
					'addon_name' => $this->addon->info['name'],
					'max_count' => $v['max_count']
				];
				$diy_view_util_data[] = $util_item;
			}
			if ($diy_view_util_data) {
				model('nc_diy_view_util')->addList($diy_view_util_data);
			}
		}
		return success();
	}
	
	/**
	 * 安装消息模板
	 *
	 * @return multitype:string mixed
	 */
	private function installMsgTpl()
	{
		if (!isset($this->addon->config['message_template'])) {
			return success();
		}
		$msg_tpl = $this->addon->config['message_template'];
		$msg_tpl_data = [];
		foreach ($msg_tpl as $k => $v) {
			$item = [
				'addon' => $this->addon->info['name'],
				'var_json' => $v['var_json'],
				'title' => $v['title'],
				'keyword' => $v['keyword'],
				'port' => $v['port'],
				'wechat_json' => $v['wechat_json']
			];
			$msg_tpl_data[] = $item;
		}
		if ($msg_tpl_data) {
			model('nc_message_type')->addList($msg_tpl_data);
		}
		return success();
	}
	
	/**
	 * 添加插件菜单
	 *
	 * @param array $tree
	 * @param number $pid
	 */
	private function addonMenuAdd($module, $tree, $pid = '', $level = 1)
	{
		if (is_array($tree)) {
			foreach ($tree as $key => $value) {
				$value['menu_pid'] = $pid;
				$value['level'] = $level;
				$value['module'] = $module;
				$value['is_menu'] = isset($value['is_menu']) ? $value['is_menu'] : 1;
				$reffer = $value;
				if (isset($reffer['child_list'])) {
					unset($reffer['child_list']);
					$id = model('nc_menu')->add($reffer);
					$p_name = $reffer['name'];
					if ($id) {
						$this->addonMenuAdd($module, $value['child_list'], $p_name, $level + 1);
					} else {
						break;
					}
				} else {
					$id = model('nc_menu')->add($reffer);
				}
			}
		}
		return true;
	}
	/**************************************************************插件安装结束*********************************************************/
	
	/**************************************************************插件卸载开始*********************************************************/
	public function uninstall($addon_name)
	{
		$addon_class = get_addon_class($addon_name);
		
		if (!class_exists($addon_class)) {
			return error('ADDON_NOT_EXIST' . $addon_class);
		}
		$this->addon = new $addon_class();
		$res = $this->uninstallBeforeCheck($addon_name);
		if ($res['code'] != 0) {
			return $res;
		}
		Db::startTrans();
		try {
			// 插件预卸载
			$res1 = $this->preUninstall($this->addon);
			if ($res1['code'] != 0) {
				Db::rollback();
				return $res1;
			}
			// 卸载菜单
			$res2 = $this->uninstallMenu($addon_name);
			if ($res2['code'] != 0) {
				Db::rollback();
				return $res2;
			}
			$res3 = $this->uninstallDiyView($addon_name);
			if ($res3['code'] != 0) {
				Db::rollback();
				return $res3;
			}
			$res4 = $this->uninstallMsgTpl($addon_name);
			if ($res4['code'] != 0) {
				Db::rollback();
				return $res4;
			}
			
			// 卸载钩子及插件
			$hooks_update = $this->removeHooks($addon_name);
			if ($hooks_update === false) {
				// $this->error = '卸载插件所挂载的钩子数据失败';
				Db::rollback();
				return error();
			}
			$delete_res = model('nc_addon')->delete([
				'name' => $addon_name
			]);
			if ($delete_res === false) {
				// $this->error = '卸载插件失败';
				Db::rollback();
				return error();
			}
			//清理缓存
			Cache::clear();
			Db::commit();
			return success();
		} catch (\Exception $e) {
			//清理缓存
			Cache::clear();
			Db::rollback();
			return error($e->getMessage());
		}
	}
	
	/**
	 * 卸载插件前检测
	 */
	private function uninstallBeforeCheck($addon_name)
	{
		//检测是否存在预置插件
		$pre_addon = model('nc_addon')->query("SELECT name,title FROM nc_addon na WHERE FIND_IN_SET('" . $addon_name . "', na.preset_addon) > 0");
		if (!empty($pre_addon)) {
			return error('', '[' . $pre_addon[0]['title'] . ']插件属于预装载插件，请卸载对应插件后再卸载当前插件!');
		}

		//检测站点中是否存在该插件的应用
		$count = model("nc_site")->getCount([ 'addon_app' => $addon_name ]);
		$class_name = get_addon_class($addon_name);
		$addon_info = new $class_name();
		if ($count > 0) {
			return error('', '发现' . $count . '个[' . $addon_info->info['title'] . ']站点，请删除站点后再卸载插件!');
		}
		return success();
	}
	
	/**
	 * 插件预卸载
	 */
	private function preUninstall()
	{
		session('addon_uninstall_error', null);
		$uninstall_flag = $this->addon->uninstall();
		if ($uninstall_flag['code'] != 0) {
			return $uninstall_flag;
		}
		return success();
	}
	
	/**
	 * 卸载插件菜单
	 */
	private function uninstallMenu($addon_name)
	{
		$res1 = model('nc_site_menu')->delete([
			'module' => $addon_name
		]);
		$res2 = model('nc_menu')->delete([
			'module' => $addon_name
		]);
		if ($res1 === false || $res2 === false) {
			return error('', 'ADDON_UNINSTALL_MENU_FAIL');
		}
		return success();
	}
	
	/**
	 * 卸载自定义模板
	 *
	 * @param string $addon_name
	 * @return multitype:string mixed
	 */
	private function uninstallDiyView($addon_name)
	{
		$temp_array = model('nc_diy_view_temp')->getColumn([ 'addon_name' => $addon_name ], 'name');
		model('nc_diy_view_temp')->delete([
			'addon_name' => $addon_name
		]);
		model('nc_link')->delete([
			'addon_name' => $addon_name
		]);
		model('nc_diy_view_util')->delete([
			'addon_name' => $addon_name
		]);
		if (!empty($temp_array)) {
			$temp_array_str = implode(',', $temp_array);
			model("nc_site_diy_view")->delete([
				'name' => [ 'in', $temp_array_str ]
			]);
		}
		
		return success();
	}
	
	/**
	 * 卸载 消息模板
	 *
	 * @param string $addon_name
	 * @return multitype:string mixed
	 */
	private function uninstallMsgTpl($addon_name)
	{
		model('nc_message_type')->delete([
			'addon' => $addon_name
		]);
		model('nc_site_message_type')->delete([
			'addon' => $addon_name
		]);
		return success();
	}
	/***************************************************************插件卸载结束********************************************************/
	
	/***************************************************************插件更新开始********************************************************/
	/**
	 * 更新插件
	 * @param unknown $addon_name
	 * @param unknown $version
	 */
	public function upgrade($addon_name, $version)
	{
		Cache::clear();
		Db::startTrans();
		try {
			$addon_info = model('nc_addon')->getInfo([
				'name' => $addon_name
			]);
			if ($addon_info['type'] == 'ADDON_MODULE') {
				$root_path = ADDON_MODULE_PATH;
			} else {
				$root_path = ADDON_APP_PATH;
			}
			$root_dir = $addon_name; // 源根目录
			$update_dir = 'update';
			$upgrade_dir = $addon_name . '/' . $update_dir . '/' . $version; // 升级根目录
			
			if (!file_exists($root_path . $upgrade_dir)) {
				return error('', '升级文件不存在');
			}
			
			// 检查权限
			$check_res = $this->checkPermission($root_path . $root_dir);
			if (!$check_res) {
				return $check_res;
			}
			
			$addon_class = get_addon_class($addon_name);
			if (!class_exists($addon_class)) {
				return error('ADDON_NOT_EXIST');
			}
			$this->addon = new $addon_class();
			$this->upgrade = new Upgrade();
			
			// 备份文件
			$update_path = $root_path . $root_dir . '/' . $update_dir . '/history/';
			$backup_file_res = $this->backupFile($root_path, $root_dir, $update_path);
			if ($backup_file_res['code'] != 0) {
				return $backup_file_res;
			}
			// 备份数据库
			$backup_sql_res = $this->addon->backupSql();
			if ($backup_sql_res['code'] != 0) {
				return $backup_sql_res;
			}
			// 覆盖原文件
			$from_path = $upgrade_dir . '/';
			$cover_file_res = $this->coverFile($root_path, $from_path, $root_dir);
			if ($cover_file_res['code'] != 0) {
				// 还原文件
				$history_dir = $root_dir . '/' . $update_dir . '/' . 'history/';
				$recover_file_res = $this->recoverFile($root_path, $history_dir, $root_dir);
				if ($recover_file_res['code'] != 0) {
					return $recover_file_res;
				}
				return $cover_file_res;
			}
			// 执行数据库升级
			$cover_sql_res = $this->addon->coverSql();
			if ($cover_sql_res['code'] != 0) {
				// 还原文件
				// 还原sql
				return error('升级数据库的时候出错了');
			}
			$pid = $addon_info['type'] == 'ADDON_MODULE' ? 'ADDON_ROOT' : '';
			$level = $addon_info['type'] == 'ADDON_MODULE' ? 2 : 1;
			$site_id_list = model('nc_site_menu')->getList([
				'module' => $addon_name
			], 'site_id', '', '', '', 'site_id');
			$this->site_ids = array_column($site_id_list, 'site_id');
			// $this->upgradeConfig();
			$update_res = $this->upgradeMenu($this->addon->config['menu'], $pid, $level);
			// 重写配置文件
			$path = ADDON_MODULE_PATH . 'NcTest/update.php';
			$config_path = $root_path . $addon_name . '/config.php';
			$write = new Write($config_path, $this->addon->config);
			$write_res = $write->create();
			if ($write_res === false) {
				return error('', '重载配置文件出错！');
			}
			Db::commit();
			return success();
		} catch (\Exception $e) {
			Db::rollback();
			return error($e->getMessage());
		}
	}
	
	/**
	 * 检测权限
	 */
	private function checkPermission($path)
	{
		if (!is_writable($path)) {
			return error('', '文件夹没有写入的权限');
		}
		return success();
	}
	
	/**
	 * 备份原文件
	 *
	 * @param string $root_path
	 *            根目录
	 * @param string $root_dir
	 *            需要备份的文件夹名称
	 * @param string $update_path
	 *            更新包的文件夹路径
	 */
	private function backupFile($root_path, $root_dir, $update_path)
	{
		$res = $this->upgrade->backup($root_path, $root_dir, $update_path);
		if (!$res) {
			return error($res, '备份文件出错');
		}
		return success($res);
	}
	
	/**
	 * 覆盖现有文件
	 * 将更新包中的文件 覆盖到 原文件中 更新包路径，原来的路径
	 */
	private function coverFile($root_path, $from_path, $to_dir)
	{
		$res = $this->upgrade->cover($root_path, $from_path, $to_dir);
		if (!$res) {
			return error($res, '覆盖文件出错');
		}
		return success($res);
	}
	
	/**
	 * 还原文件
	 *
	 * @param string $addon_name
	 * @param string $version
	 * @return boolean
	 */
	private function recoverFile($root_path, $from_dir, $to_dir)
	{
		$res = $this->upgrade->recover($root_path, $from_dir, $to_dir);
		if (!$res) {
			return error($res, '还原文件失败');
		}
		return success($res);
	}
	
	/**
	 * 升级菜单
	 */
	private function upgradeMenu($menu_array, $pid = '', $level = 1)
	{
		if (empty($menu_array)) {
			return success();
		}
		// 循环每一个菜单，然后根据配置中的菜单进行具体的修改 每一步操作需要有对菜单的修改，
		foreach ($menu_array as $item) {
			if (isset($item['action'])) {
				switch ($item['action']) {
					case 'ADD':
						
						// 处理系统菜单
						unset($item['action']);
						$data = [
							'name' => $item['name'],
							'title' => $item['title'],
							'menu_pid' => $pid,
							'url' => $item['url'],
							'level' => $level,
							'module' => $this->addon->info['name'],
							'is_menu' => isset($item['is_menu']) ? $item['is_menu'] : 1
						];
						model('nc_menu')->add($data);
						// 处理站点菜单 （添加站点菜单时，每个站点只要有这个插件的地方都需要添加, 站点的菜单如果是已经调整过了，需要实时查当前的等级）
						$site_data = [];
						foreach ($this->site_ids as $site_id) {
							$p_level = model('nc_site_menu')->getValue([
								'name' => $pid,
								'site_id' => $site_id
							], 'level');
							$site_item = $data;
							$site_item['site_id'] = $site_id;
							$site_item['level'] = $p_level + 1;
							$site_data[] = $site_item;
						}
						model('nc_site_menu')->addList($site_data);
						break;
					case 'EDIT':
						
						// 处理系统菜单
						$data = [
							'title' => $item['title'],
							'url' => $item['url']
						];
						if (isset($item['is_menu'])) {
							$data['is_menu'] = $item['is_menu'];
						}
						$condition = [
							'name' => $item['name']
						];
						model('nc_menu')->update($data, $condition);
						// 处理站点菜单
						model('nc_site_menu')->update($data, $condition);
						break;
					case 'DELETE':
						
						// 处理系统菜单表
						model('nc_menu')->delete([
							'name' => $item['name']
						]);
						// 处理站点菜单表
						model('nc_site_menu')->delete([
							'name' => $item['name']
						]);
						break;
					default:
						break;
				}
			}
			if (isset($item['child_list'])) {
				$this->upgradeMenu($item['child_list'], $item['name'], $level + 1);
			}
		}
		return success();
	}
	/************************************************************************插件更新结束**********************************************/
	/**
	 * 初始化菜单、链接入口
	 *
	 * @param string $addon_name
	 */
	public function initAddonMenu($addon_name)
	{
		// 启动事务
		Cache::clear();
		Db::startTrans();
		try {
			// 初始化菜单的时候 应该是还原成最开始的
			$del_res = model('nc_menu')->delete([
				'module' => $addon_name
			]);
			$addon_class = get_addon_class($addon_name);
			if (!class_exists($addon_class)) {
				return error('ADDON_NOT_EXIST');
			}
			$this->addon = new $addon_class();
			$res = $this->installMenu();
			// 是否需要安装站点的菜单--跟之前的对比name
			$res = $this->initSiteMenu($addon_name);
			if ($res['data'] != 0) {
				return $res;
			}
			
			//重置链接入口
			model('nc_link')->delete([
				'addon_name' => $addon_name
			]);
			
			if (isset($this->addon->config['diy'])) {
				
				$diy = $this->addon->config['diy'];
				
				// 自定义链接
				if (isset($diy['link'])) {
					$link_data = [];
					foreach ($diy['link'] as $k => $v) {
						$item = [
							'addon_name' => isset($v['addon_name']) ? $v['addon_name'] : $this->addon->info['name'],
							'name' => $v['name'],
							'title' => $v['title'],
							'design_url' => $v['design_url'],
							'web_url' => $v['web_url'],
							'h5_url' => $v['h5_url'],
							'weapp_url' => $v['weapp_url'],
							'aliapp_url' => isset($v['aliapp_url']) ? $v['aliapp_url'] : '',
							'baiduapp_url' => isset($v['baiduapp_url']) ? $v['baiduapp_url'] : '',
							'icon' => isset($v['icon']) ? $v['icon'] : '',
						];
						$link_data[] = $item;
					}
					if ($link_data) {
						model('nc_link')->addList($link_data);
					}
				}
			}
			
			Db::commit();
			return success();
		} catch (\Exception $e) {
			Db::rollback();
			return error('', $e->getMessage());
		}
	}
	
	/**
	 * 初始化站点菜单
	 */
	public function initSiteMenu($addon_name)
	{
		// 站点菜单要初始化吗？进行对比？当前插件所有的站点
		$site_id_array = model('nc_site')->query("SELECT ns.site_id FROM nc_site ns WHERE FIND_IN_SET('$addon_name', ns.addon_modules)");
		$menus = model('nc_menu')->getList([
			'module' => $addon_name,
			'port' => "SITEHOME"
		]);
		$data = [];
		foreach ($site_id_array as $site_id) {
			foreach ($menus as $k => $v) {
				$item = $v;
				$item['site_id'] = $site_id["site_id"];
				unset($item['menu_id']);
				$data[] = $item;
			}
		}
		model('nc_site_menu')->delete([
			'module' => $addon_name
		]);
		$num = model('nc_site_menu')->addList($data);
		if ($num === false || $num != count($data)) {
			return error('', 'SITE_ADD_MENU_FAIL');
		}
		return success();
	}
	
	public function getAddonMenuList($tree, $port)
	{
		$list = [];
		if (!$tree) {
			return [];
		}
		foreach ($tree as $k => $v) {
			if (isset($v['parent'])) {
				if ($v['parent'] == '') {
					$pid = '';
					$level = 1;
				} else {
					$parent_menu_info = model('nc_menu')->getInfo([
						'name' => $v['parent']
					]);
					if ($parent_menu_info) {
						$pid = $parent_menu_info['name'];
						$level = $parent_menu_info['level'] + 1;
					} else {
						$pid = $v['parent'];
						$level = 1;
					}
				}
			} else {
				$pid = 'ADDON_ROOT';
				$level = 2;
			}
			$item = [
				'name' => $v['name'],
				'title' => $v['title'],
				'url' => $v['url'],
				'is_menu' => isset($v['is_menu']) ? $v['is_menu'] : 1,
				'menu_pid' => $pid,
				'level' => $level,
				'module' => $this->addon->info['name'],
				'port' => $port,
				'sort' => isset($v['sort']) ? $v['sort'] : 100,
				'icon' => isset($v['icon']) ? $v['icon'] : '',
				'icon_selected' => isset($v['icon_selected']) ? $v['icon_selected'] : '',
			];
			array_push($list, $item);
			if (isset($v['child_list'])) {
				$this->list = [];
				$this->menuTreeToList($v['child_list'], $this->addon->info['name'], $port, $v['name'], $level + 1);
				$list = array_merge($list, $this->list);
			}
		}
		return $list;
	}
	
	private function menuTreeToList($tree, $module, $port, $pid = '', $level = 1)
	{
		if (is_array($tree)) {
			foreach ($tree as $key => $value) {
				$item = [
					'name' => $value['name'],
					'title' => $value['title'],
					'url' => $value['url'],
					'is_menu' => isset($value['is_menu']) ? $value['is_menu'] : 1,
					'menu_pid' => $pid,
					'level' => $level,
					'module' => $module,
					'port' => $port,
					'sort' => isset($value['sort']) ? $value['sort'] : 100,
					'icon' => isset($value['icon']) ? $value['icon'] : '',
					'icon_selected' => isset($value['icon_selected']) ? $value['icon_selected'] : '',
				];
				$refer = $value;
				if (isset($refer['child_list'])) {
					unset($refer['child_list']);
					array_push($this->list, $item);
					$p_name = $refer['name'];
					$this->menuTreeToList($value['child_list'], $module, $port, $p_name, $level + 1);
				} else {
					array_push($this->list, $item);
				}
			}
		}
	}
	
	/**************************************************************插件升级结束**********************************************************/
	
	/**
	 * 检测插件与应用是否安装和正常
	 *
	 * @param array $addon_arr
	 */
	public function checkAddonIsExist($addon_arr = [])
	{
		$res = 0;
		if (count($addon_arr) > 0) {
			$res = model('nc_addon')->getCount([
				'name' => [
					'in',
					$addon_arr
				],
				'status' => 1
			]);
		}
		return $res;
	}
	
	/***************************************************************************部署  安装插件*************************************************************************************/
    /**
     * 选择
     * @return array
     */
    public function installAllAddon()
    {
        try {
            // 初始化菜单的时候 应该是还原成最开始的
            $addon_list = $this->getAdminUninstallList();//获取所有插件
            $addon_data = $this->screenModule($addon_list);//筛选出应用
            $module_list = $addon_data["module_list"];//应用
            //递归由上而下安装插件
            $addon_list = $this->recursiveAddon(array_column($addon_list,null, "name"),array_column($addon_list,'name', "name"));//阶梯化插件数据

            return success($module_list);
        } catch (\Exception $e) {

            return error('', $e->getMessage());
        }
    }

    /**
     * 筛选应用
     * @param $addon_list
     * @return array
     */
    public function screenModule($addon_list)
    {
        $module_list = [];
        foreach ($addon_list as $k => $v) {
            //筛选应用
            if ($v["type"] == "ADDON_APP") {
                $module_list[] = [ "name" => $v["name"] ];
                unset($addon_list[ $k ]);
            }
        }
        $data = array(
            "addon_list" => $addon_list,
            "module_list" => $module_list
        );
        return $data;
    }

    /**
     * 插件递归整合阶层位置
     * @param $addon_list
     */
    public function recursiveAddon($addon_list = [], $addon_column_array = [])
    {
        $is_install = false;
        foreach ($addon_list as $list_k => $list_v) {
            $temp_addon = empty($list_v["preset_addon"]) ? [] : explode(",", $list_v["preset_addon"]);
            $intersect = array_intersect($temp_addon, $addon_column_array);
            if(empty($intersect)){
                $is_install = true;
                unset($addon_list[ $list_k ]);
                unset($addon_column_array[ $list_k ]);
                $res = $this->install($list_v["name"]);//安装插件
            }
        }
        if($is_install){
            $this->recursiveAddon($addon_list, $addon_column_array);
        }

        return success();
    }

    /**
	 * 初始化
	 */
	public function getAddonCategory($const)
	{
		$cache = Cache::tag("addon")->get("addon_category");
		if (!empty($cache)) {
			return success($cache);
		} else {
			$list = model("nc_addon_category")->getList();
			if (empty($list)) {
				//初始化插件
				$category = $const['addon_category'];
				model("nc_addon_category")->addList($category);
				$list = model("nc_addon_category")->getList();
			}
			Cache::tag("addon")->set("addon_category", $list);
			return success($list);
		}
	}
	
	/**
	 * 获取插件分类名称
	 */
	public function getAddonCategoryName()
	{
		$const = get_const();
		$category = $this->getAddonCategory($const);
		$name = '';
		foreach ($category['data'] as $k => $v) {
			$name = $name . ',' . $v['category_name'];
		}
		return $name;
	}
	
	/**
	 * 获取系统模块标识组
	 */
	public function getSystemAddonName()
	{
		$system_addon_array = Cache::get('getSystemAddonName');
		if (empty($system_addon_array)) {
			$sys_addon = Db::query("select GROUP_CONCAT(name) as name from nc_addon where type = 'ADDON_SYSTEM'");
			$system_addon_array = explode(',', $sys_addon[0]['name']);
			Cache::set('getSystemAddonName', $system_addon_array);
		}
		return $system_addon_array;
	}
	
	/**
	 * 快速创建插件
	 * @param unknown $data
	 */
	public function build($data)
	{
		$data['info']['name'] = trim($data['info']['name']);
		$addons_dir = './addon/module/';
		//创建目录结构
		$files = array();
		$addon_dir = "$addons_dir{$data['info']['name']}/";
		$files[] = $addon_dir;
		$files[] = "{$addon_dir}api/";
		$files[] = "{$addon_dir}api/controller/";
		$files[] = "{$addon_dir}common/";
		$files[] = "{$addon_dir}common/model/";
		$files[] = "{$addon_dir}common/view/";
		$files[] = "{$addon_dir}component/";
		$files[] = "{$addon_dir}component/controller/";
		$files[] = "{$addon_dir}component/view/";
		$files[] = "{$addon_dir}data/";
		$files[] = "{$addon_dir}lang/";
		$files[] = "{$addon_dir}sitehome/";
		$files[] = "{$addon_dir}sitehome/controller/";
		$files[] = "{$addon_dir}sitehome/view/";
		
		$files[] = "{$addon_dir}wap/";
		$files[] = "{$addon_dir}wap/controller/";
		$files[] = "{$addon_dir}wap/view/";
		$files[] = "{$addon_dir}weapp/";
		$files[] = "{$addon_dir}aliapp/";
		$files[] = "{$addon_dir}baiduapp/";
		$addon_name = "{$data['info']['name']}Addon.php";
		$files[] = "{$addon_dir}{$addon_name}";
		$files[] = $addon_dir . 'config.php';
		$files[] = $addon_dir . 'ico.png';
		create_dir_or_files($files);
		$addon_file = file_get_contents('./application/common/model/build/addon.php');
		$addon_file = str_replace('{{addon_name}}', $data['info']['name'], $addon_file);
		$addon_file = str_replace('{{addon_title}}', $data['info']['title'], $addon_file);
		$addon_file = str_replace('{{addon_description}}', $data['info']['description'], $addon_file);
		$addon_file = str_replace('{{addon_version}}', $data['info']['version'], $addon_file);
		$addon_file = str_replace('{{addon_type}}', $data['info']['type'], $addon_file);
		$addon_file = str_replace('{{addon_category}}', $data['info']['category'], $addon_file);
		$addon_file = str_replace('{{addon_content}}', $data['info']['content'], $addon_file);
		$addon_file = str_replace('{{addon_preset_addon}}', $data['info']['preset_addon'], $addon_file);
		$addon_file = str_replace('{{addon_support_addon}}', $data['info']['support_addon'], $addon_file);
		$addon_file = str_replace('{{addon_support_app_type}}', $data['info']['support_app_type'], $addon_file);
		$addon_file = str_replace('{{addon_function}}', '', $addon_file);
		file_put_contents("{$addon_dir}{$addon_name}", $addon_file);
		$config_file = file_get_contents('./application/common/model/build/config.php');
		file_put_contents($addon_dir . 'config.php', $config_file);
		return success();
		
	}
	
	/**
	 * 获取站点系统应用和营销应用
	 * @param $site_id
	 * @return array
	 */
	public function getSiteSystemModule($site_id)
	{
		//验证应用权限
		if ($site_id > 0) {
			$site_model = new Site();
			$site_info_result = $site_model->getSiteInfo([ "site_id" => $site_id ], "addon_app,addon_modules");
			$site_info = $site_info_result["data"];
			$addon_model = new Addon();
			$addon_info_result = $addon_model->getAddonInfo([ "name" => $site_info["addon_app"] ], "preset_addon");
			$addon_info = $addon_info_result["data"];
			
			$preset_addon = explode(",", $addon_info["preset_addon"]);
			$addon_modules = explode(",", $site_info["addon_modules"]);
			$system_addon = array_intersect($preset_addon, $addon_modules);
            $system_addon[] = $site_info["addon_app"];
			$module_addon = array_diff($addon_modules, $system_addon);
			return success(array( "system" => $system_addon, "mobule" => $module_addon ));
			
		}
	}
	
}