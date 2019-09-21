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
namespace app\sitehome\controller;

use app\common\controller\BaseSiteHome;
use app\common\model\Addon;
use app\common\model\Config;
use app\common\model\Site;

/**
 * 我的插件
 */
class Addons extends BaseSiteHome
{
	
	public $app_key = '';
	public $app_secret = '';
	
	public function __construct()
	{
		parent::__construct();
		$config_model = new Config();
		$auth_info = $config_model->getConfigInfo([ 'name' => 'SYSTEM_AUTH_CONFIG' ]);
		$app_config = json_decode($auth_info['data']['value'], true);
		$this->app_key = $app_config['app_key'];
		$this->app_secret = $app_config['app_secret'];
	}
	
	/**
	 * 会员购买的插件列表
	 */
	public function lists()
	{
		$addon = new Addon();
		$category_result = $addon->getAddonCategoryList();
		$addons_list = $addon->getSiteAddonList($this->siteId);
		
		$category_list = $category_result["data"];
		if (!empty($category_list) && !empty($addons_list["data"])) {
			foreach ($addons_list["data"] as $k => $v) {
				
				foreach ($category_list as $category_k => $category_v) {
					if ($category_v["category_name"] == $v["category"]) {
						
						$addon_info_result = $addon->getAddonInfo([ "name" => $v["name"] ]);
						$addon_info = $addon_info_result["data"];
						
						$v["support_app_type"] = getSupportPort($addon_info["support_app_type"]);
						$category_list[ $category_k ]["addon_list"][] = $v;
						
						break 1;
					}
				}
			}
			
		}
		$this->assign("is_system", $this->groupInfo['is_system']);
		$this->assign("category_list", $category_list);
		return $this->fetch('addons/lists');
	}
	
	/**
	 * 启动单个插件(针对站点已购买但是未启动的插件)
	 */
	public function setUp()
	{
		$name = input('name', '');
		$site_model = new Site();
		$res = $site_model->addSiteModule($this->siteId, $name);
		if ($res['code'] != 0) {
			return error('', $res['message']);
		}
		if (empty($res['data']['url'])) {
			return success('启动成功');
		} else {
			return success(addon_url($res['data']['url']));
		}
		
	}
	
	/**
	 * 撤销单个插件
	 */
	public function unSetUp()
	{
		$name = input('name', '');
		$site_model = new Site();
		$res = $site_model->deleteSiteModule($this->siteId, $name);
		if ($res['code'] != 0) {
			return error('', $res['message']);
		}
		return success('卸载成功');
		
	}
	
	/**
	 * 应用中心
	 */
	public function service()
	{
		if (IS_AJAX) {
			$addon = new Addon();
			$page = input('page', 1);
			$page_size = input('limit', PAGE_LIST_ROWS);
			$search_text = input('search_text', 0);
			$search_type = input('search_type', '');
			$condition = [
				'nua.uid' => UID
			];
			if ($search_text) {
				$condition['na.title'] = [ 'like', '%' . $search_text . '%' ];
			}
			if ($search_type) {
				if ($search_type == 1) {
					$condition['nua.status'] = 1;
				} else if ($search_type == 2) {
					$condition['nua.status'] = 0;
				}
			}
			$join = [
				[
					'nc_addon na',
					'nua.addon_name = na.name',
					'INNER'
				],
			];
			$addons_list = $addon->getUserAddonPageList($condition, $page, $page_size, 'nua.create_time desc', 'nua.*, na.title', 'nua', $join);
			$addons_list_new = $addons_list;
			foreach ($addons_list['data']['list'] as $key => $val) {
				$val['expire_time'] = $val['validity_time'] != 0 ? date('Y-m-d H:i:s', $val['validity_time']) : '永久';
				if ($val['status'] == 0) {
					$status_name = '已过期';
				} else if ($val['status'] == 1 && ($val['validity_time'] < time() + 3600 * 24 * 30) && $val['validity_time'] != 0) {
					$status_name = '即将过期';
					$val['status'] = 2;
				} else {
					$status_name = '正常运行';
				}
				$val['status_name'] = $status_name;
				$addons_list_new['data']['list'][ $key ] = $val;
			}
			return $addons_list_new;
		}
		return $this->fetch('addons/service');
	}
	
	/**
	 * 插件详情
	 */
	public function info()
	{
		$addon_name = input('name', '');
		$addon_model = new Addon();
		$addon_info = $addon_model->getAddonInfo([ 'name' => $addon_name ]);
		$this->assign('info', $addon_info['data']);
		return $this->fetch('addons/info');
	}
	
	/**
	 * 应用市场
	 */
	public function market()
	{
		//暂忽略加密
		$this->app_key;
		$this->app_secret;
		$url = 'https://www.niucloud.com/?s=/s1/ncweb/web/index/market?ak=' . $this->app_key . '&as=' . $this->app_secret;
		//         $url = 'http://localhost/Niucloud/?s=/s1/ncweb/web/index/market?ak='.$this->app_key.'&as='.$this->app_secret;
		Header("Location: $url");
		exit;
	}
}