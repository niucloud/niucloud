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

use think\Cache;
use think\Loader;
use app\common\model\Addon;

/**
 * 初始化基础信息
 */
class InitBase
{
	/**
	 * 初始化行为入口  入口必须是run
	 */
	public function run()
	{
		
		// 初始化常量
		$this->initConst();
		
		//初始化配置信息
		$this->initConfig();
		
		// 注册命名空间
		$this->registerNamespace();
	}
	
	/**
	 * 初始化常量
	 */
	private function initConst()
	{
		//加载版本信息
		include_once APP_PATH . 'version.php';
		//加载基础化配置信息
		define('__ROOT__', str_replace([ '/index.php', '/install.php' ], '', \think\Request::instance()->root(true)));
		define('__PUBLIC__', __ROOT__ . '/public');
		define('__UPLOAD__', 'attachment');
		define('API_URL', \think\Request::instance()->root(true));//API域名,默认当前路径
		//define('API_URL'             , 'http://127.0.0.1');//API域名,默认当前路径
		define('SOURCE_URL', '');//来源域名，用于进行检查api安全，如果api网址与主网址不同需要专门配置主网站用于检测，主网站不进行秘钥检测，只传入站点key值
		//插件目录名称
		define('ADDON_DIR_NAME', 'addon');
		//插件目录路径
		define('ADDON_PATH', dirname(realpath(APP_PATH)) . DS . ADDON_DIR_NAME . DS);
		//应用插件 携带域名的路径
		define('ADDON_APP', __ROOT__ . '/addon/app');
		//插件应用路径
		define('ADDON_APP_PATH', dirname(realpath(APP_PATH)) . DS . ADDON_DIR_NAME . DS . 'app' . DS);
		//模块插件 携带域名的路径
		define('ADDON_MODULE', __ROOT__ . '/addon/module');
		//插件业务模块路径
		define('ADDON_MODULE_PATH', dirname(realpath(APP_PATH)) . DS . ADDON_DIR_NAME . DS . 'module' . DS);
		//插件系统模块路径
		define('ADDON_SYSTEM_PATH', dirname(realpath(APP_PATH)) . DS . ADDON_DIR_NAME . DS . 'system' . DS);
		//插件安装包路径
		define('ADDON_ZIP_PATH', dirname(realpath(APP_PATH)) . DS . 'attachment' . DS . 'addon' . DS);
		//分页每页数量
		define('PAGE_LIST_ROWS', 10);
		//返回成功
		define('RESULT_SUCCESS', 'success');
		//返回失败
		define('RESULT_ERROR', 'error');
		//定义反斜线
		define('SYS_DS_CONS', '\\');
		//定义控制器层名称
		define('LAYER_CONTROLLER_NAME', 'controller');
		//加载error_code文件
		include_once APP_PATH . 'extra/error_code.php';
		//伪静态模式是否开启
		define('REWRITE_MODULE', true);
		//兼容模式访问
		if (!REWRITE_MODULE) {
			\think\Url::root(\think\Request::instance()->root(true) . '/?s=');
		}
		
	}
	
	/**
	 * 初始化配置信息
	 */
	private function initConfig()
	{
		if (defined('BIND_MODULE') && BIND_MODULE === 'install') {
			return;
		}
		
		$config_array['view_replace_str'] = [
			'__ROOT__' => __ROOT__,
			'__STATIC__' => __PUBLIC__ . '/static',
			'STATIC_EXT' => __PUBLIC__ . '/static/ext',
			'STATIC_CSS' => __PUBLIC__ . '/static/css',
			'STATIC_JS' => __PUBLIC__ . '/static/js',
			'STATIC_IMG' => __PUBLIC__ . '/static/img',
			'SITEHOME_IMG' => __ROOT__ . '/application/sitehome/view/public/img',
			'SITEHOME_CSS' => __ROOT__ . '/application/sitehome/view/public/css',
			'SITEHOME_JS' => __ROOT__ . '/application/sitehome/view/public/js',
			'SITEHOME_STYLE' => __ROOT__ . '/application/sitehome/view/style',
			'ADMIN_IMG' => __ROOT__ . '/application/admin/view/public/img',
			'ADMIN_CSS' => __ROOT__ . '/application/admin/view/public/css',
			'ADMIN_JS' => __ROOT__ . '/application/admin/view/public/js',
			'WAP_IMG' => __ROOT__ . '/application/wap/view/public/img',
			'WAP_CSS' => __ROOT__ . '/application/wap/view/public/css',
			'WAP_JS' => __ROOT__ . '/application/wap/view/public/js',
			'HOME_IMG' => __ROOT__ . '/application/home/view/public/img',
			'HOME_CSS' => __ROOT__ . '/application/home/view/public/css',
			'HOME_JS' => __ROOT__ . '/application/home/view/public/js',
			'__UPLOAD__' => __UPLOAD__,
		];
		//初始化常量
		$const = get_const();
		$config_array['addon_type'] = $const['addon_type'];
		$config_array['support_app_type'] = $const['support_app_type'];
		
		config($config_array);
		//初始化插件
		$addon_model = new Addon();
		$addon_model->getAddonCategory($const);
		
	}
	
	/**
	 * 注册命名空间
	 */
	private function registerNamespace()
	{
		// 注册插件addon根命名空间
		Loader::addNamespace(ADDON_DIR_NAME, ADDON_PATH);
		// 注册系统公用类根命名空间
		Loader::addNamespace('util', dirname(realpath(APP_PATH)) . DS . 'util' . DS);
	}
}