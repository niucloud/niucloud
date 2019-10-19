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

namespace addon\system\PayAlipay\sitehome\controller;

use addon\system\PayAlipay\common\model\AlipayConfig;
use app\common\controller\BaseSiteHome;

/**
 * 支付设置
 */
class Payconfig extends BaseSiteHome
{
	
	/**
	 * 支付宝支付配置
	 */
	public function index()
	{
		$alipay_config = new AlipayConfig();
		if (IS_AJAX) {
			$appid = request()->post("appid", '');
			$alipay_public_key = request()->post("alipay_public_key", '');
			$private_key = request()->post("private_key", '');
			$public_key = request()->post("public_key", '');
			$pay_status = request()->post("pay_status", 0);
			$refund_is_use = request()->post("refund_is_use", '0');
			$transfer_is_use = request()->post("transfer_is_use", '0');
			$status = request()->post("status", '0');
			$json_data = array(
				'appid' => $appid,
				'alipay_public_key' => $alipay_public_key,
				'private_key' => $private_key,
				'public_key' => $public_key,
				'pay_status' => $pay_status,
				'refund_is_use' => $refund_is_use,
				'transfer_is_use' => $transfer_is_use
			);
			$value = json_encode($json_data);
			$site_id = $this->siteId;
			$data = array(
				"site_id" => $site_id,
				"value" => $value,
				"status" => $status,
				"update_time" => time()
			);
			$result = $alipay_config->setAlipayConfig($data);
			return $result;
			
		} else {
			$site_id = $this->siteId;
			$config = $alipay_config->getAlipayConfig($site_id);
			$this->assign('config', $config['data']['value']);
			$this->assign('status', $config['data']['status']);
			return $this->fetch('pay_config/index');
		}
	}
}