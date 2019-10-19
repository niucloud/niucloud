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

use addon\system\DiyView\common\model\BottomNav;
use app\common\model\Auth;
use app\common\model\DiyView;
use app\common\model\Site;

/**
 * 网站整体配置控制器
 * 修改时间：2018年7月24日11:53:25
 */
class Design extends BaseView
{
	
	public $name;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 装修
	 * 修改时间：2018年7月24日11:53:11
	 */
	public function diyView()
	{
		$diy_view = new DiyView();
		$support_addons_list = $diy_view->getDiyViewSupportAddonsList($this->siteInfo, 'H5');
		
		$diy_view_list = array();
		
		//加载当前站点应用的默认页面
		foreach ($support_addons_list['data'] as $k => $v) {
			
			if ($v['addon_name'] == $this->siteInfo['addon_app']) {
				$diy_view_list[] = [
					'addon_name' => $v['addon_name'],
					'name' => $v['name'],
					'title' => $v['title']
				];
			}
		}
		
		$this->assign("diy_view_list", $diy_view_list);
		
		$default_name = "";
		if (!empty($diy_view_list)) {
			$default_name = $diy_view_list[0]['name'];
		}
		$name = input("name", $default_name);
		$addon_name = input("addon_name", "");
		$name = strtoupper($name);
		$this->assign("name", $name);
		$this->assign("addon_name", $addon_name);
		
		return $this->fetch('design/diy_view', [], $this->replace);
	}
	
	/**
	 * 模块装修
	 */
	public function diyModule()
	{
		var_dump("模块装修");
	}
	
	/**
	 * 装修编辑
	 * 修改时间：2018年7月24日11:53:11
	 */
	public function edit()
	{
		$name = input("name", "");
		$this->assign('name', $name);
		
		$addon_name = input("addon_name", "");//插件标识
		$this->assign('addon_name', $addon_name);
		
		return $this->fetch('design/edit', [], $this->replace);
	}
	
	/**
	 * 装修编辑（多平台小程序）
	 * 修改时间：2018年7月24日11:53:11
	 */
	public function editForApplet()
	{
		$name = input("name", "");
		$this->assign('name', $name);
		
		$addon_name = input("addon_name", "");//插件标识
		$this->assign('addon_name', $addon_name);
		
		$auth_model = new Auth();
		if (!$auth_model->checkModuleAuth($addon_name, $this->groupInfo, "diyview_page_array")) {
			$this->error("当前操作无权限！");
		}
		
		$this->assign("title", "微页面编辑");
		
		return $this->fetch('design/edit', [], $this->replace);
	}
	
	/**
	 * 微页面
	 * 修改时间：2018年7月24日15:32:31
	 */
	public function feature()
	{
		$diy_view = new DiyView();
		if (IS_AJAX) {
			$page_index = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$addon_name = input("addon_name", "");//插件标识
			$condition = array(
				'nsdv.site_id' => $this->siteId,
				'nsdv.type' => 'DEFAULT',
				'nsdv.show_type' => 'H5',
				'ndva.addon_name' => null, //排除插件中的自定义模板
				'nsdv.addon_name' => $addon_name //查询当前插件应用的微页面
			);
			$list = $diy_view->getSiteDiyViewPageList($condition, $page_index, $limit, "nsdv.create_time desc");
			return $list;
		} else {
			
			$addon_name = input("addon_name", $this->siteInfo['addon_app']);//插件标识
			$this->assign("addon_name", $addon_name);
			
			$auth_model = new Auth();
			if (!$auth_model->checkModuleAuth($addon_name, $this->groupInfo, "auth_page_array")) {
				$this->error("当前操作无权限！");
			}
			
			$this->assign("addon_app", $this->siteInfo['addon_app']);
			
			$support_addons_list = $diy_view->getDiyViewSupportAddonsList($this->siteInfo, 'H5');
			
			$diy_view_list = array();
			
			//加载当前站点应用的默认页面
			foreach ($support_addons_list['data'] as $k => $v) {
				
				if ($v['addon_name'] == $this->siteInfo['addon_app']) {
					$diy_view_list[] = [
						'addon_name' => $v['addon_name'],
						'name' => $v['name'],
						'title' => $v['title'],
						'icon' => $v['icon'],
						'create_time' => $v['create_time']
					];
				}
			}
			
			$this->assign("edit_flag", '');
			$this->assign("diy_view_list", $diy_view_list);
			return $this->fetch('design/feature');
		}
	}
	
