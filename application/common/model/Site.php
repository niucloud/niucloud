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
use think\Hook;
use think\Log;

/**
 * 管理员模型
 */
class Site
{
	
	/*******************************************************站点信息编辑与域名设置开始*****************************************************/
	/**
	 * 获取站点绑定域名信息
	 */
	public function getSiteDomains()
	{
		//查询所有站点绑定域名
		$cache = Cache::tag("site")->get("domains");
		if ($cache == -1) {
			return success([]);
		}
		if (!empty($cache)) {
			
			return success($cache);
		}
		$domains = model('nc_site')->getColumn([
			'status' => 1,
			'domain' => [
				'neq',
				''
			]
		], 'site_id', 'domain');
		if (empty($domains)) {
			Cache::tag("site")->set("domains", -1);
		} else {
			Cache::tag("site")->set("domains", $domains);
		}
		
		return success($domains);
	}
	
	/**
	 * 初始化加载钩子
	 * @param unknown $site_id
	 */
	public function initHook($site_id)
	{
		Hook::clear();
		$site_model = new Site();
		$addon_model = new Addon();
		if (!empty($site_id)) {
			$site_info = $site_model->getSiteInfo([ 'site_id' => $site_id ]);
			if (!empty($site_info['data'])) {
				$site_module_array = explode(',', $site_info['data']['addon_modules']);
				$system_addon_array = $addon_model->getSystemAddonName();
				$site_module_array = array_unique(array_merge($site_module_array, $system_addon_array));
				$addon_data = $addon_model->getAddons();
				$addon_class = $addon_data['addon_class'];
				$hooks = $addon_model->getHookColumn([], 'addons', 'name');
				// 获取钩子的实现插件信息
				foreach ($hooks['data'] as $key => $value) {
					if ($value) {
						$names_array = explode(',', $value);
						$names_array = array_intersect($site_module_array, $names_array);
						foreach ($names_array as $k_name => $v_name) {
							if (!empty($addon_class[ $v_name ])) {
								Hook::add($key, $addon_class[ $v_name ]);
							}
						}
					}
				}
			}
		} else {
			$hooks = $addon_model->getHookColumn([], 'addons', 'name');
			$addon_data = $addon_model->getAddons();
			$addon_class = $addon_data['addon_class'];
			// 获取钩子的实现插件信息
			foreach ($hooks['data'] as $key => $value) {
				if ($value) {
					$names_array = explode(',', $value);
					foreach ($names_array as $k_name => $v_name) {
						Hook::add($key, $addon_class[ $v_name ]);
					}
				}
			}
		}
		//批量导入初始化行为
		$tags = include APP_PATH . 'tags.php';
		Hook::import($tags);
	}
	
	/**
	 * 获取第一个站点信息
	 * @return multitype:string mixed
	 */
	public function getFirstSite()
	{
		$site_info = model('nc_site')->getInfo([], '*');
		return success($site_info);
	}
	
