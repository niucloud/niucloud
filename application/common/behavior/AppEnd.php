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
use think\Cache;

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
	    //执行系统事件
	    $this->loadTask();
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
	
	/**
	 * 执行系统事件
	 */
	private function loadTask()
	{
	    if (defined('BIND_MODULE') && BIND_MODULE === 'install') {
	        return;
	    }
	    //执行事件
	    $cache = Cache::tag('config')->get('load_task');
	    $last_time = cache("last_load_time");
	    if (empty($last_time)) {
	        $last_time = 0;
	    }
	    if (empty($cache) || time() - $last_time > 300) {
	        Cache::tag('config')->set('load_task', 1);
	        $url = url('cron/task/phpCron');
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_HEADER, true);
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	        curl_exec($ch);
	    }
	}
}