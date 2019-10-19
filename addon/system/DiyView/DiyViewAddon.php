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
namespace addon\system\DiyView;

use addon\system\DiyView\common\model\BottomNav;
use app\common\controller\BaseAddon;
use app\common\controller\BaseDiyView;
use app\common\model\DiyView;
use app\common\model\Site;

/**
 * 自定义模板插件
 */
class DiyViewAddon extends BaseAddon
{
	
	public $info = array(
		'name' => 'DiyView',
		'title' => '自定义模板',
		'description' => '设置系统自定义微页面',
		'status' => 1,
		'author' => '',
		'version' => '1.0',
		'visble' => 0,
		'type' => 'ADDON_SYSTEM',
		'category' => 'SYSTEM',
		'content' => '设置系统自定义微页面',
		//预置插件，多个用英文逗号分开
		'preset_addon' => '',
		'support_addon' => '',
		'support_app_type' => 'wap,weapp'
	);
	
	public $config;
	protected $replace = [];
	
	public function __construct()
	{
		parent::__construct();
		$this->config = $this->config_info;
		$this->replace = [
			'DIYVIEW_CSS' => __ROOT__ . '/addon/system/DiyView/sitehome/view/public/css',
			'DIYVIEW_JS' => __ROOT__ . '/addon/system/DiyView/sitehome/view/public/js',
			'DIYVIEW_IMG' => __ROOT__ . '/addon/system/DiyView/sitehome/view/public/img'
		];
		
	}
	
	/**
	 * 安装
	 */
	public function install()
	{
		$this->executeSql('install');
		return success();
	}
	
	/**
	 * 卸载
	 */
	public function uninstall()
	{
		return error('', 'System addon can not be uninstalled!');
//		return success();//测试用
	}
	
	/**
	 * 初始化站点数据，在添加站点的时候用
	 *
	 * @param integer $site_id
	 * @return boolean
	 */
	public function addToSite($site_id)
	{
		return success();
	}
	
	/**
	 * 删除站点数据--删除站点时调用
	 *
	 * @param integer $site_id
	 * @return boolean
	 */
	public function delFromSite($site_id)
	{
		return success();
	}
	
	/**
	 * 复制站点数据--复制站点时调用
	 *
	 * @param integer $site_id
	 * @param integer $new_site_id
	 * @return boolean
	 */
	public function copyToSite($site_id, $new_site_id)
	{
		return success();
	}
	
	public function getTypeName($type)
	{
		$arr = [
			'SYSTEM' => '系统组件',
			'ADDON' => '营销插件',
			'OTHER' => '其他插件',
		];
		return $arr[ $type ];
	}
	