	/**
	 * 微页面（多平台小程序）
	 */
	public function featureForApplet()
	{
		$diy_view = new DiyView();
		if (IS_AJAX) {
			$page_index = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$addon_name = input("addon_name", "");//插件标识
			$condition = array(
				'nsdv.site_id' => $this->siteId,
				'nsdv.type' => 'DEFAULT',
				'nsdv.show_type' => 'H5',
				'ndva.addon_name' => null, //排除插件中的自定义模板
				'nsdv.addon_name' => $addon_name //查询当前插件应用的微页面
			);
			$list = $diy_view->getSiteDiyViewPageList($condition, $page_index, $limit, "nsdv.create_time desc");
			return $list;
		} else {
			
			$this->assign("title", "微页面");
			
			$addon_name = input("addon_name", $this->siteInfo['addon_app']);//插件标识
			$this->assign("addon_name", $addon_name);
			
			$auth_model = new Auth();
			if (!$auth_model->checkModuleAuth($addon_name, $this->groupInfo, "diyview_page_array")) {
				$this->error("当前操作无权限！");
			}
			
			$this->assign("addon_app", $this->siteInfo['addon_app']);
			
			$support_addons_list = $diy_view->getDiyViewSupportAddonsList($this->siteInfo, 'H5');
			
			$diy_view_list = array();
			
			//加载当前站点应用的默认页面
			foreach ($support_addons_list['data'] as $k => $v) {
				
				if ($v['addon_name'] == $this->siteInfo['addon_app']) {
					$diy_view_list[] = [
						'addon_name' => $v['addon_name'],
						'name' => $v['name'],
						'title' => $v['title'],
						'icon' => $v['icon'],
						'create_time' => $v['create_time']
					];
				}
			}
			
			$this->assign("edit_flag", 'forapplet');
			$this->assign("diy_view_list", $diy_view_list);
			return $this->fetch('design/feature', [], $this->replace);
		}
	}
	
	/**
	 * 删除自定义模板页面
	 * 创建时间：2018年8月1日18:08:21
	 */
	public function deleteSiteDiyView()
	{
		$diy_view = new DiyView();
		$id_array = input("post.id", 0);
		$condition = array();
		$condition['id'] = [
			'in',
			$id_array
		];
		$res = $diy_view->deleteSiteDiyView($condition);
		return $res;
	}
	
	/**
	 * 获取应用中固定模板
	 */
	private function getAppTemplateArr()
	{
		$site_model = new Site();
		$site_info = $site_model->getSiteInfo([ 'site_id' => $this->siteId ]);
		$app_path = ADDON_APP_PATH . $site_info['data']['addon_app'] . DS . 'wap' . DS . 'view' . DS;
		if (is_dir($app_path)) {
			$sub_dir_arr = scandir($app_path);
			$template_arr = [];
			foreach ($sub_dir_arr as $dir_name) {
				if ($dir_name != '.' && $dir_name != '..') {
					$template_config_file_path = $app_path . $dir_name . DS . 'config.json';
					if (file_exists($template_config_file_path)) {
						$template_config = file_get_contents($template_config_file_path);
						$template_config_data = json_decode($template_config, true);
						$img_relative_path = __ROOT__ . '/addon/app/' . $site_info['data']['addon_app'] . '/wap/view/' . $dir_name . '/';
						$img_relative_path = str_replace('/', DS, $img_relative_path);
						$template_config_data['picture'] = $img_relative_path . $template_config_data['picture'];
						array_push($template_arr, $template_config_data);
					}
				}
			}
			return $template_arr;
		}
	}
	
	/**
	 * 模板设置
	 */
	public function templateSetting()
	{
		if (IS_AJAX) {
			$site_model = new Site();
			$wap_template = input('wap_template', '');
			$res = $site_model->editSite([ 'wap_template' => $wap_template ], [ 'site_id' => SITE_ID ]);
			return $res;
		} else {
			$template_arr = $this->getAppTemplateArr();
			$this->assign('template_arr', $template_arr);
			$this->assign("wap_template", json_decode($this->siteInfo['wap_template'], true));
			
			return $this->fetch('design/templatesetting', [], $this->replace);
		}
	}
	
	/**
	 * 底部导航
	 */
	public function bottomNavDesign()
	{
		$bottom_nav = new BottomNav();
		if (IS_AJAX) {
			
			$value = input("value", "");
			$addon_name = input("addon_name", "");
			$res = $bottom_nav->setBottomNavConfig($value, request()->siteid(), $addon_name);
			return $res;
		} else {
			
			$addon_name = input("addon_name", $this->siteInfo['addon_app']);
			$this->assign("addon_name", $addon_name);
			
			$class_name = get_addon_class($this->siteInfo['addon_app']);
			$class = new $class_name();
			
			$site_addon_str = $this->siteInfo['addon_app'] . (!empty($class->info['preset_addon']) ? ',' . $class->info['preset_addon'] : '');
			if (!empty($addon_name) && $addon_name != $this->siteInfo['addon_app']) {
				$site_addon_str = $addon_name;
			}
			
			$diy_view = new DiyView();
			$diy_view_links = $diy_view->getDiyLinkList([ "ncl.addon_name" => [ "in", $site_addon_str ] ]);
			$this->assign("diy_view_links", json_encode($diy_view_links['data']));
			
			$bottom_nav_info = $bottom_nav->getBottomNavConfig(request()->siteid(), $addon_name);
			$this->assign("bottom_nav_info", $bottom_nav_info['data']);
			
			return $this->fetch('design/bottom_nav_design', [], $this->replace);
		}
		
	}
	
	/**
	 * 自定义模块
	 */
	public function compment()
	{
	
	}
	
	public function compmentAdd()
	{
	
	}
	
	public function compmentEdit()
	{
	
	}
	
	public function deleteCompment()
	{
	
	}
}