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

use app\common\model\Addon;
use app\common\model\Site;
use think\Lang;

/**
 * 初始化插件
 */
class InitAddon
{
	// 行为扩展的执行入口必须是run
	public function run()
	{
		if (defined('BIND_MODULE') && BIND_MODULE === 'install')
			return;
		// 获取钩子数据
		$site_id = request()->siteid();
		$site_model = new Site();
		$site_model->initHook($site_id);
		//初始化插件语言包
		$this->initLang();
		//初始化插件错误码
		$this->initErrorCode();
	}
	
	/**
	 * 初始化插件语言包
	 */
	private function initLang()
	{
		$addon_model = new Addon();
		$addons = $addon_model->getAddons();
		$addon_class = $addons['addon_class_path'];
		$lang_path = [];
		foreach ($addon_class as $k => $v) {
			$path = $v . 'lang\\zh-cn.php';
			$path = str_replace("\\", DIRECTORY_SEPARATOR, $path);
			$path = str_replace("/", DIRECTORY_SEPARATOR, $path);
			$lang_path[] = $path;
		}
		Lang::load($lang_path);
		
	}
	
	/**
	 * 初始化插件错误码
	 */
	private function initErrorCode()
	{
		$addon_model = new Addon();
		$addons = $addon_model->getAddons();
		$addon_class = $addons['addon_class_path'];
		foreach ($addon_class as $k => $v) {
			$error_path = $v . 'extra\\error_code.php';
			$error_path = str_replace("\\", DIRECTORY_SEPARATOR, $error_path);
			$error_path = str_replace("/", DIRECTORY_SEPARATOR, $error_path);
			if (is_file($error_path)) {
				require_once $error_path;
			}
			
		}
		
	}
	
}