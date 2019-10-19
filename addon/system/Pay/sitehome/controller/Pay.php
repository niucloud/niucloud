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

namespace addon\system\Pay\sitehome\controller;

use addon\system\Pay\common\model\PayList;
use app\common\controller\BaseSiteHome;
use app\common\model\Site;

/**
 * 支付管理控制器
 */
class Pay extends BaseSiteHome
{
	protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'
	
	public function __construct()
	{
		parent::__construct();
		$this->replace = [
			'ADDON_NC_PAY_IMG' => __ROOT__ . '/addon/system/Pay/sitehome/view/public/img',
		];
	}
	
	public function lists()
	{
		if (IS_AJAX) {
			$site_id = $this->siteId;
			$pay_config_list = hook('getPayConfig', [ 'site_id' => $site_id ]);
			$res = error();
			if(!empty($pay_config_list)) {
				$res = success();
				$res['data'] = [
					'count' => !empty($pay_config_list) ? count($pay_config_list) : 0,
					'list' => $pay_config_list
				];
			}
			return $res;
		}
		
		return $this->fetch('pay/lists', [], $this->replace);
	}
	
	
	public function config()
	{
		$addon_name = input('addon_name', '');
		hook('doEdit', [ 'name' => $addon_name ]);
	}
	
	/**
	 * 支付记录信息
	 */
	public function payList()
	{
		$pay_list = new PayList();
		if (IS_AJAX) {
			
			$condition = [ 'site_id' => request()->siteid() ];
			$page = input('page', 1);
			$field = '*';
			$order = 'create_time desc';
			$limit = PAGE_LIST_ROWS;
			
			$list = $pay_list->getPayPageList($condition, $page, $limit, $order, $field);
			return $list;
		}
		
		$statistics_arr = $pay_list->getPayStatistics(request()->siteid());
		$this->assign('statistics_arr', $statistics_arr);
		return $this->fetch('pay/pay_list');
	}
	
	/**
	 * 修改支付插件状态
	 * @return multitype:string mixed
	 */
	public function setPayConfigStatus()
	{
		if (IS_AJAX) {
			$site_id = $this->siteId;
			$name = input('name', '');
			$status = input('status', 1);
			$site_model = new Site();
			$res = $site_model->setSiteConfig([ "status" => $status, 'site_id' => $site_id, "name" => $name ]);
			return $res;
		}
	}
}