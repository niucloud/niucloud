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

use app\common\model\Visit;

/**
 * 应用结束
 */
class AppEnd
{
	// 行为扩展的执行入口必须是run
	public function run()
	{
	    if (defined('BIND_MODULE') && BIND_MODULE === 'install')
	        return;
	    
		if (!request()->isAjax()) {
			$visit_model = new Visit();
			$site_id = request()->siteid();
			$addon_app = request()->siteAddon();
			$addon = request()->addon();
			$uid = defined('UID') ? UID : 0;
			$module = request()->module();
			$visit_model->todayVisit([ "type" => $module, "site_id" => $site_id, "module" => $addon_app, "uid" => $uid, "addon" => $addon ]);//添加访问记录
		}
		
	}
}