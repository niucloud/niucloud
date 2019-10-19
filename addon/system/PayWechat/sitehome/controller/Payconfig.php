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

namespace addon\system\PayWechat\sitehome\controller;

use addon\system\PayWechat\common\model\WechatConfig;
use app\common\controller\BaseSiteHome;
use addon\system\Wechat\common\model\Wechat;

/**
 * 微信支付
 */
class Payconfig extends BaseSiteHome
{
	/**
	 * 微信支付配置
	 */
	public function index()
	{
		
		$site_id = $this->siteId;
		$wechat_config_model = new WechatConfig();
		if (IS_AJAX) {
			$appid = request()->post("appid", '');
			$app_secrect = request()->post("app_secrect", '');
			$app_paysignkey = request()->post("app_paysignkey", '');
			$mchid = request()->post("mchid", 0);
			$apiclient_cert = request()->post("apiclient_cert", '');
			$apiclient_key = request()->post("apiclient_key", '');
			$pay_status = request()->post("pay_status", 0);
			$refund_is_use = request()->post("refund_is_use", '');
			$transfer_is_use = request()->post("transfer_is_use", '');
			$status = request()->post("status", '');
			$data = array(
				'app_paysignkey' => $app_paysignkey,
				'mchid' => $mchid,
				'apiclient_cert' => $apiclient_cert,
				'apiclient_key' => $apiclient_key,
				'refund_is_use' => $refund_is_use,
				'transfer_is_use' => $transfer_is_use,
				"pay_status" => $pay_status,
			);
			$value = json_encode($data);
			$data = array(
				"site_id" => $site_id,
				"value" => $value,
				"update_time" => time(),
				"status" => $status
			);
			$res = $wechat_config_model->setWechatConfig($data);
			return $res;
		} else {
			$weatch_model = new Wechat();
			$wechat_config = $weatch_model->getWechatConfigInfo($site_id);
			$this->assign("wechat_config", $wechat_config["data"]["value"]);
			//微信支付配置
			$get_pay_config = $wechat_config_model->getWechatConfig($site_id);
			$this->assign('status', $get_pay_config['data']['status']);
			$this->assign('config', $get_pay_config['data']['value']);
			return $this->fetch('pay_config/index');
		}
	}
	
}