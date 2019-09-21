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
namespace addon\system\PayAlipay;

use addon\system\Pay\common\model\PayList;
use addon\system\PayAlipay\common\model\AlipayConfig;
use addon\system\PayAlipay\common\model\Pay;
use app\common\controller\BasePayAddon;
use app\common\model\Site;

/**
 * 支付宝支付插件
 */
class PayAlipayAddon extends BasePayAddon
{
	public $info = array(
		'name' => 'PayAlipay',
		'title' => '支付宝',
		'description' => '支付宝支付',
		'status' => 1,
		'author' => '',
		'version' => '1.0',
		'visble' => 1,
		'type' => 'ADDON_SYSTEM',
		'category' => 'SYSTEM',
		'content' => '支付宝支付',
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
	 * @return ['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function install()
	{
		return success();
	}
	
	/**
	 * 卸载
	 * @return ['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function uninstall()
	{
		return success();
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
		
		$alipay_config = new AlipayConfig();
		$res = $alipay_config->delAlipayConfig($site_id);
		return success();
	}
	
	/**
	 * 复制站点数据--复制站点时调用
	 * @param integer $site_id
	 * Returns:['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function copyToSite($site_id, $new_site_id)
	{
		
		$alipay_config = new AlipayConfig();
		$config_info = $alipay_config->getAlipayConfig($site_id);
		if (!empty($config_info['data'])) {
			$data = $config_info['data'];
			$data["site_id"] = $new_site_id;
			$res = $alipay_config->setAlipayConfig($data);
		}
		return success();
	}
	
	/**
	 * 获取支付方式
	 * @param array $param
	 */
	public function getPayType($param = [])
	{
		$pay_scene = array( "wap", "app", "pc" );//使用场景
		if (in_array($param["pay_scene"], $pay_scene)) {
			
			$site_id = $param['site_id'];
			$alipay_config = new AlipayConfig();
			$pay_config = $alipay_config->getAlipayConfig($site_id);
			//是否启用  并且是否使用支付
			if ($pay_config["data"]["status"] == 1 && $pay_config["data"]["value"]["pay_status"] == 1) {
				$this->info['icon'] = __ROOT__ . '/addon/system/' . $this->info['name'] . '/icon.png';
				return $this->info;
			}
		}
	}
	
	/**
	 * 获取退款方式
	 * @param array $param
	 */
	public function getRefundType($param = [])
	{
	    $site_id = $param['site_id'];
	    $alipay_config = new AlipayConfig();
	    $pay_config = $alipay_config->getAlipayConfig($site_id);
	    //是否启用  并且是否使用退款
	    if ($pay_config["data"]["status"] == 1 && $pay_config["data"]["value"]["refund_is_use"] == 1) {
	        $this->info['icon'] = __ROOT__ . '/addon/system/' . $this->info['name'] . '/icon.png';
	        return $this->info;
	    }
	}
	
	/**
	 * 获取转账方式
	 * @param array $param
	 */
	public function getTransferType($param = [])
	{
	    $site_id = $param['site_id'];
	    $alipay_config = new AlipayConfig();
	    $pay_config = $alipay_config->getAlipayConfig($site_id);
	    //是否启用  并且是否使用退款
	    if ($pay_config["data"]["status"] == 1 && $pay_config["data"]["value"]["transfer_is_use"] == 1) {
	        $this->info['icon'] = __ROOT__ . '/addon/system/' . $this->info['name'] . '/icon.png';
	        return $this->info;
	    }
	}
	
	/**
	 * 获取支付配置
	 * @param array $param
	 */
	public function getPayConfig($param = [])
	{
		$site_id = $param['site_id'];
		$alipay_model = new AlipayConfig();
		$config_result = $alipay_model->getAlipayConfig($site_id);
		$config_info = $config_result["data"];
		$this->info['icon'] = __ROOT__ . './addon/system/' . $this->info['name'] . '/icon.png';
		$this->info['url'] = addon_url('PayAlipay://sitehome/payconfig/index');
		return [
			'info' => $this->info,
			'config' => $config_info
		];
	}
	
	/**
	 * 支付页跳转
	 * @param array $param
	 * Returns: 跳转新页
	 */
	public function pay($param = [])
	{
		$res = parent::pay($param);
		if (!$res) return;
		
		if (in_array($param["pay_scene"], [ "wap", "app", "pc" ])) {
			$pay = new Pay($param['site_id']);
			$res = $pay->doPay($param['out_trade_no'], $param["pay_body"], $param["pay_body"], $param["pay_money"], $param["notify_url"], $param["return_url"], $param["pay_scene"]);
			$data = array(
				"type" => "url",
				"url" => $res,
			);
			return success($data);
		}
		
		
	}
	
	/**
	 * 异步回调支付结果
	 * array $param
	 */
	public function payNotify($param = [])
	{
		try {
			$data = input();
			$out_trade_no = $data['out_trade_no'];
			//获取支付信息
			$pay_list = new PayList();
			$pay_info = $pay_list->readPay($out_trade_no);
			$pay = new Pay($pay_info['site_id']);
			//验证支付宝配置信息
			$verify_result = $pay->getVerifyResult($data, 'notify');
			if ($verify_result) { // 验证成功
				
				// 支付宝交易号
				$trade_no = isset($result_data['trade_no']) ? $data['trade_no'] : '';
				// 交易状态
				$trade_status = $data['trade_status'];
				if ($trade_status == "TRADE_SUCCESS") {
					$res = $pay_list->onlinePay($out_trade_no, $this->info['name'], $trade_no);
					return $res;
				}
			} else {
				
				// 验证失败
				return error('', 'UNKNOW_ERROR');
			}
		} catch (\Exception $e) {
		
		
		}
	}
	
	/**
	 * 同步回调支付结果
	 * array $param
	 */
	public function payReturn($param = [])
	{
		
		$out_trade_no = $param['out_trade_no'];
		$trade_no = $param['trade_no'];
		$trade_status = $param['trade_status'];
		
		if ($trade_no == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
			return [ 'status' => 1, 'out_trade_no' => $out_trade_no ];
		} else {
			return [ 'status' => 0, 'out_trade_no' => $out_trade_no ];
		}
	}
	
	/**
	 * 退款
	 * @param array $param
	 */
	public function refund($param = [])
	{
		$res = parent::refund($param);
		if (!$res) return error();
		$alipay_config = new AlipayConfig();
		$res = $alipay_config->doRefundPay($param);
		return $res;
	}
	
	/**
	 * 转账
	 * @param array $param
	 */
	public function transfer($param = [])
	{
	
	}
	
	/**
	 * 关闭支付
	 * @param array $param
	 */
	public function closepay($param = [])
	{
	
	}
	
}