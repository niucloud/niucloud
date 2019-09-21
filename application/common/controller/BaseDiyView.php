<?php

namespace app\common\controller;

use app\common\model\DiyView;
use think\Config;
use think\View;

class BaseDiyView extends BaseSite
{
	
	private $replace;
	
	// 当前插件路径
	private $addon_path;
	
	// 绑定接口地址
	protected $bind_api;
	
	public $param;
	
	//传过来的replace
	public $incoming_replace = [];
	
	
	/**
	 * 解析HTML代码
	 *
	 * @param $attr 自定义模板属性
	 */
	public function parseHtml($attr)
	{
	}
	
	/**
	 * 后台编辑界面
	 * 创建时间：2018年7月9日12:12:55
	 */
	public function edit()
	{
	}
	
	public function __construct()
	{
		parent::__construct();
		
		//重新实例化view，防止常量被覆盖，导致替换错误，例如RESOURCE_PATH常量
		$this->view = new View(Config::get('template'), Config::get('view_replace_str'));
		$class = get_class($this);
		$arr = explode('\\', $class);
		$this->addon_path = ADDON_PATH . $arr[1] . DS . $arr[2] . DS;
		$this->replace = [
			'RESOURCE_PATH' => __ROOT__ . '/addon/' . $arr[1] . "/" . $arr[2],
			'ADDON_MODULE' => ADDON_MODULE
		];
	}
	
	public function setParam($param)
	{
		$this->param = $param;
	}
	
	/**
	 * 渲染HTML代码
	 * 创建时间：2018年7月9日14:23:46
	 *
	 * @param array $param
	 */
	final public function fetchHtml($param = [])
	{
		$diy_view = new DiyView();
		if ($param['site_id'] > 0 && !empty($param['name'])) {
			$diy_view_info = $diy_view->getSiteDiyViewDetail([
				'nsdv.site_id' => $param['site_id'],
				'nsdv.name' => $param['name'],
			]);
		}
		
		$body = "";
		$bind_api_arr = array();
		$global = [];//全局属性
		if (!empty($diy_view_info['data'])) {
			if (!empty($diy_view_info['data']['value'])) {
				$obj = json_decode($diy_view_info['data']['value'], true);
				$global = $obj['global'];
				if (!empty($obj['value'])) {
					foreach ($obj['value'] as $k => $v) {
						$instance = $this->getComponentClass($v['addon_name'], $v['controller']);
						if (!empty($instance)) {
							if (!empty($instance->bind_api)) {
								$bind_api_arr = array_merge($bind_api_arr, $instance->bind_api);
							}
						}
					}
				}
				
				// 查询接口数据
				if (!empty($bind_api_arr)) {
					$bind_api_arr = array_unique($bind_api_arr); // 去重
					foreach ($bind_api_arr as $k => $v) {
						$api_data = api($v, $param['data']);
						$assign_name = explode(".", $v);
						if (!empty($api_data)) {
							if ($api_data['code'] == 0) {
								$this->assign($assign_name[ count($assign_name) - 1 ], $api_data);
							} else {
//                                var_dump("接口发生错误：" . $api_data['message']);
//                                throw exception("接口发生错误：" . $api_data['message']);
							}
						}
					}
				}
				
				if (!empty($obj['value'])) {
					foreach ($obj['value'] as $k => $v) {
						$instance = $this->getComponentClass($v['addon_name'], $v['controller']);
						if (!empty($instance)) {
							$instance->setParam($param['data']);
							//页面输出自定义模板属性
							$v['random_flag'] = unique_random() . "_" . $k;//随机标识，防重复标识
							$instance->assign("attr", $v);
							$instance->assign('API_URL', API_URL);
							$instance->assign("site_info", $this->site_info);
							//当前应用插件名称
							$instance->assign("addon_name", $diy_view_info['data']['addon_name']);
							$instance->assign("access_token", $this->access_token);
							$body .= $instance->parseHtml($v);
						}
					}
				}
			}
		}
		
		$this->assign('API_URL', API_URL);
		$this->assign("body", $body);
		$this->assign("global", $global);
		$this->assign("site_info", $this->site_info);
		//将会员相关信息传入到界面上
		$this->initMember();
		$template = ADDON_SYSTEM_PATH . 'DiyView/sitehome/view/index/container.html';
		
		$this->assign('wap_style', "wap@style/$this->wap_style/base");
		return parent::fetch($template, [], $this->replace);
	}
	
	private function getComponentClass($addon_name, $controller)
	{
		$instance = null;
		$path = '\\addon\\system\\' . $addon_name . "\\component\\controller\\" . $controller;
		if (!class_exists($path)) {
			$path = '\\addon\\module\\' . $addon_name . "\\component\\controller\\" . $controller;
		}
		
		if (class_exists($path)) {
			$class = new \ReflectionClass($path);
			$instance = $class->newInstanceArgs();
			return $instance;
		} else {
			var_dump("not found：" . $path);
			echo '<br/>';
		}
		return $instance;
	}
	
	/**
	 * 获取类
	 *
	 * @param string $method
	 */
	private function getdata($method, $params)
	{
		$params['site_id'] = request()->siteid();
		$method_array = explode('.', $method);
		
		if ($method_array[0] == 'System') {
			$class_name = 'app\\api\\controller\\' . $method_array[1];
			if (!class_exists($class_name)) {
				return error();
			}
			$api_model = new $class_name($params);
		} else {
			
			$class_name = "addon\\module\\{$method_array[0]}\\api\\controller\\" . $method_array[1];
			
			if (!class_exists($class_name)) {
				$class_name = "addon\\app\\{$method_array[0]}\\api\\controller\\" . $method_array[1];
			}
			
			if (!class_exists($class_name)) {
				return error();
			}
			$api_model = new $class_name($params);
		}
		$data = $api_model->$method_array[2]($params);
		return $data;
	}
	
	/**
	 * 加载模板输出
	 *
	 * @access protected
	 * @param string $template
	 *            模板文件名
	 * @param array $vars
	 *            模板输出变量
	 * @param array $replace
	 *            模板替换
	 * @param array $config
	 *            模板参数
	 * @return mixed
	 */
	protected function fetch($template = '', $vars = [], $replace = [], $config = [])
	{
		//合并将传过来的replace
		if (!empty($this->incoming_replace)) {
			$this->replace = array_merge($this->replace, $this->incoming_replace);
		}
		if (empty($replace)) {
			$replace = $this->replace;
		} else {
			$replace = array_merge($this->replace, $replace);
		}
		
		$template = $this->addon_path . "component/view/" . $template;
		return $this->view->fetch($template, $vars, $replace, $config);
	}
}