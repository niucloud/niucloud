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

namespace addon\system\Pay\common\controller;

use addon\system\Pay\common\model\PayList;
use app\common\controller\BaseSite;
use think\Hook;

/**
 * 支付管理控制器
 */
class Pay extends BaseSite
{
	/**
	 * 支付异步回调
	 */
	public function callBack()
	{


        $res = hook('payNotify', []);
//        return success($res);

//		$post_obj = input();
//		if (!isset($post_obj['out_trade_no'])) {
//			$postStr = file_get_contents('php://input');
//			$post_obj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
//		}
		$post_obj = json_decode(json_encode($post_obj), true);
//
//		$pay_list = new PayList();
//		$pay_info = $pay_list->readPay($post_obj['out_trade_no']);
//		if (!empty($pay_info)) {
//			$res = hook('payResult', [ 'data' => $post_obj, 'pay_type' => $pay_info['pay_type'], 'postStr' => $postStr ]);
//
//			if ($res[0]['code'] == 0) {
//				curl_get($pay_info['notify_url']);
//			}
//		}
	}

	/**
	 * 支付同步回调
	 */
	public function payReturn()
	{
		$post_obj = input();
		$pay_list = new PayList();
		$pay_info = $pay_list->readPay($post_obj['out_trade_no']);
		if (!empty($pay_info)) {
		    $this->redirect($pay_info["return_url"]);
		}
	}

	/**
	 * 获取支付结果
	 */
	public function getPayResult()
	{
		if (IS_AJAX) {
			$out_trade_no = input('out_trade_no', '');
			
			$pay_list = new PayList();
			$res = $pay_list->getPayResult($out_trade_no);
			
			return $res;
		}
	}
}