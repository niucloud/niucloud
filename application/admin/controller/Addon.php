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

namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\model\Addon as AddonModel;
use app\common\model\Site;
use think\Exception;
use util\addon\AddonService;

/**
 * 插件  控制器
 */
class Addon extends BaseAdmin
{
	
	public function index()
	{
		$this->redirect('addon/addonList');
	}
	
	/**
	 * 应用插件
	 */
	public function addonList()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = 0;//查询全部
			$type = input('type', '');
			$install_type = input('install_type', '');
			$addon_name = input('addon_name', '');
			$addons_model = new AddonModel();
			$where = [];
			$where['type'] = $type;
			$where['install_type'] = $install_type;
			$where['addon_name'] = $addon_name;
			$data = $addons_model->getAdminAddonList($page, $limit, $where);
			$list = [];
			if (!empty($data['data']['list'])) {
				
				$addon_type = config("addon_type");
				if (empty($type)) {
					foreach ($addon_type as $k => $v) {
						$value = array();
						$value['type'] = $k;
						$value['type_name'] = $v;
						$value['list'] = [];
						array_push($list, $value);
					}
				} else {
					$value = array();
					$value['type'] = $type;
					$value['type_name'] = $addon_type[ $type ];
					$value['list'] = [];
					array_push($list, $value);
				}
				
				//遍历集合，将其添加到对应的分类中
				foreach ($data['data']['list'] as $k => $v) {
					$v["support_app_type"] = getSupportPort($v["support_app_type"]);
					foreach ($list as $ck => $cv) {
						if ($cv['type'] == $v['type']) {
							array_push($list[ $ck ]['list'], $v);
						}
					}
				}
			}
			return $list;
		}
		$request_type = input("type", '');
		$this->assign("type", $request_type);
		
		$addon_type = config("addon_type");
		$this->assign("addon_type", $addon_type);
		
		return $this->fetch('addon/addon_list');
	}
	
	/**
	 * 安装插件
	 */
	public function install()
	{
		if (IS_AJAX) {
			$addon_model = new AddonModel();
			$addon_name = trim(input('addon_name', ''));
			$is_buy = input('is_buy', 0);
			$version = input('version', '');
			if (!$addon_name) {
				return error('', 'ADDON_MISSIMG_ADDON_NAME');
			} else {
				$addon_class = get_addon_class($addon_name);
				if (!class_exists($addon_class)) {
					if ($is_buy == 1) {
						$auth = get_auth();
						$addon_service = new AddonService($auth['app_key'], $auth['app_secret']);
						try {
							$addon_service->install($addon_name, [ 'addon_version' => $version, 'type' => 'addon' ]);
						} catch (Exception $e) {
							return error($e->getMessage());
						}
					}
				}
			}
			
			$res = $addon_model->install($addon_name);
			return $res;
		}
	}
	
	/**
	 * 卸载插件
	 */
	public function uninstall()
	{
		if (IS_AJAX) {
			$addon_model = new AddonModel();
			$addon_name = trim(input('addon_name', ''));
			if (!$addon_name) {
				return error('', 'ADDON_UNINSTALL_FAIL');
			}
			$res = $addon_model->uninstall($addon_name);
			return $res;
		}
	}
	
	/**
	 * 升级插件
	 */
	public function upgrade()
	{
		if (IS_AJAX) {
			$addon_model = new AddonModel();
			$addon_name = input('addon_name', '');
			$is_buy = input('is_buy', '');
			$version = input('version', '');
			if (!$addon_name) {
				return error('', 'ADDON_UNINSTALL_FAIL');
			}
			
			//判断升级包是否存在
			$addon_info = model('nc_addon')->getInfo([ 'name' => $addon_name ]);
			if ($addon_info['type'] == 'ADDON_MODULE') {
				$root_path = ADDON_MODULE_PATH;
			} else {
				$root_path = ADDON_APP_PATH;
			}
			$update_dir = 'update';
			$upgrade_dir = $addon_name . '/' . $update_dir . '/' . $version;//升级根目录
			if (!file_exists($root_path . $upgrade_dir)) {
				if ($is_buy == 1) {
					$auth = get_auth();
					$addon_service = new AddonService($auth['app_key'], $auth['app_secret']);
					$addon_service->upgrade($addon_name, [ 'addon_version' => $version, 'type' => 'update' ]);
				}
			}
			$res = $addon_model->upgrade($addon_name, $version);
			return $res;
		}
	}
	
	/**
	 * 查看应用信息
	 */
	public function detail()
	{
		if (IS_AJAX) {
			$addon_model = new AddonModel();
			$addon_name = trim(input('addon_name', ''));
			if (!$addon_name) {
				return error('', 'ADDON_UNINSTALL_FAIL');
			}
			$addon_info = $addon_model->getAddonInfo([ 'name' => $addon_name ]);
			$addon_info['data']['create_time'] = date('Y-m-d H:i:s', $addon_info['data']['create_time']);
			return $addon_info;
		}
	}
	
	/**
	 * 版本更新信息
	 */
	public function versionInfo()
	{
		if (IS_AJAX) {
			$addon_model = new AddonModel();
			$addon_name = trim(input('addon_name', ''));
			if (!$addon_name) {
				return error('', 'ADDON_UNINSTALL_FAIL');
			}
			$addon_info = $addon_model->getAddonInfo([ 'name' => $addon_name ]);
			$addon_info['data']['create_time'] = date('Y-m-d H:i:s', $addon_info['data']['create_time']);
			return $addon_info;
		}
	}
	
	/**
	 * 重置菜单
	 */
	public function initMenu()
	{
		$addon_name = input('addon_name', '');
		if (!$addon_name) {
			return error('', 'ADDON_UNINSTALL_FAIL');
		}
		$addon_model = new AddonModel();
		$res = $addon_model->initAddonMenu($addon_name);
		return $res;
	}
	
	/**
	 * 插件市场
	 */
	public function market()
	{
		//暂忽略加密
		$this->app_key;
		$this->app_secret;
		$url = 'https://www.niucloud.com/?s=/s1/ncweb/web/index/market?ak=' . $this->app_key . '&as=' . $this->app_secret;
		Header("Location: $url");
		exit;
	}
	
	/**
	 * 插件详情
	 * @return
	 */
	public function addonDetail()
	{
		if (IS_AJAX) {
			$name = input('name', '');
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$site = new Site();
			$condition = [
				'ns.addon_modules' => [ 'like', '%' . $name . '%' ],
				'nsu.uid' => UID
			];
			$list = $site->getSitePageListByUid($condition, $page, $limit);
			return $list;
		}
		
		$name = input('name', '');
		$addons_model = new AddonModel();
		$info = $addons_model->getAddonInfo([ 'name' => $name ]);
		if (empty($info['data'])) {
			$class = get_addon_class($name);
			if (class_exists($class)) { // 实例化插件失败忽略执行
				$obj = new $class();
				$info = [
					'data' => $obj->info
				];
				$icon = "./addon/system/" . $name . "/icon.png";
				if (!file_exists($icon)) {
					$icon = "./addon/module/" . $name . "/icon.png";
				}
				if (!file_exists($icon)) {
					$icon = "./addon/app/" . $name . "/icon.png";
				}
				$info['data']['icon'] = $icon;
				$info['data']['status'] = 0;
			}
		}
		if (empty($info['data'])) $this->error('未获取到插件信息');
		
		$info['data']['support_app_type_arr'] = getSupportPort($info['data']['support_app_type']);
		$this->assign('info', $info['data']);
		return $this->fetch('addon/addon_detail');
	}
	
}