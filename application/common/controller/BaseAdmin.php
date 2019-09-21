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

namespace app\common\controller;

use app\common\model\Auth as AuthModel;
use app\common\model\User;
use app\common\model\Config as ConfigModel;

class BaseAdmin extends BaseController
{
	//有权限的菜单树
	protected $menuTree = [];
	//有权限的菜单树
	protected $menuView = [];
	protected $crumbs = [];
	protected $crumbsNames = [];
	protected $crumbsView = '';
	
	protected $auth;
	protected $group_info;
	protected $menus;
	
	public function __construct()
	{
		//执行父类构造函数
		parent::__construct();
		request()->siteid(0);
		
		//检测登录并且判断是否是后台会员
		$this->checkLogin();
		
		$this->auth = new AuthModel();
		$this->group_info = $this->getGroupInfo();
		
		//检测平台用户
		if (!$this->checkAuth()) {
			$this->error('没有权限');
		}
		if (!request()->isAjax()) {
			//获取菜单
			$this->menus = $this->getMenu();
			$this->initBaseInfo();
			
			//站点配置信息
			$config_model = new ConfigModel();
			$config_info = $config_model->getConfigInfo([ 'name' => 'SYSTEM_SITE_CONFIG' ]);
			$system_site_config_info = empty($config_info['data']['value']) ? [] : json_decode($config_info['data']['value'], true);
			$this->assign("system_site_config_info",$system_site_config_info);
		}
		
	}
	
	/**
	 * 获取菜单
	 */
	private function getMenu()
	{
		if (empty($this->group_info)) {
			return [];
		}
		if ($this->group_info['is_system'] == 1) {
			$menus = $this->auth->getMenuList([ 'port' => 'ADMIN', 'is_menu' => 1 ], '*', 'sort asc');
		} else {
			$menus = $this->auth->getMenuList([ 'name' => [ 'in', $this->group_info['array'] ], 'is_menu' => 1 ], '*', 'sort asc');
		}
		
		return $menus['data'];
	}
	
	/**
	 * 验证登录
	 */
	private function checkLogin()
	{
		//验证登录
		if (!UID) {
			$this->redirect(url('Login/login'));
		}
	}
	
	/**
	 * 加载基础信息
	 */
	private function initBaseInfo()
	{
		//获取一级权限菜单
		$first_menu = $this->getFirstMenu();
		$this->assign("first_menu", $first_menu);
		$auth_model = new AuthModel();
		$info = $auth_model->getMenuInfoByUrl($this->url);
		$this->getParentMenuList($info['data']['name']);
		
		//加载菜单树
		$this->menuTree = list_to_tree($this->menus, 'name', 'menu_pid', 'child_list', '');
		
		$this->menuToView($this->menuTree);
		$this->assign("url", $this->url);
		$this->assign("menu", $this->menuView);
		$this->assign("crumbs", $this->crumbs);
		
	}
	
	/**
	 * 菜单转视图
	 * @param array $list
	 * @param string $child
	 * @param number $i
	 */
	protected function menuToView($list = [], $child = 'child_list', $i = 1)
	{
		
		foreach ($list as $key => $item) {
			if (in_array($item['name'], $this->crumbsNames)) {
				$selected = "layui-this";
			} else {
				$selected = "";
			}
			//判断是否新窗口打开
			if ($item['is_blank'] == 1) {
				$target = "target='_blank'";
			} else {
				$target = "";
			}
			//必须是菜单才拼接
			if ($item['is_menu'] == 1) {
				if (isset($item[ $child ]) && !empty($item[ $child ]) && $selected === "layui-this") {
					
					$this->menuView[ $i ][] = [
						'selected' => $selected,
						'url' => addon_url($item['url']),
						'title' => $item['title'],
						'icon' => $item['icon'],
						'icon_selected' => $item['icon_selected'],
						'target' => $target
					];
					$this->menuToView($item[ $child ], 'child_list', $i + 1);
				} else {
					$this->menuView[ $i ][] = [
						'selected' => $selected,
						'url' => addon_url($item['url']),
						'title' => $item['title'],
						'icon' => $item['icon'],
						'icon_selected' => $item['icon_selected'],
						'pid' => $item['menu_pid'],
						'target' => $target
					];
				}
			}
		}
	}
	
	/**
	 * 获取一级权限菜单
	 */
	protected function getFirstMenu()
	{
		$list = array_filter($this->menus, function ($v) {
			return $v['menu_pid'] == 0;
		});
		return $list;
		
	}
	
	/**
	 * 获取上级菜单列表
	 * @param number $menu_id
	 */
	private function getParentMenuList($name = '')
	{
		if (!empty($name)) {
			$auth_model = new AuthModel();
			$menu_info = $auth_model->getMenuInfo([ 'name' => $name ]);
			
			if (!empty($menu_info['data'])) {
				$this->getParentMenuList($menu_info['data']['menu_pid']);
				$this->crumbs[] = $menu_info['data'];
				$this->crumbsNames[] = $menu_info['data']['name'];
			}
		}
		
	}
	
	/**
	 * 获取当前用户的用户组
	 */
	private function getGroupInfo()
	{
		if (UID) {
			$group_id = defined('GROUP_ID') ? GROUP_ID : '';
			if (empty($group_id)) {
				$user_model = new User();
				$site_user_info = $user_model->getSiteUserInfo([ 'uid' => UID, 'site_id' => 0 ], 'group_id');
				$group_id = $site_user_info['data']['group_id'];
				defined('GROUP_ID') or define('GROUP_ID', $group_id);
			}
			$info = $this->auth->getGroupInfo([ 'group_id' => $group_id, 'site_id' => 0 ]);
			return $info['data'];
		} else {
			return [];
		}
	}
	
	/**
	 * 检测权限
	 */
	private function checkAuth()
	{
		$group_info = $this->getGroupInfo();
		$res = $this->auth->checkAuth(0, $group_info, $this->url);
		return $res;
	}
	
}