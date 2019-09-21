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

namespace addon\system\Pay\common\model;

use think\Cache;

/**
 * 支付配置
 */
class PayList
{
	
	
	public $refund_pay_type = array(
		"offline_refund_pay" => "线下支付",
		"online_refund_pay" => "原路退款"
	);
	
	/**
	 * 写入支付信息
	 * @param unknown $out_trade_no
	 * @param unknown $pay_body
	 * @param unknown $pay_detail
	 * @param unknown $pay_money
	 */
	public function writePay($site_id, $out_trade_no, $pay_type, $pay_body, $pay_detail, $pay_money, $pay_no, $notify_url, $return_url)
	{
		$data = array(
			'site_id' => $site_id,
			'out_trade_no' => $out_trade_no,
			'pay_type' => $pay_type,
			'pay_body' => $pay_body,
			'pay_detail' => $pay_detail,
			'pay_money' => $pay_money,
			'pay_no' => $pay_no,
			'notify_url' => $notify_url,
			'return_url' => $return_url,
		);
		$data_json = json_encode($data);
		
		
		$pay_file_path = RUNTIME_PATH . '/pay/';
		if (!is_dir($pay_file_path)) {
			mkdir($pay_file_path);
		}
		file_put_contents($pay_file_path . $out_trade_no . '.txt', $data_json);
	}
	
	/**
	 * 读取支付信息
	 * @param unknown $out_trade_no
	 */
	public function readPay($out_trade_no)
	{
		$pay_file_path = RUNTIME_PATH . '/pay/';
		$list = file_get_contents($pay_file_path . $out_trade_no . '.txt');
		return json_decode($list, true);
	}
	
	
	/**
	 * 异步回调添加库
	 * @param unknown $out_trade_no
	 */
	public function onlinePay($out_trade_no, $pay_type, $trade_no)
	{
		
		$pay_info = $this->readPay($out_trade_no);
		
		$get_pay_list = model('nc_pay_list')->getInfo([ 'out_trade_no' => $out_trade_no, 'site_id' => $pay_info['site_id'] ]);
		
		if (empty($get_pay_list)) {
			$data = array(
				'out_trade_no' => $out_trade_no,
				'trade_no' => $trade_no,
				'pay_no' => $pay_info['pay_no'],
				'site_id' => $pay_info['site_id'],
				'pay_body' => $pay_info['pay_body'],
				'pay_detail' => $pay_info['pay_detail'],
				'pay_money' => $pay_info['pay_money'],
				'pay_type' => $pay_type,
				'create_time' => time()
			);
			
			$pay_file_path = RUNTIME_PATH . '/pay/' . $out_trade_no . '.txt';
			$res = model('nc_pay_list')->add($data);
			
			if ($res === false) {
				error('', 'UNKNOW_ERROR');
			} else {
				
				//成功则直接给应用异步回调地址发送
				$return_data = array(
					'out_trade_no' => $out_trade_no,
					'trade_no' => $trade_no,
					'code' => 1
				);
				if(!empty($pay_info["notify_url"])){
                    curl_get($pay_info["notify_url"]);
                }

				return success($return_data);
			}
		}
		
	}
	
	/**
	 * 获取支付信息详情
	 * @param string $out_trade_no
	 */
	public function getPayListInfo($out_trade_no)
	{
		$get_pay_list = model('nc_pay_list')->getInfo([ 'out_trade_no' => $out_trade_no ]);
		return success($get_pay_list);
	}
	
	/**
	 * 支付记录
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getPayPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		
		$list = model('nc_pay_list')->pageList($condition, $field, $order, $page, $page_size);
		return success($list);
	}
	
	/**
	 * 支付统计
	 * @param unknown $site_id
	 */
	public function getPayStatistics($site_id)
	{
		
		$statistics_array = array(
			'count' => model('nc_pay_list')->getCount([ 'site_id' => $site_id ]),
			'sum_money' => model('nc_pay_list')->getSum([ 'site_id' => $site_id ], 'pay_money')
		);
		
		return $statistics_array;
	}
	
	/**
	 * 创建支付流水号
	 */
	public function createOutTradeNo()
	{
		$cache = Cache::get("niucloud_out_trade_no" . time());
		if (empty($cache)) {
			Cache::set("niubfd" . time(), 1000);
			$cache = Cache::get("niucloud_out_trade_no" . time());
		} else {
			$cache = $cache + 1;
			Cache::set("niucloud_out_trade_no" . time(), $cache);
		}
		$no = time() . rand(1000, 9999) . $cache;
		return $no;
	}
	
	/**
	 * 创建退款流水号
	 */
	public function createRefundNo()
	{
		$cache = Cache::get("niucloud_out_trade_no" . time());
		if (empty($cache)) {
			Cache::set("niutk" . time(), 1000);
			$cache = Cache::get("niucloud_refund_no" . time());
		} else {
			$cache = $cache + 1;
			Cache::set("niucloud_refund_no" . time(), $cache);
		}
		$no = date('Ymdhis', time()) . rand(1000, 9999) . $cache;
		return $no;
	}
	
	/**
	 * 添加退款记录
	 * @param $data
	 */
	public function addRefundPayList($condition)
	{
		$data = [
			'refund_no' => $condition['refund_no'],
			'out_trade_no' => $condition['out_trade_no'],
			'site_id' => $condition['site_id'],
			'refund_type' => $condition['refund_type'],
			'create_time' => time(),
			'refund_fee' => $condition['refund_fee'],
			'total_money' => $condition['total_money'],
		];
		$data["create_time"] = time();
		$data["detail"] = "支付交易号:" . $condition['order_no'] . "，退款方式为:[" . $this->refund_pay_type[ $condition["refund_type"] ] . "]，退款金额:" . $condition["refund_fee"] . "元";
		$res = model("nc_pay_refund_list")->add($data);
		if ($res == false) {
			return error($res);
		}
		return success($res);
	}
	
	/**线下支付
	 * @param $param
	 * @return array
	 */
	public function offlinePay($param)
	{
		$get_pay_list = model('nc_pay_list')->getInfo([ 'out_trade_no' => $param["out_trade_no"], 'site_id' => $param['site_id'] ]);
		
		if (empty($get_pay_list)) {
			$data = array(
				'out_trade_no' => $param["out_trade_no"],
				'trade_no' => '',
				'pay_no' => '',
				'site_id' => $param['site_id'],
				'pay_body' => "线下支付",
				'pay_detail' => $param['pay_detail'],
				'pay_money' => $param['pay_money'],
				'pay_type' => "",
				'create_time' => time()
			);
			
			$res = model('nc_pay_list')->add($data);
			
			if ($res === false) {
				return error('', 'UNKNOW_ERROR');
			} else {
				//成功则直接给应用异步回调地址发送
				return success();
			}
		}
	}
	
	/**
	 * 获取支付结果
	 * @param unknown $out_trade_no
	 */
	public function getPayResult($out_trade_no)
	{
		$count = model('nc_pay_list')->getCount([ 'out_trade_no' => $out_trade_no ]);
		return success($count);
	}
	
	/**
	 * 删除站点
	 * @param unknown $site_id
	 */
	public function deleteSite($site_id)
	{
	    model('nc_pay_list')->delete([ 'site_id' => $site_id ]);
	    model('nc_pay_refund_list')->delete([ 'site_id' => $site_id ]);
	    return success();
	}
}
