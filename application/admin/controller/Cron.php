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
use app\common\model\Cron as CronModel;

/**
 * 计划任务控制器
 */
class Cron extends BaseAdmin
{
	
	/**
	 * 计划任务列表
	 * @return mixed
	 */
	public function cronList()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$condition = array();
			$cron_model = new CronModel();
			$user_list = $cron_model->getCronPageList($condition, $page, $limit);
			
			return $user_list;
		}
		return $this->fetch('cron/cron_list');
	}
	
	/**
	 * 开启或关闭事件状态
	 */
	public function modifyCronStatus()
	{
		$cron_model = new CronModel();
		$cron_id = input("cron_id", 0);
		$status = input("status", "0");
		$data = array(
			"cron_id" => $cron_id,
			"status" => $status
		);
		$res = $cron_model->updateCron($data);
		return $res;
	}
	
	/**
	 * 事件记录列表
	 */
	public function cronExecuteList()
	{
		$cron_id = input("cron_id", "");
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$order = "execute_time  desc";
			$condition = array();
			if (!empty($cron_id)) {
				$condition["cron_id"] = $cron_id;
			}
			$cron_model = new CronModel();
			$user_list = $cron_model->getCronExecutePageList($condition, $page, $limit, $order);
			
			return $user_list;
		}
		$this->assign("cron_id", $cron_id);
		return $this->fetch('cron/cronexecutelist');
	}
	
	/**
	 * 设置管理计划任务
	 */
	public function setCron()
	{
		$type = input("type", "php");
		if ($type == "window") {
			$path_data = $this->getPhpTaskPath();
			$command = 'schtasks /create /sc minute /mo 1 /tn "Niucloud Cron"  /tr ';
			$command .= '"' . php_exe_real_path() . " -f " . $path_data["path"] . DS . "index.php " . $path_data["param"] . '"';
			return success($command);
		} else if ($type == "linux") {
			$command = "*/1 * * * * /usr/bin/curl -o temp.txt " . __ROOT__ . DS . "index.php" . DS . "Cron/Task/test";
			return success($command);
		} else {
			//开启php计划任务
			$url = url('cron/task/phpCron');
			http($url, 1);
			return success();
		}
	}
	
	/**
	 * 获取自动事件启动路径()
	 */
	public function getPhpTaskPath()
	{
		$path = getcwd();
//            .DS."index.php";
		$param = "/Cron/Task/test";
		
		return [ "path" => $path, "param" => $param ];
	}
	
}