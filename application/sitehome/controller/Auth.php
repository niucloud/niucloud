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
namespace app\sitehome\controller;

use app\common\controller\BaseSiteHome;
use app\common\model\User;

class Auth extends BaseSiteHome
{
	/**
	 * 操作日志
	 */
	public function operation()
	{
		if (IS_AJAX) {
			$user_model = new User();
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			
			$search_username = input('search_username', '');
			$search_module = input('search_module', '');
			$search_start_time = input('search_start_time', '');
			$search_end_time = input('search_end_time', '');
			
			$condition['site_id'] = $this->siteId;
			
			if ($search_username != '') {
				$condition['username'] = [
					'like',
					'%' . $search_username . '%'
				];
			}
			
			if ($search_module != '') {
				$condition['module'] = [
					'like',
					'%' . $search_module . '%'
				];
			}
			
			if ($search_start_time != '' || $search_end_time != '') {
				$search_start_time = $search_start_time == '' ? '0' : strtotime($search_start_time);
				$search_end_time = $search_end_time == '' ? '9999999999' : strtotime($search_end_time);
				$condition['create_time'] = [
					'between',
					[
						$search_start_time,
						$search_end_time
					]
				];
			}
			
			$log_list = $user_model->getUserLogPageList($condition, $page, $limit, 'create_time desc');
			return $log_list;
		}
		$this->assign('site_id', SITE_ID);
		return $this->fetch('Auth/operation');
	}
}