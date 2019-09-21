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

namespace addon\system\SmsNiucloud\sitehome\controller;

use app\common\controller\BaseSiteHome;
use util\api\WebClient;
use addon\app\NiuCloud\common\model\Order;

class Index extends BaseSiteHome
{
	public $client;
	public $auth;
	
	public function __construct()
	{
		parent::__construct();
		$this->auth = getAuth();
		$this->auth = [
			'app_key' => 'h0p74b04ww4yn',
			'app_secret' => '4vjgx316zluvamvakcyx7n4g6pdjv7ty'
		];
		$this->client = new WebClient($this->auth['app_key'], $this->auth['app_secret']);
	}
	
	/**
	 * 牛云短信
	 */
	public function buysitesms()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$order = 'create_time desc';
			if (SYS_VERSION == 'NiuCloud') {
				$condition = [
					'uid' => UID,
					'order_type' => 'smsOrder'
				];
				$order_model = new Order();
				$list = $order_model->getUserAddonsOrderList($condition, $page, $limit, $order);
				return $list;
			} else if (SYS_VERSION == 'DEPLOY') {
				$condition = [
					'order_type' => 'smsOrder'
				];
				$list = $this->client->post('Order.getOrderList', [ 'condition' => $condition, 'page' => $page, 'page_size' => $limit, 'order' => $order, 'user_app_key' => $this->auth['app_key'] ]);
				return $list;
			}
		}
		$sms_data = [
			'sms_count_num' => 0,
			'sms_surplus_num' => 0,
			'sms_use_num' => 0
		];
		if (SYS_VERSION == 'NiuCloud') {
			$sms_count_num = model("nc_niucloud_user_sms_record")->getSum([ "uid" => UID ], "num");
			$user_info = model("nc_user")->getInfo([ "uid" => UID ], "sms_num");
			$sms_surplus_num = $user_info["sms_num"];
			$sms_use_num = $sms_count_num - $sms_surplus_num;
			$sms_data = [
				'sms_count_num' => $sms_count_num,
				'sms_surplus_num' => $sms_surplus_num,
				'sms_use_num' => $sms_use_num
			];
			$buy_url = addon_url('niucloud://web/personal/smsbuy');
		} else if (SYS_VERSION == 'DEPLOY') {
			$data = $this->client->post('Sms.getSmsNum');
			if ($data['code'] == 0) {
				$sms_data = $data['data'];
			}
			$buy_url = addon_url('sitehome/personal/smsBuy');
		}
		$this->assign('buy_url', $buy_url);
		$this->assign('sms_data', $sms_data);
		$this->assign('sys_version', SYS_VERSION);
		return $this->fetch('index/buy_site_sms');
	}
}