	/**
	 * 后台编辑钩子
	 * 创建时间：2018年7月9日18:01:57
	 */
	public function diy($param = [])
	{
		$diy_view = new DiyView();
		$condition = array();
		if (!empty($param['name'])) {
			// 查询公共组件和当前的
			$condition['support_diy_view'] = [
				"like",
				[
					$param['name'],
					'%' . $param['name'] . ',%',
					'%' . $param['name'],
					'%,' . $param['name'] . ',%',
					''
				],
				"or"
			];
		} else {
			//查询公共系统组件
			$condition['support_diy_view'] = '';
			$condition['type'] = "SYSTEM";
		}
		
		$site_model = new Site();
		$site_info = $site_model->getSiteInfo([
			'site_id' => request()->siteid()
		]);
		$site_info = $site_info['data'];
		
		//查询当前站点支持的插件
		$condition['addon_name'] = [ 'in', $site_info['addon_modules'] ];
		
		// 自定义模板组件集合
		$utils = $diy_view->getDiyViewUtilList($condition);
		
		$show_type = isset($param['show_type']) ? $param['show_type'] : input("show_type", "H5");
		$diy_view_info = array();
		if (!empty($param)) {
			if (!empty($param['id'])) {
				$diy_view_info = $diy_view->getSiteDiyViewDetail([
					'nsdv.site_id' => request()->siteid(),
					'id' => $param['id']
				]);
			} elseif (!empty($param['addon_name']) && !empty($param['name'])) {
				$condition = [
					'nsdv.name' => $param['name'],
					'nsdv.addon_name' => $param['addon_name'],
					'site_id' => request()->siteid(),
					'show_type' => $show_type
				];
				$diy_view_info = $diy_view->getSiteDiyViewDetail($condition);
			} elseif (!empty($param['name'])) {
				$condition = [
					'nsdv.name' => $param['name'],
					'site_id' => request()->siteid(),
					'show_type' => $show_type
				];
				$diy_view_info = $diy_view->getSiteDiyViewDetail($condition);
			}
			
			if (!empty($diy_view_info)) {
				$diy_view_info = $diy_view_info['data'];
			}
		}
		
		$diy_view_info['show_type'] = $show_type;
		
		if (!empty($diy_view_info) && !empty($diy_view_info['value'])) {
			
			//转义斜杠，保证JavaScript中转换对象成功
			$diy_view_info['value'] = str_replace('\"', "\'", $diy_view_info['value']);
		}
		
		$diy_view_utils = array();
		if (!empty($utils['data'])) {
			
			// 先遍历，组件分类
			foreach ($utils['data'] as $k => $v) {
				$value = array();
				$value['type'] = $v['type'];
				$value['type_name'] = $this->getTypeName($v['type']);
				$value['list'] = [];
				if (!in_array($value, $diy_view_utils)) {
					array_push($diy_view_utils, $value);
				}
			}
			
			// 遍历每一个组件，将其添加到对应的分类中
			foreach ($utils['data'] as $k => $v) {
				foreach ($diy_view_utils as $diy_k => $diy_v) {
					if ($diy_v['type'] == $v['type']) {
						array_push($diy_view_utils[ $diy_k ]['list'], $v);
					}
				}
			}
		}
		
		//查询当前编辑的自定义模板是否有整体参数设置
		$head_edit = "";
		if (!empty($diy_view_info['addon_name'])) {
			
			$port = [ 'system', 'module', 'app' ];
			$is_exist = false;
			$class_name = '';
			foreach ($port as $k => $v) {
				$class_name = "\\addon\\" . $v . "\\" . $diy_view_info['addon_name'] . "\\component\\controller\\Design";
				if (class_exists($class_name)) {
					$is_exist = true;
					break;
				}
			}
			
			if ($is_exist) {
				$design = new $class_name();
				$head_edit = $design->headEdit();
			}
			
		}
		
		$addon_name = !empty($param['addon_name']) ? $param['addon_name'] : "";
		if (empty($addon_name)) {
			$site_model = new Site();
			$site_info = $site_model->getSiteInfo([
				'site_id' => request()->siteid()
			]);
			$site_info = $site_info['data'];
			$addon_name = $site_info['addon_app'];
		}
		
		$this->assign("name", $param['name']);
		$this->assign("addon_name", $addon_name);
		$this->assign("time", time());
		$this->assign('diy_view_utils', $diy_view_utils);
		$this->assign("diy_view_info", $diy_view_info);
		$this->assign("head_edit", $head_edit);
		
		return $this->fetch('sitehome/index/index', [], $this->replace);
	}
	
	/**
	 * 渲染输出单个后台组件代码钩子
	 * 创建时间：2018年7月9日14:09:04
	 *
	 * @param unknown $param
	 */
	public function diyUtils($param = [])
	{
		$port = [ 'system', 'module', 'app' ];
		if (!empty($param['controller'])) {
			$class_name = '';
			$is_exist = false;
			foreach ($port as $k => $v) {
				$class_name = '\\addon\\' . $v . '\\' . $param['addon_name'] . '\\component\\controller\\' . $param['controller'];
				if (class_exists($class_name)) {
					$is_exist = true;
					break;
				}
			}
			
			if ($is_exist) {
				$class = new \ReflectionClass($class_name);
				$instance = $class->newInstanceArgs();
				//传入当前的常量
				$instance->incoming_replace = $this->replace;
				return $instance->edit();
			} else {
				var_dump("not found：" . $class_name);
			}
			
		}
	}
	
	/**
	 * 前台渲染输出钩子
	 * 创建时间：2018年7月9日14:28:43
	 *
	 * @param [] $param
	 */
	public function diyFetch($param = [])
	{
		$param['site_id'] = request()->siteid();
		$base_diy_view = new BaseDiyView();
		return $base_diy_view->fetchHtml($param);
	}
	
	/**
	 * 渲染自定义底部导航
	 * @param array $param
	 */
	public function diyBottomNavFetch($param = [])
	{
		$param['site_id'] = request()->siteid();
		$param['addon_name'] = request()->addon();
		$bottom_nav = new BottomNav();
		$bottom_nav_config = $bottom_nav->getBottomNavConfig($param['site_id'], $param['addon_name']);
		$bottom_nav_info = array();
		if (!empty($bottom_nav_config['data']['value'])) {
			$bottom_nav_info = json_decode($bottom_nav_config['data']['value'], true);
		}
		
		$is_show = false;
		if (!empty($param['openBottomNav'])) {
			$is_show = true;
		}
		
		$method = strtolower(request()->controller() . "/" . request()->action());
		if (!empty($bottom_nav_info['showPage'])) {
			foreach ($bottom_nav_info['showPage'] as $k => $v) {
				if (strpos($v['h5_url'], $method) !== false) {
					$is_show = true;
					break;
				}
			}
		}

//		if ($is_show) {
		
		$this->assign("method", $method);
		$this->assign("bottom_nav_info", $bottom_nav_info);
		$template = ADDON_SYSTEM_PATH . 'DiyView/sitehome/view/design/bottom_nav.html';
		return $this->fetch($template, [], $this->replace);
//		}
	}
	
}