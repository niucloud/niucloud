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
namespace addon\system\Pay;

use app\common\controller\BaseAddon;
use addon\system\Pay\common\model\PayList;
use app\common\model\Site;

/**
 * 支付插件
 */
class PayAddon extends BaseAddon
{
	public $info = array(
		'name' => 'Pay',
		'title' => '支付管理',
		'description' => '支付管理插件',
		'status' => 1,
		'author' => '',
		'version' => '1.0',
		'visble' => 1,
		'type' => 'ADDON_SYSTEM',
		'category' => 'SYSTEM',
		'content' => 'this is a file!',
		//预置插件，多个用英文逗号分开
		'preset_addon' => '',
		'support_addon' => '',
		'support_app_type' => 'wap,weapp'
	);
	
	public $config;
	
	public function __construct()
	{
		parent::__construct();
		$this->config = $this->config_info;
	}
	
	/**
	 * 安装
	 * Returns:['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function install()
	{
		return success();
	}
	
	/**
	 * 卸载
	 * Returns:['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function uninstall()
	{
// 	    return success();
		return error('', 'System addon can not be uninstalled!');
	}
	
	
	/**
	 * 初始化站点数据，在添加站点的时候用
	 * @param integer $site_id
	 * Returns:['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function addToSite($site_id)
	{
		return success();
	}
	
	/**
	 * 删除站点数据--删除站点时调用
	 * @param integer $site_id
	 * Returns:['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function delFromSite($site_id)
	{
		$pay_model = new PayList();
		$pay_model->deleteSite($site_id);
		return success();
	}
	
	/**
	 * 复制站点数据--复制站点时调用
	 * @param integer $site_id
	 * Returns:['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function copyToSite($site_id, $new_site_id)
	{
		return success();
	}
	
	/**
	 * 创建支付流水号
	 * @param unknown $param
	 */
	public function getOutTradeNo($param = [])
	{
		$pay = new PayList();
		$out_trade_no = $pay->createOutTradeNo();
		return $out_trade_no;
	}
	
	/**
	 * 验证支付信息
	 * @param array $param
	 * Returns: 支付详情信息
	 */
	public function validatePay($param = [])
	{
		$pay_list = new PayList();
		$pay_info = $pay_list->getPayListInfo($param['out_trade_no']);
		return $pay_info;
	}
	
	/**
	 * 退款
	 * @param array $param
	 */
	public function refundPay($param = [])
	{
		$out_trade_no = $param["out_trade_no"];
		$refund_fee = $param["refund_fee"];
		$refund_type = $param["refund_type"];
		$total_fee = $param["total_fee"];
		$pay_info = model("nc_pay_list")->getInfo([ "out_trade_no" => $out_trade_no, "site_id" => $param['site_id'] ], "*");
		$pay_list_model = new PayList();
		$refund_no = $pay_list_model->createRefundNo();
		$param["refund_no"] = $refund_no;
		$param["trade_no"] = $pay_info['trade_no'];
		if (!empty($pay_info)) {
			if ($refund_type != "offline_refund_pay") {
				$param["name"] = $pay_info["pay_type"];
				$res = hook("doRefundPay", $param);
				if ($res[0]["code"] !== 0) {
					return $res[0];
				}
			}
			$res = $pay_list_model->addRefundPayList($param);
			return $res;
		} else {
			return error();
		}
	}
	
	/**
	 * 直接跳转支付
	 * @param array $param
	 * Returns: 跳转新页
	 */
	public function payment($param = [])
	{
		$site_model = new Site();
		$site_info = $site_model->getSiteInfo([ 'site_id' => $param['pay_data']["site_id"] ]);
		$json_data = encrypt(json_encode($param['pay_data']), $site_info['data']['app_secret']);
		$pay_url = addon_url('pay://wap/pay/index', [ 'data' => $json_data ]);
		$this->redirect($pay_url);
	}
	
}