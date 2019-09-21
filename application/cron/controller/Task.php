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

namespace app\cron\controller;

use app\common\model\Cron;
use think\Cache;
use think\Controller;
use think\Log;

/**
 * 计划任务
 * @author Administrator
 */
class Task extends Controller
{
	
	/**
	 * 执行计划任务(单独计划任务)
	 */
	public function execute()
	{
		//设置计划任务标识
	    $last_time = cache("last_load_time");
	    if ($last_time == false) {
	        $last_time = 0;
	    }
	    ignore_user_abort();
	    set_time_limit(0);
	    cache("task_load", 1);
	    do {
	        $task_load = cache("task_load");
	        if ($task_load == false) {
	            Log::write("清除缓存，可能进行了系统更新，跳出循环");
	            break;//跳出循环
	        }
	        $last_time = cache("last_load_time");
	        if ($last_time == false) {
	            $last_time = 0;
	        }
	        $time = time();
	        if (($time - $last_time) < 30) {
	            Log::write("跳出多余循环事件，保证当前只存在一个循环");
	            break;//跳出循环
	        }
	        cache("last_load_time", time());
    		$cron_model = new Cron();
    		$cron_model->cronExecute();
	        Log::write("检测事件");
	        cache("last_load_time", time());
	        sleep(60);
	    } while (TRUE);
	}
	
	/**
	 * php自动执行事件
	 */
	public function phpCron()
	{
	    $url = url('cron/task/execute');
	    http($url, 1);
	    return 1;
	}
	
	/**
	 * php异步开启事件
	 */
	public function ajaxCron()
	{
		$url = url('cron/task/phpCron');
		http($url, 1);
		return success();
	}
}