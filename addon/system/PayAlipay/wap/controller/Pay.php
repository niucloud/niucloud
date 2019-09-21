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

namespace addon\system\PayAlipay\wap\controller;

use addon\system\Pay\common\model\PayList;
use app\common\controller\BaseSite;

/**
 * 支付宝支付
 */
class Pay extends BaseSite
{
	/**
	 * 支付宝同步回调地址
	 */
	public function payReturn()
	{
		$out_trade_no = input('out_trade_no');
		$trade_no = input('trade_no');
		$trade_status = input('trade_status');
		
		$this->assign('out_trade_no', $out_trade_no);
		if ($trade_no == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
			
			$this->assign("status", 1);
		} else {
			$this->assign("status", 2);
		}
		
		$pay_list = new PayList();
		$pay_info = $pay_list->readPay($out_trade_no);
		if ($pay_info['return_url'] != '') {
			$this->redirect($pay_info['return_url']);
		}
		$this->assign('pay_info', $pay_info);
		return $this->fetch('pay/payReturn');
	}
}