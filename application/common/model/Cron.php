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

namespace app\common\model;

use think\Db;

/**
 * 事件
 */
class Cron
{
	/**
	 * 获取计划任务列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getCronList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$list = model('nc_cron')->getList($condition, $field, $order, '', '', '', $limit);
		return success($list);
	}
	
	/**
	 * 获取计划任务执行记录
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getCronExecuteList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$list = model('nc_cron_execute_list')->getList($condition, $field, $order, '', '', '', $limit);
		return success($list);
	}
	
	/**
	 * 执行计划任务
	 */
	public function cronExecute()
	{
//        model('nc_cron_execute_list')->startTrans();
//
//        try {
		$list = model('nc_cron')->getList([ "status" => 1 ]);
		$time = time();
		
		foreach ($list as $k => $v) {
			$execute_time = cache("cron_" . $v['cron_id']);
			
			if (empty($execute_time)) {
				$execute_time = model('nc_cron_execute_list')->stat([ 'cron_id' => $v['cron_id'] ], 'max', 'execute_time');
			}
			if (empty($execute_time) || $execute_time == null) {
				$execute_time = 0;
			}
			//查询周期时间
			switch ($v['cron_period']) {
				case 1:
					$period_time = 3600 * 24;
					$max_count = 100;
					break;
				case 2:
					$period_time = 3600;
					$max_count = 100;
					break;
				case 3:
					$period_time = 60;
					$max_count = 100;
					break;
				default:
					$period_time = 3600 * 24;
					$max_count = 100;
					break;
			}
			$space_time = $period_time * $v['cron_period'];

//                dump($time-$execute_time >= $space_time);
//                if($time-$execute_time >= $space_time)
//                {
			
			
			//开始执行
			cache("cron_" . $v['cron_id'], $time);
			
			$res = hook($v['cron_hook'], [ 'addon_name' => $v['cron_addon'] ]);
			
			
			if (!empty($res)) {
				
				if ($res[0]['code'] == 0) {
					$is_success = 1;
					
				} else {
					$is_success = 0;
				}
				
				model('nc_cron_execute_list')->add([
					'cron_id' => $v['cron_id'],
					'cron_name' => $v['cron_name'],
					'cron_hook' => $v['cron_hook'],
					'cron_addon' => $v['cron_addon'],
					'execute_time' => $time,
					'is_success' => $is_success,
					'message' => $res[0]['message']
				]);
				
				$condition = [ 'cron_hook' => $v['cron_hook'], 'cron_addon' => $v['cron_addon'] ];
				$record_count = model('nc_cron_execute_list')->getCount($condition);//记录条数
				
				$count = $record_count - $max_count;
				
				//删除超出数量的记录
				if ($count > 0) {
					Db::table('nc_cron_execute_list')->where("cron_hook", $v['cron_hook'])->where("cron_addon", $v['cron_addon'])->order("execute_time asc")->limit($count)->delete();
				}
			}
//                }
		}
//            model('nc_cron_execute_list')->commit();
//            return success();
//        } catch (\Exception $e) {
//            model('nc_cron_execute_list')->rollback();
//            return error('', $e->getMessage());
//        }
	
	
	}
	
	/**计划任务分页
	 * @param $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @param string $field
	 * @return array
	 */
	public function getCronPageList($condition, $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$list = model('nc_cron')->pageList($condition, $field, $order, $page, $page_size);
		return success($list);
	}
	
	/**计划任务记录分页
	 * @param $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @param string $field
	 * @return array
	 */
	public function getCronExecutePageList($condition, $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$list = model('nc_cron_execute_list')->pageList($condition, $field, $order, $page, $page_size);
		return success($list);
	}
	
	/**
	 * 修改事件
	 * @param $data
	 * @param $condition
	 */
	public function updateCron($data)
	{
		$condition = array(
			"cron_id" => $data["cron_id"]
		);
		$res = model('nc_cron')->update($data, $condition);
		if ($res === false) {
			return error($res);
		}
		return success($res);
		
	}
}