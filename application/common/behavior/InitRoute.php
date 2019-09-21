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
namespace app\common\behavior;

use think\Route;
use think\Request;
use think\App;
use app\common\model\Site;
use app\common\model\Addon;
use think\Lang;
use think\Cache;

/**
 * 初始化路由规则
 */
class InitRoute
{
	// 行为扩展的执行入口必须是run
	public function run()
	{
		if (defined('BIND_MODULE') && BIND_MODULE === 'install')
			return;
		$base_file = Request::instance()->baseFile();
		$is_pay = strpos($base_file, 'pay.php');
		
		if ($is_pay) {
			$namespace = 'addon\\system\\Pay';
			$addon_path = ADDON_PATH . DS . 'system' . DS . 'Pay' . DS;
			App::$namespace = $namespace;
			App::$modulePath = $addon_path . 'common' . DS;
			Route::bind('common/pay/callback');
			return;
			
		}
		//查询所有插件
		$addon_model = new Addon();
		$addon_data = $addon_model->getAddons();
		$addons = $addon_data['addons'];
		$keys = array_keys($addons);
		$site_model = new Site();
		$domains = $site_model->getSiteDomains();
		$domains = $domains['data'];
		//获取当前网址的pathinfo信息
		$pathinfo = Request::instance()->pathinfo();
		$pathinfo_array = explode('/', $pathinfo);
		//获取当前域名用于检测当前域名是否是站点绑定域名
		$domain = request()->domain();
		//检测是否输入了站点id,获取站点id以及站点基础信息
		if (preg_match('/^s\d{1,11}$/', $pathinfo_array[0])) {
			$site_id = ltrim($pathinfo_array[0], 's');
			if (!is_numeric($site_id)) {
				$site_id = 0;
			} else {
				$site_id = intval($site_id);
			}
			request()->siteid($site_id);
			$site_info = $site_model->getSiteInfo([ 'site_id' => $site_id ]);
			
			if (empty($site_info['data']) || $site_info['data'] == null) {
				die("站点不存在，请检测服务器配置");
			} else {
				$addon_app = $site_info['data']['addon_app'];
				request()->siteAddon($site_info['data']['addon_app']);
			}
			
			$check_model = $pathinfo_array[1];
			$tag = 1;
			
		} else {
			//检测是否存在绑定站点域名
			if (array_key_exists($domain, $domains)) {
				if (empty($site_id) || $domains[ $domain ] != $site_id) {
					request()->siteid($domains[ $domain ]);
					$site_id = $domains[ $domain ];
					$site_info = $site_model->getSiteInfo([ 'site_id' => $domains[ $domain ] ]);
					$addon_app = $site_info['data']['addon_app'];
					request()->siteAddon($site_info['data']['addon_app']);
					if (!empty($site_info['data']['default_link']) && empty($pathinfo_array[0])) {
						$url_action = url_action($site_info['data']['default_link']);
						
						if (!empty($url_action['addon'])) {
							request()->addon($url_action['addon']);
							$addon_model = new Addon();
							$addon_info = $addon_model->getAddonInfo([ 'name' => $url_action['addon'] ]);
							$addon_info = $addon_info['data'];
							if (!empty($addon_info['type']) && $addon_info['type'] == 'ADDON_APP') {
								$namespace = 'addon\\app\\' . $url_action['addon'];
								$addon_path = ADDON_PATH . 'app\\' . $url_action['addon'] . DS;
							} elseif (!empty($addon_info['type']) && $addon_info['type'] == 'ADDON_SYSTEM') {
								$namespace = 'addon\\system\\' . $url_action['addon'];
								$addon_path = ADDON_PATH . 'system\\' . $url_action['addon'] . DS;
								
							} else {
								$namespace = 'addon\\module\\' . $url_action['addon'];
								$addon_path = ADDON_PATH . 'module\\' . $url_action['addon'] . DS;
							}
							
							App::$namespace = $namespace;
							App::$modulePath = $addon_path . $url_action['model'] . DS;
							Route::bind($url_action['model'] . '/' . $url_action['controller'] . '/' . $url_action['action']);
						} else {
							Route::bind($url_action['model'] . '/' . $url_action['controller'] . '/' . $url_action['action']);
						}
						return;
						
					}
					$check_model = $pathinfo_array[0];
					$tag = 0;
				}
			} else {
				$version_data = $this->sysVersion(SYS_VERSION);
				if ($version_data['site_id'] != 0) {
					$site_id = $version_data['site_id'];
					$addon_app = $version_data['addon_app'];
					request()->siteid($site_id);
					request()->siteAddon($version_data['addon_app']);
					$check_model = $pathinfo_array[0];
					$tag = 0;
				} else {
					//查询第一个站点
					$cache_site = Cache::get("default_site_id");
					if (empty($cache_site)) {
						$site_info = $site_model->getFirstSite();
						$site_id = $site_info['data']['site_id'];
						$addon_app = $site_info['data']['addon_app'];
						Cache::set("default_site_id", $site_id);
						request()->siteid($site_id);
						request()->siteAddon($site_info['data']['addon_app']);
					} else {
						$site_info = $site_model->getSiteInfo([ 'site_id' => $cache_site ]);
						$site_id = $site_info['data']['site_id'];
						$addon_app = $site_info['data']['addon_app'];
						Cache::set("default_site_id", $site_id);
						request()->siteid($site_id);
						request()->siteAddon($site_info['data']['addon_app']);
					}
					
					$check_model = $pathinfo_array[0];
					$tag = 0;
				}
				
			}
			
		}
		$check_model = str_replace(".html", "", $check_model);
		$key_model = strtolower($check_model);
		//判断当前输入的域名网址是否是插件或者应用的网址
		if (in_array($key_model, $keys)) {
			//对应插件网址,对应的模块控制器以及方法是对应插件后边网址部分
			$model_path = $pathinfo_array[ $tag + 1 ];
			$controller_path = isset($pathinfo_array[ $tag + 2 ]) ? $pathinfo_array[ $tag + 2 ] : '';
			$action_path = isset($pathinfo_array[ $tag + 3 ]) ? $pathinfo_array[ $tag + 3 ] : '';
			$addon_class_name = get_addon_class($addons[ $key_model ]);
			$addon_class = new $addon_class_name();
			$config_info = $addon_class->config_info;
			// 定义应用目录
			if (!empty($addons[ $key_model ])) {
				request()->addon($addons[ $key_model ]);
				$addon_model = new Addon();
				$addon_info = $addon_model->getAddonInfo([ 'name' => $addons[ $key_model ] ]);
				$addon_info = $addon_info['data'];
			} else {
				$addon_info = [];
			}
			
			if (!empty($addon_info['type']) && $addon_info['type'] == 'ADDON_APP') {
				$namespace = 'addon\\app\\' . $addons[ $key_model ];
				$addon_path = ADDON_PATH . 'app\\' . $addons[ $key_model ] . DS;
			} elseif (!empty($addon_info['type']) && $addon_info['type'] == 'ADDON_SYSTEM') {
				$namespace = 'addon\\system\\' . $addons[ $key_model ];
				$addon_path = ADDON_PATH . 'system\\' . $addons[ $key_model ] . DS;
				
			} else {
				$namespace = 'addon\\module\\' . $addons[ $key_model ];
				$addon_path = ADDON_PATH . 'module\\' . $addons[ $key_model ] . DS;
			}
			App::$namespace = $namespace;
			
			//查询插件对应的默认模块控制器以及方法
			if (empty($model_path)) {
				$model_path = isset($config_info['default_module']) ? $config_info['default_module'] : 'index';
			}
			if (empty($controller_path)) {
				$controller_path = isset($config_info['default_controller']) ? $config_info['default_controller'] : 'index';
			}
			if (empty($action_path)) {
				$action_path = isset($config_info['default_action']) ? $config_info['default_action'] : 'index';
			}
			
			App::$modulePath = $addon_path . ($model_path ? $model_path . DS : '');
			
		} else {
			//查询站点域名中对应的模块控制器以及方法
			$model_path = isset($pathinfo_array[ $tag ]) ? $pathinfo_array[ $tag ] : '';
			$controller_path = isset($pathinfo_array[ $tag + 1 ]) ? $pathinfo_array[ $tag + 1 ] : '';
			$action_path = isset($pathinfo_array[ $tag + 2 ]) ? $pathinfo_array[ $tag + 2 ] : '';
			//针对只输入域名以及站点或者只输入站点域名处理路由
			if (empty($model_path)) {
				$addon_class_name = get_addon_class($addon_app);
				
				$addon_class = new $addon_class_name();
				$config_info = $addon_class->config_info;
				
				// 定义应用目录
				request()->addon($addon_app);
				request()->siteAddon($addon_app);
				$addon_path = ADDON_PATH . 'app' . DS . $addon_app . DS;
				$namespace = 'addon\\app\\' . $addon_app;
				App::$namespace = $namespace;
				
				if (empty($model_path)) {
					$model_path = isset($config_info['default_module']) ? $config_info['default_module'] : 'index';
				}
				
				if (empty($controller_path)) {
					$controller_path = isset($config_info['default_controller']) ? $config_info['default_controller'] : 'index';
				}
				
				if (empty($action_path)) {
					$action_path = isset($config_info['default_action']) ? $config_info['default_action'] : 'index';
				}
				
				App::$modulePath = $addon_path . ($model_path ? $model_path . DS : '');
				
			} else {
				if (empty($controller_path)) {
					$controller_path = config('default_controller');
				}
				
				if (empty($action_path)) {
					$action_path = config('default_action');
				}
				
				
			}
		}
		$model_path = str_replace(".html", "", $model_path);
		$controller_path = str_replace(".html", "", $controller_path);
		$action_path = str_replace(".html", "", $action_path);
		Route::bind($model_path . '/' . $controller_path . '/' . $action_path);
		
	}
	
	/**
	 * 对应模式先应用不创建站点
	 * @param unknown $type
	 */
	private function sysVersion($type)
	{
		
		$version = [
			'NIUCLOUD' => [                    //独立部署模式，用于开源授权用户
				'site_id' => 0,              //默认站点id  0表示没有，按照第一个站点
				'addon_app' => '',           //默认网站应用   空表示没有按照系统第一个,key值默认与addon_app相同
				''
			],
		];
		return $version[ $type ];
	}
	
}

