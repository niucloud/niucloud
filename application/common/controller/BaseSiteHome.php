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
use app\common\model\Site;
use app\common\model\User;
use app\common\model\Config as ConfigModel;

class BaseSiteHome extends BaseController
{
	// 站点id
	public $siteId;
	// 站点信息
	public $siteInfo;
	
	public $auth;
	
	public $groupInfo;
	
	public $crumbsNames;
	
	public $crumbs;
	
	public $menus;
	
	public $menu_tree;
	
	public $SiteHomeStyle;
	
	public $addon;
	
	public function __construct()
	{
		// 执行父类构造函数
		parent::__construct();
		$this->checkLogin();
		
		$this->siteId = request()->siteid();
		$this->addon = request()->addon();
		$this->auth = new AuthModel();
		$this->assign("request_addon", $this->addon);
		
		if ($this->siteId != 0) {
			$this->groupInfo = $this->getGroupInfo();
			if (empty($this->groupInfo)) {
				$this->error(lang('SITE_PERMISSION_DENIED'));
			}
			$check_auth = $this->checkAuth();
			if (!$check_auth) {
				$this->error(lang('PERMISSION_DENIED'));
			}
			if (!IS_AJAX) {
				$this->initSite();
			}
		}
	}
	
	/**
	 * 初始化站点
	 *
	 * @param number $site_id
	 */
	protected function initSite($site_id = 0)
	{
		$site_info = session("site_info_" . UID);
		if (empty($site_info) || !$site_info) {
			$site_model = new Site();
			$site_info = $site_model->getSiteInfo([
				'site_id' => $this->siteId,
			]);
			$site_info = $site_info['data'];
		}
		
		if ($this->siteId != 0 && $this->siteId != 1 && $this->siteId != 2) {
			cookie(DOMAIN . "default_site_id_" . UID, $this->siteId);
		}
		$this->siteInfo = $site_info;
		$this->assign('site_info', $this->siteInfo);
		$load_menu = defined("LOAD_MENU") ? LOAD_MENU : '';
		if ($load_menu == '') {
			$this->loadMenu();
		}
		
		//站点配置信息
		$config_model = new ConfigModel();
		$config_info = $config_model->getConfigInfo([ 'name' => 'SYSTEM_SITE_CONFIG' ]);
		$system_site_config_info = empty($config_info['data']['value']) ? [] : json_decode($config_info['data']['value'], true);
		$this->assign("system_site_config_info", $system_site_config_info);
		
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
				$site_user_info = $user_model->getSiteUserInfo([ 'uid' => UID, 'site_id' => $this->siteId ], 'group_id');
				$group_id = $site_user_info['data']['group_id'];
				defined('GROUP_ID') or define('GROUP_ID', $group_id);
			}
			
			$info = $this->auth->getGroupInfo([ 'group_id' => $group_id, 'site_id' => $this->siteId ]);
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
		$res = $this->auth->checkAuth($this->siteId, $group_info, $this->url);
		return $res;
	}
	
	/**
	 * 加载菜单
	 */
	protected function loadMenu()
	{
		if (empty($this->SiteHomeStyle)) {
			$app = $this->siteInfo['addon_app'];
			$app_class = get_addon_class($app);
			$styleClass = (new $app_class())->styleClass ? : 'app\common\controller\StandardSiteHomeStyle';
			$this->SiteHomeStyle = new $styleClass();
		}
		$this->SiteHomeStyle->loadMenu($this);
	}
	
	/**
	 * 模板变量赋值
	 * @access protected
	 * @param  mixed $name 要显示的模板变量
	 * @param  mixed $value 变量的值
	 * @return $this
	 */
	public function assign($name, $value = '')
	{
		
		$this->view->assign($name, $value);
		return $this;
	}
	
	/**
	 * 加载样式
	 * @param unknown $style_obj
	 */
	public function loadStyle($style_obj)
	{
		$this->SiteHomeStyle = $style_obj;
	}
	
	/**
	 * 验证登录A
	 */
	private function checkLogin()
	{
		// 验证登录
		if (!UID) {
			// 没有登录跳转到登录页面
			$this->redirect('home/Login/login');
		}
	}
	
	public function getCrumbsNames()
	{
		return $this->crumbsNames;
	}
	
	public function setCrumbsNames()
	{
		$addon = ADDON_NAME ? ADDON_NAME . '://' : '';
		$url = $addon . URL_MODULE;
		$auth_model = new AuthModel();
		$info = $auth_model->getFirstSiteMenu([
			'site_id' => $this->siteId,
			'url' => $url
		], 'name, title, menu_pid, url, is_menu, icon, icon_selected, sort, module, is_blank, port', "level desc");
		$info = $info['data'];
		if (isset($info['name'])) {
			$this->getParentMenuList($info['name']);
		}
	}
	
	/**
	 * 获取上级菜单列表
	 *
	 * @param number $menu_id
	 */
	private function getParentMenuList($name = '')
	{
		if (!empty($name)) {
			$auth_model = new AuthModel();
			$menu_info = $auth_model->getSiteMenuInfo([
				'site_id' => $this->siteId,
				'name' => $name
			], 'name, title, menu_pid, url, is_menu, icon, icon_selected, sort, module, is_blank, port');
			$menu_info = $menu_info['data'];
			if (!empty($menu_info)) {
				$this->getParentMenuList($menu_info['menu_pid']);
				$this->crumbs[] = $menu_info;
				$this->crumbsNames[] = $menu_info['name'];
			}
		}
		
	}
	
	public function parseMenu($list)
	{
		$data = [];
		if (empty($this->crumbs)) {
			$data[0] = list_to_tree($list, 'name', 'menu_pid', 'child_list', '');
			return $data;
		}
		foreach ($this->crumbs as $k => $v) {
			$end_item = end($this->crumbs);
			foreach ($list as $ko => $vo) {
				$vo['selected'] = false;
				if ($v['menu_pid'] == $vo['menu_pid']) {
					if ($v['name'] == $vo['name']) {
						$vo['selected'] = true;
					}
					$data[ $k ][] = $vo;
					unset($vo);
				}
			}
			if ($end_item['name'] == $v['name']) {
				$end_data = [];
				foreach ($list as $ke => $ve) {
					if ($v['name'] == $ve['menu_pid']) {
						$end_data[] = $ve;
						unset($ve);
					}
				}
				array_push($data, $end_data);
			}
		}
		return $data;
	}
	
}