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
namespace app\common\controller;

use addon\system\Pay\common\model\PayList;

/**
 * 支付系统插件
 */
abstract class BasePayAddon extends BaseAddon
{
	
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 安装
	 * @return ['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function install()
	{
	    //设置默认值
	    return true;
	}
	
	/**
	 * 卸载
	 * @return ['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function uninstall()
	{
	    //删除掉，设置别的为默认
	    return true;
	}
	
	/**
	 * 获取支付配置
	 * @param array $param
	 * @return array
	 */
	protected function getPayConfig($param = [])
	{
		return [
			'info' => $this->info
		];
	}
	
	/**
	 * 支付配置跳转
	 * @param array $param
	 * Returns: 跳转新页
	 */
	protected function doEdit($param = [])
	{
		if ($param['name'] == $this->info['name']) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 支付
	 * @param array $param
	 */
	protected function pay($param = [])
	{
		if ($param['name'] == $this->info['name']) {
			return true;
		} else {
			return false;
		}
		
	}
	
	/**
	 * 同步回调支付结果
	 * @param array $param
	 */
	protected function payReturn($param = [])
	{
		$pay_list = new PayList();
		$pay_type = '';
		
		if (isset($param['out_trade_no'])) {
			
			$pay_info = $pay_list->readPay($param['out_trade_no']);
			$pay_type = $pay_info['pay_type'];
		}
		return $pay_type == $this->info['name'] ? true : false;
		
	}
	
	/**
	 * 异步回调支付结果
	 * @param array $param
	 */
	protected function payNotify($param = [])
	{
		if ($param['name'] == $this->info['name']) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 退款
	 * @param array $param
	 */
	protected function refund($param = [])
	{
		if ($param['name'] == $this->info['name']) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 转账
	 * @param array $param
	 */
	protected function transfer($param = [])
	{
		if ($param['name'] == $this->info['name']) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 验证支付配置
	 * @param array $param
	 */
	protected function checkPayTypeConfig($param = [])
	{
		if ($param['name'] == $this->info['name']) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 关闭支付
	 * @param array $param
	 */
	protected function closePay($param = [])
	{
		if ($param['name'] == $this->info['name']) {
			return true;
		} else {
			return false;
		}
		
	}
}