	/**
	 * 修改站点基本信息
	 * @param array $data
	 * @param array $condition
	 */
	public function editSite($data, $condition)
	{
		
		$site_id = isset($condition['site_id']) ? $condition['site_id'] : '';
		if ($site_id === '') {
			return error('', '缺少必须参数site_id');
		}
		Cache::clear("site_" . $site_id);
		//特殊处理站点域名
		$domain = isset($data['domain']) ? $data['domain'] : '';
		if (!empty($domain)) {
			Cache::tag("site")->set("domains", '');
		}
		$res = model('nc_site')->update($data, $condition);
		
		if ($res >= 0) {
			return success($res);
		} else {
			return error($res);
		}
	}
	/*******************************************************站点信息编辑与域名设置结束*****************************************************/
	/*******************************************************站点基础查询**************************************************************/
	/**
	 * 获取站点列表
	 *
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 * @return multitype:string mixed
	 */
	public function getSiteList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$list = model('nc_site')->getList($condition, $field, $order, '', '', '', $limit);
		return success($list);
	}
	
	/**
	 * 获取站点分页列表
	 *
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getSitePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		
		$field = 'ns.site_id,ns.uid,ns.site_name,ns.icon,ns.desc,ns.addon_app,ns.status,nu.uid,nu.username,na.title,ns.validity_time,ns.create_time, nu.nick_name, nu.mobile';
		$alias = 'ns';
		$join = [
			[
				'nc_user nu',
				'ns.uid = nu.uid',
				'INNER'
			],
			[
				'nc_addon na',
				'ns.addon_app = na.name',
				'INNER'
			]
		];
		$list = model('nc_site')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
		return success($list);
	}
	
	/**
	 * 获取用户管理的站点列表
	 * @param unknown $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 */
	public function getSitePageListByUid($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '')
	{
		$join = [
			[ 'nc_site ns', 'ns.site_id = nsu.site_id' ],
			[ 'nc_user nu', 'nsu.uid = nu.uid' ],
			[ 'nc_group ng', 'ng.group_id = nsu.group_id', 'left' ],
			[ 'nc_addon na', 'ns.addon_app = na.name' ]
		];
		$field = 'nu.mobile,ns.domain,ns.create_time,ns.desc,na.description,ns.site_id,ns.site_name,ns.icon,na.icon as app_icon,ns.desc,ns.addon_app,ns.status,nu.uid,nu.username,na.title,ns.validity_time,ns.create_time, nu.nick_name, nu.mobile, ng.group_id, ng.group_name';
		$list = model('nc_site_user')->pageList($condition, $field, $order, $page, $page_size, 'nsu', $join);
		return success($list);
	}
	
	/**
	 * 获取站点安装插架情况
	 * @param unknown $condition
	 */
	public function getModuleInformationList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		// 判断当前是够存在插件特殊处理
		$site_info = model('nc_site')->getInfo([
			'site_id' => $condition['site_id']
		]);
		$condition = [];
		$condition['visble'] = 1;
		$condition['name'] = [ 'IN', $site_info['addon_modules'] ];
		$list = model('nc_addon')->pageList($condition, $field, $order, $page, $page_size);
		if (!empty($list['list'])) {
			foreach ($list['list'] as $k => $v) {
				$list['list'][ $k ]['support_app_type_arr'] = getSupportPort($v['support_app_type']);
			}
		}
		return success($list);
	}
	
	/**
	 * 获取站点信息
	 *
	 * @param array $condition
	 */
	public function getSiteInfo($condition)
	{
		$site_id = isset($condition['site_id']) ? $condition['site_id'] : '';
		if ($site_id === '') {
			return error('', '缺少必须参数site_id');
		}
		$data = json_encode([ $condition ]);
		$cache = Cache::tag("site_" . $site_id)->get("getSiteInfo_" . $site_id . "_" . $data);
		if (!empty($cache)) {
			return success($cache);
		}
		$alias = 'ns';
		$join = [
			[
				'nc_addon na',
				'na.name = ns.addon_app',
				'left'
			]
		];
		$field = 'ns.*, na.title as addon_name';
		$info = model('nc_site')->getInfo($condition, $field, $alias, $join);
		Cache::tag("site_" . $site_id)->set("getSiteInfo_" . $site_id . "_" . $data, $info);
		return success($info);
	}
	/********************************************************************站点基础查询结束*************************************************/
	
	/*********************************************************************添加站点开始***************************************************/
	/**
	 * 添加站点
	 *
	 * @param array $data
	 */
	public function addSite($data)
	{
		//添加站点前处理
		$check_res = $this->addSiteBeforeCheck($data);
		if ($check_res['code'] != 0) {
			return $check_res;
		}
		$data['app_key'] = get_site_app_key();
		$data['app_secret'] = get_site_app_secret();
		$res = hook('addSiteBefore', $data);
		if (empty($res)) {
			//独立部署系统
			$data['validity_time'] = 0;
			$data['status'] = 1;
		} else {
			if ($res[0]['code'] == 0) {
				$data['validity_time'] = $res[0]['data']['validity_time'];
			}
			
		}
		
		$modules = $this->getInitSiteModules($data['uid'], $data['addon_app']);
		array_push($modules, $data['addon_app']);
		
		if (!empty($modules)) {
			$data['addon_modules'] = implode(',', $modules);
		}
		// 启动事务
		Db::startTrans();
		try {
			//添加站点主表
			$site_id = model('nc_site')->add($data);
			
			if ($site_id) {
				// 添加权限
				$group_data = [
					'group_name' => '管理员组',
					'site_id' => $site_id,
					'is_system' => 1,
					'create_time' => time(),
					'array' => ''
				];
				$group_id = model('nc_group')->add($group_data);
				$site_user_data = [
					'uid' => $data['uid'],
					'site_id' => $site_id,
					'group_id' => $group_id,
				];
				model('nc_site_user')->add($site_user_data);
				
				//添加默认账户
				$json_data = [];
				for ($i = 0; $i < 7; $i++) {
					$key = 'credit' . ($i + 1);
					$name = '账户' . ($i + 1);
					$is_exchange = 0;
					$is_use = 0;
					$rate = 0;
					if ($i == 0) {
						$name = '积分';
						$is_exchange = 1;
						$is_use = 1;
					} elseif ($i == 1) {
						$name = '余额';
						$is_exchange = 1;
						$is_use = 1;
					} elseif ($i == 2) {
						$name = '成长值';
						$is_exchange = 1;
						$is_use = 1;
					}
					$json_data[ $i ] = array(
						'key' => $key,
						'name' => $name,
						'is_exchange' => $is_exchange,
						'is_use' => $is_use,
						'rate' => $rate
					);
				}
				
				$account_data = array(
					"site_id" => $site_id,
					"name" => "NS_MEMBER_ACCOUNT_CONFIG",
					"value" => json_encode($json_data),
					'create_time' => time()
				);
				
				$this->setSiteConfig($account_data);
				
				//账户策略
				if (!empty($json_data[0])) {
					//添加系统或营销策略
					$basic_tactics_data = array(
						"site_id" => $site_id,
						"name" => "NS_BASIC_TACTICS_CONFIG",
						"value" => $json_data[0]["key"],
						'create_time' => time()
					);
					$this->setSiteConfig($basic_tactics_data);
					
				}
				
				if (!empty($json_data[1])) {
					//添加系统或营销策略
					$pay_tactics_data = array(
						"site_id" => $site_id,
						"name" => "NS_PAY_TACTICS_CONFIG",
						"value" => $json_data[1]["key"],
						'create_time' => time()
					);
					$this->setSiteConfig($pay_tactics_data);
				}
				
				// 添加菜单
				array_push($modules, $data['addon_app'], 'SITEHOME');
				$res1 = $this->initSiteMenu($site_id, $modules);
				if ($res1['code'] != 0) {
					return $res1;
				}
				
				$this->refreshSiteMenu($site_id);
				
				// 初始化站点
				$res2 = $this->initSite($site_id);
				if ($res2['code'] != 0) {
					return $res2;
				}
			}
			Db::commit();
			return success($site_id);
		} catch (\Exception $e) {
			Db::rollback();
			return error('', $e->getMessage());
		}
	}
	
	/**
	 * 添加站点前检测
	 * @param array $data
	 */
	private function addSiteBeforeCheck($data)
	{
		return success();
	}
	
	/**
	 * 初始化站点菜单
	 * @param int $site_id
	 */
	private function initSiteMenu($site_id, $modules)
	{
		
		$auth_model = new Auth();
		
		$menu_array = $auth_model->getMenuList([
			'module' => [
				'IN',
				$modules
			],
			'port' => "SITEHOME"
		]);
		
		$menu_array = $menu_array['data'];
		$data = [];
		foreach ($menu_array as $k => $v) {
			$item = $v;
			$item['site_id'] = $site_id;
			unset($item['menu_id']);
			$data[] = $item;
		}
		$num = model('nc_site_menu')->addList($data);
		if ($num === false || $num != count($data)) {
			return error('', 'SITE_ADD_MENU_FAIL');
		}
		return success();
	}
	
	/**
	 * 刷新站点菜单
	 * @param unknown $site_id
	 */
	public function refreshSiteMenu($site_id)
	{
		$site_info = $this->getSiteInfo([ 'site_id' => $site_id ]);
		$site_menu = model('nc_site_menu')->getColumn([ 'site_id' => $site_id ], 'name');
		$site_menu_str = implode(',', $site_menu);
		model('nc_site_menu')->update([ 'menu_pid' => 'ADDON_ROOT' ], [ 'module' => [ 'NEQ', $site_info['data']['addon_app'] ], 'menu_pid' => '', 'name' => [ 'NEQ', 'ADDON_ROOT' ], 'site_id' => $site_id ]);
		model('nc_site_menu')->update([ 'menu_pid' => 'ADDON_ROOT' ], [ 'module' => [ 'NEQ', $site_info['data']['addon_app'] ], 'menu_pid' => [ 'NOT IN', $site_menu_str ], 'name' => [ 'NEQ', 'ADDON_ROOT' ], 'site_id' => $site_id ]);
	}
	
	/**
	 * 初始化站点
	 *
	 * @param int $site_id
	 * @return boolean
	 */
	private function initSite($site_id)
	{
		// 初始化应用的数据（复制应用的默认基础数据到站点）
		$site_info = $this->getSiteInfo([
			'site_id' => $site_id
		]);
		$site_info = $site_info['data'];
		$addon_app = $site_info['addon_app'];
		$uid = $site_info['uid'];
		$res = init_addon($site_id, $addon_app);
		if (!$res) {
			return error('', 'SITE_APP_INIT_FAIL');
		}
		
		// 初始化模块的数据（复制用户购买的模块 的默认基础数据到站点）
		$modules = $this->getInitSiteModules($uid, $addon_app);
		foreach ($modules as $k => $v) {
			$res = init_addon($site_id, $v);
			if ($res['code'] != 0) {
				return error('', 'SITE_MODULE_INIT_FAIL');
			}
		}
		return success();
	}
	
	/**
	 * 获取初始化站点 的模块
	 * @param int $uid
	 * @param string $addon_app
	 */
	protected function getInitSiteModules($uid, $addon_app)
	{
		$addon_app_info = model('nc_addon')->getInfo([ 'name' => $addon_app ], 'preset_addon, support_addon');
		//预置的 module
		$preset_modules = explode(',', $addon_app_info['preset_addon']);
		
		//最终需要的 module
		$need_modules = $preset_modules;
		$modules = model('nc_addon')->getColumn([], 'name');
		$need_modules = array_intersect($need_modules, $modules);
		return $need_modules;
	}
	/**************************************************************添加站点结束**********************************************************/
	
	/**************************************************************添加站点模块开始*******************************************************/
	/**
	 * 添加站点模块
	 * @param int $site_id
	 * @param string $module
	 */
	public function addSiteModule($site_id, $module)
	{
		//检测站点模块
		$site_info = model("nc_site")->getInfo([ 'site_id' => $site_id ]);
		$res = $this->addSiteModuleBeforeCheck($site_info, $module);
		if ($res['code'] != 0) {
			return $res;
		}
		//清理站点权限
		$auth_model = new Auth();
		$auth_model->clearSiteAuth($site_id);
		
		// 启动事务
		Db::startTrans();
		try {
			
			//开始安装站点菜单
			$auth_model = new Auth();
			$menu_array = $auth_model->getMenuList([
				'module' => $module
			]);
			$menu_array = $menu_array['data'];
			$data = [];
			foreach ($menu_array as $k => $v) {
				$item = $v;
				$item['site_id'] = $site_id;
				unset($item['menu_id']);
				$data[] = $item;
			}
			if (!empty($data)) {
				$res = model('nc_site_menu')->addList($data);;
				$url = $data[0]['url'];
			} else {
				$url = '';
			}
			$this->refreshSiteMenu($site_id);
			
			//站点增加菜单
			$res = model("nc_site")->update([ 'addon_modules' => $site_info['addon_modules'] . ',' . $module ], [ 'site_id' => $site_id ]);
			
			//初始化站点数据
			$class_name = get_addon_class($module);
			$class = new $class_name();
			$res = $class->addToSite($site_id);
			$class->initSiteDiyViewData($site_id);
			if ($res['code'] < 0) {
				Db::rollback();
				return $res;
			}
			//清理站点数据
			Cache::clear("site_" . $site_id);
			Db::commit();
			return success([ 'url' => $url ]);
		} catch (\Exception $e) {
			Db::rollback();
			return error('', $e->getMessage());
		}
		
	}
	
	/**
	 * 删除站点模块
	 * @param int $site_id
	 * @param string $module
	 */
	public function deleteSiteModule($site_id, $module)
	{
		//清理站点权限缓存
		$auth_model = new Auth();
		$auth_model->clearSiteAuth($site_id);
		
		//清理站点数据
		Cache::clear("site_" . $site_id);
		
		Db::startTrans();
		try {
			
			$site_info = model("nc_site")->getInfo([ 'site_id' => $site_id ], 'addon_app,addon_modules');
			
			//当前站点信息
			$addon_class_name = get_addon_class($site_info['addon_app']);
			$addon_app = new $addon_class_name();
			
			$class_name = get_addon_class($module);
			$class = new $class_name();
			
			//判断要卸载的模块是否属于预装插件
			if (strpos($addon_app->info['preset_addon'], $module) !== false) {
				return error('', '[' . $class->info['title'] . ']插件属于预装载插件，无法卸载!');
			}
			
			$res = model('nc_site_menu')->delete([ 'site_id' => $site_id, 'module' => $module ]);
			
			$addon_modules = $site_info['addon_modules'];
			$addon_modules = str_replace($module . ',', '', $addon_modules . ',');
			
			//清除多余的逗号
			$addon_modules = explode(",", $addon_modules);
			$addon_modules = array_filter($addon_modules);
			$addon_modules = implode($addon_modules, ",");
			
			$res = model("nc_site")->update([ 'addon_modules' => $addon_modules ], [ 'site_id' => $site_id ]);
			
			//删除模块下的自定义模板
			model("nc_site_diy_view")->delete([
				'site_id' => $site_id,
				'addon_name' => $module
			]);
			
			$res = $class->delFromSite($site_id);
			if ($res['code'] < 0) {
				Db::rollback();
				return $res;
			}
			
			Db::commit();
			
			return success();
		} catch (\Exception $e) {
			Db::rollback();
			return error('', $e->getMessage());
		}
		
	}
	
	/**
	 * 添加站点前检测
	 * @param array $data
	 */
	private function addSiteModuleBeforeCheck($site_info, $module)
	{
		//判断预置的插件是否都已经购买了
		$check = hook("addSiteModuleBeforeCheck", [ 'site_info' => $site_info, 'module' => $module ]);
		if (!empty($check)) {
			return $check[0];
		}
		$support_addon = model('nc_addon')->getInfo([ 'name' => $module ], 'type,support_addon');
		if ($support_addon['type'] == 'ADDON_SYSTEM') {
			return success();
		}
		if (strpos($support_addon['support_addon'], $site_info['addon_app']) === false) {
			return error("", "This site is not support!");
		}
		return success();
	}
	
	/**************************************************************添加站点模块结束*******************************************************/
	
	/**************************************************************复制站点开始**********************************************************/
	
	/**
	 * 复制站点
	 *
	 * @param integer $site_id
	 */
	public function copySite($site_id)
	{
		// 开启事物
		Db::startTrans();
		try {
			$site_info = model('nc_site')->getInfo([
				'site_id' => $site_id
			]);
			$uid = $site_info['uid'];
			$data = [
				'uid' => $uid,
				'site_name' => $site_info['site_name'] . '-副本',
				'desc' => $site_info['desc'],
				'create_time' => time(),
				'addon_app' => $site_info['addon_app'],
				'app_key' => get_site_app_key(),
				'app_secret' => get_site_app_secret()
			];
			$res = hook('addSiteBefore', $data);
			if (empty($res)) {
				//独立部署系统
				$data['validity_time'] = 0;
				$data['status'] = 1;
			} else {
				if ($res[0]['code'] == 0) {
					$data['validity_time'] = $res[0]['data']['validity_time'];
				}
			}
			
			$modules = $this->getInitSiteModules($data['uid'], $data['addon_app']);
			array_push($modules, $data['addon_app']);
			
			if (!empty($modules)) {
				$data['addon_modules'] = implode(',', $modules);
			}
			
			$new_site_id = model('nc_site')->add($data);
			if ($new_site_id) {
				// 添加权限
				$group_id = model('nc_group')->add([
					'group_name' => '管理员组',
					'site_id' => $new_site_id,
					'is_system' => 1,
					'create_time' => time(),
					'array' => ''
				]);
				
				// 添加关联
				model('nc_site_user')->add([
					'uid' => $uid,
					'site_id' => $new_site_id,
					'group_id' => $group_id
				]);
				
				$member_account_config_info = $this->getSiteConfigInfo([
					"site_id" => $site_id,
					"name" => "NS_MEMBER_ACCOUNT_CONFIG" ]);
				$member_account_config_info = $member_account_config_info['data'];
				
				$value = json_decode($member_account_config_info['value'], true);
				
				$account_data = array(
					"site_id" => $new_site_id,
					"name" => "NS_MEMBER_ACCOUNT_CONFIG",
					"value" => json_encode($value),
					'create_time' => time()
				);
				
				$this->setSiteConfig($account_data);
				
				//账户策略
				$basic_tactics_config_info = $this->getSiteConfigInfo([
					"site_id" => $site_id,
					"name" => "NS_BASIC_TACTICS_CONFIG" ]);
				$basic_tactics_config_info = $basic_tactics_config_info['data'];
				
				if (!empty($basic_tactics_config_info['value'])) {
					//添加系统或营销策略
					$basic_tactics_data = array(
						"site_id" => $new_site_id,
						"name" => "NS_BASIC_TACTICS_CONFIG",
						"value" => $basic_tactics_config_info['value'],
						'create_time' => time()
					);
					$this->setSiteConfig($basic_tactics_data);
					
				}
				
				//支付营销策略
				$pay_tactics_config_info = $this->getSiteConfigInfo([
					"site_id" => $site_id,
					"name" => "NS_PAY_TACTICS_CONFIG" ]);
				$pay_tactics_config_info = $pay_tactics_config_info['data'];
				if (!empty($pay_tactics_config_info['value'])) {
					//添加系统或
					$pay_tactics_data = array(
						"site_id" => $new_site_id,
						"name" => "NS_PAY_TACTICS_CONFIG",
						"value" => $pay_tactics_config_info['value'],
						'create_time' => time()
					);
					$this->setSiteConfig($pay_tactics_data);
				}
				
				// 添加菜单，查出上一个站点的菜单 加到第二个站点中
				$menu_list = model('nc_site_menu')->getList([ 'site_id' => $site_id ]);
				$new_menu_list = [];
				foreach ($menu_list as $item) {
					unset($item['menu_id']);
					$item['site_id'] = $new_site_id;
					$new_menu_list[] = $item;
				}
				model('nc_site_menu')->addList($new_menu_list);
				// 复制站点
				$this->copySiteAddon($uid, $site_id, $new_site_id);
			}
			Db::commit();
			return success($new_site_id);
		} catch (\Exception $e) {
			Db::rollback();
			return error($e->getMessage(), $e->getMessage());
		}
		
	}
	
	/**
	 * 复制站点插件
	 * @param integer $uid
	 * @param integer $site_id
	 * @param integer $new_site_id
	 */
	private function copySiteAddon($uid, $site_id, $new_site_id)
	{
		// 复制模块的数据（复制用户购买的模块 的默认基础数据到站点）
		// 获取用户购买的模块
		//暂时没有用这个表，2019年8月29日10:14:31
//		$modules = model('nc_user_addon')->getList([
//			'uid' => $uid
//		]);
//		foreach ($modules as $k => $v) {
//			$bool = copy_addon($site_id, $new_site_id, $v['addon_name']);
//			if (!$bool) {
//				return false;
//			}
//		}
		// 初始化应用的数据（复制应用的默认基础数据到站点）
		$addon_app = model('nc_site')->getValue([
			'site_id' => $site_id
		], 'addon_app');
		$res = copy_addon($site_id, $new_site_id, $addon_app);
		if (!$res) {
			return false;
		}
		return true;
	}
	/**************************************************************复制站点结束**********************************************************/
	/**************************************************************删除站点开始**********************************************************/
	
	/**
	 * 删除站点
	 * @param int $site_id
	 * @param int $uid
	 * @return multitype:string mixed
	 */
	public function deleteSite($site_id, $uid)
	{
		//清理站点权限
		$auth_model = new Auth();
		$auth_model->clearSiteAuth($site_id);
		//清理站点数据
		Cache::clear("site_" . $site_id);
		// 开启事物
		Db::startTrans();
		try {
			$info = model('nc_site')->getInfo([
				'site_id' => $site_id
			]);
			if ($info['uid'] == $uid) {
				$this->delSiteAddon($info, $uid);
				// 删除站点菜单
				model('nc_site_menu')->delete([
					'site_id' => $site_id
				]);
				// 删除权限
				$group_id = model('nc_group')->delete([
					'site_id' => $site_id
				]);
				// 删除关联
				model('nc_site_user')->delete([
					'site_id' => $site_id
				]);
				// 删除站点表
				model('nc_site')->delete([
					'site_id' => $site_id
				]);
				// 删除站点设置表
				model('nc_site_config')->delete([
					'site_id' => $site_id
				]);
				// 删除站点自定义配置表
				model('nc_site_diy_view')->delete([
					'site_id' => $site_id
				]);
				$visit_model = new Visit();
				$visit_model->deleteSite($site_id);
				//删除站点访问记录
			} else {
				// 删除关联
				model('nc_site_user')->delete([
					'site_id' => $site_id, 'uid' => $uid
				]);
			}
			
			Db::commit();
			return success();
		} catch (\Exception $e) {
			Db::rollback();
			Log::write("删除站点失败" . $e->getMessage());
			return error($e->getMessage(), 'SITE_DELETE_FAIL');
		}
	}
	
	/**
	 * 删除站点相关联数据
	 * @param unknown $site_info
	 * @param unknown $uid
	 */
	private function delSiteAddon($site_info, $uid)
	{
		// 获取用户购买的模块
		$module_array = explode(',', $site_info['addon_modules']);
		foreach ($module_array as $k => $v) {
			if (!empty($v)) {
				$bool = del_addon($site_info['site_id'], $v);
			}
			
			if (!$bool) {
				return false;
			}
		}
		// 初始化应用的数据（复制应用的默认基础数据到站点）
		$res = del_addon($site_info['site_id'], $site_info['addon_app']);
		if (!$res) {
			return false;
		}
		return true;
	}
	
	/**************************************************************删除站点结束**********************************************************/
	
	/**
	 * ***********************************************站点配置相关开始*********************************************************************
	 */
	/**
	 * 添加方法
	 */
	public function addSiteConfig($data)
	{
		$site_id = isset($data['site_id']) ? $data['site_id'] : '';
		if ($site_id === '') {
			return error('', '缺少必须参数site_id');
		}
		$name = isset($data['name']) ? $data['name'] : '';
		if ($name === '') {
			return error('', '缺少必须参数name');
		}
		Cache::tag("site_config_" . $site_id)->set("site_config_" . $site_id . "_" . $name, '');
		$res = model('nc_site_config')->add($data);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 修改方法
	 */
	public function editSiteConfig($data, $condition)
	{
		
		$site_id = isset($data['site_id']) ? $data['site_id'] : '';
		if ($site_id === '') {
			return error('', '缺少必须参数site_id');
		}
		$name = isset($data['name']) ? $data['name'] : '';
		if ($name === '') {
			return error('', '缺少必须参数name');
		}
		Cache::tag("site_config_" . $site_id)->set("site_config_" . $site_id . "_" . $name, '');
		$res = model('nc_site_config')->update($data, $condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 删除配置
	 *
	 * @param unknown $condition
	 * @return multitype:string mixed
	 */
	public function deleteSiteConfig($condition)
	{
		$site_id = isset($condition['site_id']) ? $condition['site_id'] : '';
		if ($site_id === '') {
			return error('', '缺少必须参数site_id');
		}
		$name = isset($condition['name']) ? $condition['name'] : '';
		if ($name === '') {
			return error('', '缺少必须参数name');
		}
		Cache::tag("site_config_" . $site_id)->set("site_config_" . $site_id . "_" . $name, '');
		$res = model('nc_site_config')->delete($condition);
		if ($res === false) {
			return error('', 'DELETE_FAIL');
		}
		return success($res);
	}
	
	/**
	 * 设置
	 *
	 * @param unknown $data
	 */
	public function setSiteConfig($data)
	{
		
		$site_id = isset($data['site_id']) ? $data['site_id'] : '';
		if ($site_id === '') {
			return error('', '缺少必须参数site_id');
		}
		$name = isset($data['name']) ? $data['name'] : '';
		if ($name === '') {
			return error('', '缺少必须参数name');
		}
		Cache::tag("site_config_" . $site_id)->set("site_config_" . $site_id . "_" . $name, '');
		$condition = [
			'site_id' => $data['site_id'],
			'name' => $data['name']
		];
		$info = model('nc_site_config')->getInfo($condition, '*');
		$res = empty($info) ? model('nc_site_config')->add($data) : model('nc_site_config')->update($data, $condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 获取
	 */
	public function getSiteConfigInfo($condition, $filed = '*')
	{
		
		$site_id = isset($condition['site_id']) ? $condition['site_id'] : '';
		if ($site_id === '') {
			return error('', '缺少必须参数site_id');
		}
		$name = isset($condition['name']) ? $condition['name'] : '';
		if ($name === '') {
			return error('', '缺少必须参数name');
		}
		$cache = Cache::tag("site_config_" . $site_id)->get("site_config_" . $site_id . "_" . $name);
		if (!empty($cache)) {
			return success($cache);
		}
		$info = model('nc_site_config')->getInfo($condition, $filed);
		//缺乏本配置,则创建
		if (empty($info)) {
			$data = array(
				"site_id" => $condition['site_id'],
				"name" => $condition['name'],
				"create_time" => time()
			);
			$res = $this->addSiteConfig($data);
			$info = model('nc_site_config')->getInfo($condition, $filed);
		}
		Cache::tag("site_config_" . $site_id)->set("site_config_" . $site_id . "_" . $name, $info);
		return success($info);
	}
}