<?php

namespace app\common\controller;

use app\common\model\Auth as AuthModel;
use app\common\model\DiyView;

class StandardSiteHomeStyle extends BaseSiteHomeStyle
{
	
	public function loadMenu(&$baseSiteHome)
	{
		
		$baseSiteHome->assign('style', "sitehome@style/standard/base");
		// 正式环境加载菜单
		$auth_model = new AuthModel();
		if ($baseSiteHome->groupInfo['is_system'] == 1) {
			$menu_array = $auth_model->getSiteMenuList([
				'site_id' => $baseSiteHome->siteId,
				'is_menu' => 1,
				'port' => 'SITEHOME'
			], '*', 'sort asc');
			$menu_array = $menu_array['data'];
		} else {
			$menu_array = $auth_model->getSiteMenuList([
				'site_id' => $baseSiteHome->siteId,
				'port' => 'SITEHOME',
				'name' => [
					'in',
					$baseSiteHome->groupInfo['array']
				],
				'is_menu' => 1
			], '*', 'sort asc');
			$menu_array = $menu_array['data'];
		}
		//追加SaaS模式下的应用中心菜单
		$saas_menu = hook('saasCenterMenu', []);
		if (!empty($saas_menu) && !empty($saas_menu[0])) {
			foreach ($saas_menu[0] as $k => $v) {
				$menu_array[] = $v;
			}
		}
		
		$baseSiteHome->setCrumbsNames();
		$baseSiteHome->menus = $baseSiteHome->parseMenu($menu_array);
		$baseSiteHome->menu_tree = list_to_tree($menu_array, 'name', 'menu_pid', 'child_list', '');
		
		//当前选择的菜单，包括所有子级
		$current_menu = [];
		
		if (!empty($baseSiteHome->crumbs)) {
			foreach ($baseSiteHome->crumbs as $k => $v) {
				foreach ($baseSiteHome->menu_tree as $ck => $cv) {
					if ($v['name'] == $cv['name']) {
						$current_menu = $cv;
						break;
					}
				}
			}
		}
		
		if (!empty($current_menu['child_list'])) {
			
			//查询四级菜单
			$fourth_menu = [];
			
			if (count($baseSiteHome->crumbs) >= 3) {
				foreach ($current_menu['child_list'] as $k => $v) {
					if (!empty($v['child_list'])) {
						foreach ($v['child_list'] as $ck => $cv) {
							
							if ($baseSiteHome->crumbs[2]['name'] == $cv['name']) {
								if (!empty($cv['child_list'])) {
									$fourth_menu = $cv['child_list'];
								}
							}
						}
					}
				}
			}
			
			$baseSiteHome->assign("current_menu", $current_menu['child_list']);
			
			//四级菜单
			$baseSiteHome->assign("fourth_menu", $fourth_menu);
			
		}
		
		//加载自定义设计页面（$current_menu['name'] == "APPLET_ROOT"，这个判断暂时这样处理）
		if ($baseSiteHome->addon == 'DiyView' || $current_menu['name'] == "APPLET_ROOT") {
			$show_type = 'H5';
			if (request()->action() && strpos(request()->action(), 'applet')) {
				$show_type = 'applet';
			}
			$diy_view = new DiyView();
			$view_list = $diy_view->getSiteDiyViewTempList($baseSiteHome->siteId, $show_type);
			$baseSiteHome->assign("addon_diy_view_list", $view_list);
		}
		
		$addon = ADDON_NAME ? ADDON_NAME . '://' : '';
		$url = strtolower($addon . URL_MODULE);
		$baseSiteHome->assign('current_url', $url);
		
		//针对自定义页面的查询展示
		if ($baseSiteHome->addon == '')
			$baseSiteHome->assign("menu_array", $menu_array);
		
		$baseSiteHome->assign('crumbs', $baseSiteHome->crumbs);
		$baseSiteHome->assign("addon_app", $baseSiteHome->siteInfo['addon_app']);
		$baseSiteHome->assign("saas_menu", $saas_menu);
		$baseSiteHome->assign('menus', $baseSiteHome->menus);
		$baseSiteHome->assign('menu_tree', $baseSiteHome->menu_tree);
		define("LOAD_MENU", 1);
	}
}