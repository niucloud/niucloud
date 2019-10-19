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
namespace addon\system\PayWechat;

use addon\system\Pay\common\model\PayList;
use addon\system\PayWechat\common\model\Pay;
use addon\system\PayWechat\common\model\WechatConfig;
use app\common\controller\BasePayAddon;

/**
 * 短信系统插件
 */
class PayWechatAddon extends BasePayAddon
{
	public $info = array(
		'name' => 'PayWechat',
		'title' => '微信支付',
		'description' => '微信支付',
		'status' => 1,
		'author' => '',
		'version' => '1.0',
		'visble' => 1,
		'type' => 'ADDON_SYSTEM',
		'category' => 'SYSTEM',
		'content' => '微信支付',
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
		$wechat_config = new WechatConfig();
		$res = $wechat_config->delWechatConfig($site_id);
		return success();
	}
	
	/**
	 * 复制站点数据--复制站点时调用
	 * @param integer $site_id
	 * Returns:['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function copyToSite($site_id, $new_site_id)
	{
		$wechat_config = new WechatConfig();
		$config_info = $wechat_config->getWechatConfig($site_id);
		
		if (!empty($config_info['data'])) {
			$data = $config_info['data'];
			$data["site_id"] = $new_site_id;
			$res = $wechat_config->setWechatConfig($data);
		}
		return success();
	}
	
	/**
	 * 获取支付方式
	 * @param array $param
	 */
	public function getPayType($param = [])
	{
		$pay_scene = array( "wap", "wechat", "app", "pc", "wechat_applet" );//使用场景
		if (in_array($param["pay_scene"], $pay_scene)) {
			$site_id = $param['site_id'];
			$wechat_config = new WechatConfig();
			$config_info = $wechat_config->getWechatConfig($site_id);
			//是否启用  并且是否使用支付
			if ($config_info["data"]["status"] == 1 && $config_info["data"]["value"]["pay_status"] == 1) {
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
	    $wechat_config = new WechatConfig();
	    $config_info = $wechat_config->getWechatConfig($site_id);
	    //是否启用  并且是否使用支付
	    if ($config_info["data"]["status"] == 1 && $config_info["data"]["value"]["refund_is_use"] == 1) {
	        $this->info['icon'] = __ROOT__ . '/addon/system/' . $this->info['name'] . '/icon.png';
	        return $this->info;
	    }
	}
	
	/**
	 * 获取退款方式
	 * @param array $param
	 */
	public function getTransferType($param = [])
	{
	    $site_id = $param['site_id'];
	    $wechat_config = new WechatConfig();
	    $config_info = $wechat_config->getWechatConfig($site_id);
	    //是否启用  并且是否使用支付
	    if ($config_info["data"]["status"] == 1 && $config_info["data"]["value"]["transfer_is_use"] == 1) {
	        $this->info['icon'] = __ROOT__ . '/addon/system/' . $this->info['name'] . '/icon.png';
	        return $this->info;
	    }
	}
	
	/**
	 * 获取支付配置
	 * @param array $param
	 * Returns:['info' => [], 'site_config' => []]
	 */
	public function getPayConfig($param = [])
	{
		$site_id = $param['site_id'];
		
		$site_model = new WechatConfig();
		$config_result = $site_model->getWechatConfig($site_id);
		$config_info = $config_result["data"];
		$this->info['icon'] = __ROOT__ . '/addon/system/' . $this->info['name'] . '/icon.png';
		$this->info['url'] = addon_url('PayWechat://sitehome/payconfig/index');
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
		
		$pay_service = new Pay();
		$data = $param;
		
		switch ($param["pay_scene"]) {
			case "wap":
				//是否是微信内置浏览器
				if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
					$data["trade_type"] = 'MWEB';
					$res = $pay_service->doPay($data);
					if (empty($res['mweb_url'])) {
						return error([ 'type' => 'url', 'out_message' => json_encode($res, JSON_UNESCAPED_UNICODE) ]);
					}
					return success([ 'type' => 'url', 'url' => $res["mweb_url"] ]);
					break;
				} else {
					$data["trade_type"] = 'JSAPI';
					$res = $pay_service->doPay($data);
					
					if (!empty($res["return_code"]) && $res["return_code"] == "FAIL" && $res["return_msg"] == "JSAPI支付必须传openid") {
						return error([ 'return_type' => 'url', 'out_message' => json_encode($res, JSON_UNESCAPED_UNICODE) ]);
					} else {
						$retval = $pay_service->getWxJsApi($res, $data["site_id"]);
						$this->assign("out_trade_no", $param['out_trade_no']);
						$this->assign('jsApiParameters', $retval);
						$this->assign("pay_data", $data);
						$view = $this->fetch(ADDON_SYSTEM_PATH . '/PayWechat/wap/view/pay/wechat_jsapi.html');
						return success([ 'type' => 'html', 'html' => $view ]);
					}
					break;
				}
			case "pc":
				$data["trade_type"] = 'NATIVE';
				$res = $pay_service->doPay($data);
				if ($res["return_code"] == "SUCCESS") {
					if (empty($res['code_url'])) {
						$code_url = json_encode($res, JSON_UNESCAPED_UNICODE);
					} else {
						$code_url = $res['code_url'];
					}
					if (!empty($res["err_code"]) && $res["err_code"] == "ORDERPAID" && $res["err_code_des"] == "该订单已支付") {
						$code_url = json_encode($res, JSON_UNESCAPED_UNICODE);
					}
				} else {
					$code_url = json_encode($res, JSON_UNESCAPED_UNICODE);
				}
				return success([ 'type' => 'qrcode', 'code_url' => $code_url ]);
				break;
			case "app":
				$data["trade_type"] = 'APP';
				$res = $pay_service->doPay($data);
				
				break;
			case "wechat_applet":
				$data["trade_type"] = 'APPLET';
				$res = $pay_service->doPay($data);
				if ($res["result_code"] == "SUCCESS" && $res["return_code"] == "SUCCESS") {
					$appid = $res["appid"];
					$nonceStr = $res["nonce_str"];
					$package = $res["prepay_id"];
					$signType = "MD5";
					$config = new WechatConfig();
					$wchat_config = $config->getWechatConfig($param["site_id"]);
					$key = $wchat_config['value']['mch_key'];
					$timeStamp = time();
					$sign_string = "appId=$appid&nonceStr=$nonceStr&package=prepay_id=$package&signType=$signType&timeStamp=$timeStamp&key=$key";
					$paySign = strtoupper(md5($sign_string));
					$res["timestamp"] = $timeStamp;
					$res["PaySign"] = $paySign;
					return success([ 'type' => 'data', 'html' => $res ]);
				}
				break;
		}
		return error();
	}
	
	/**
	 * 异步回调支付结果
	 * array $param
	 */
	public function payNotify($param = [])
	{
		$postStr = file_get_contents('php://input');
		try {
			if (!empty($postStr)) {
				libxml_disable_entity_loader(true);
				$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
				$pay_list = new PayList();
				$pay_info = $pay_list->readPay($postObj->out_trade_no);
				$pay_service = new Pay();
				$check_sign = $pay_service->checkSign($postObj, $postObj->sign, $pay_info["site_id"]);
				if ($postObj->result_code == 'SUCCESS' && $check_sign == 1) {
					$res = $pay_list->onlinePay($postObj->out_trade_no, $this->info['name'], $postObj->transaction_id);
					$xml = "<xml>
                                <return_code><![CDATA[SUCCESS]]></return_code>
                                <return_msg><![CDATA[支付成功]]></return_msg>
                            </xml>";
					echo $xml;
					return $res;
				} else {
					$xml = "<xml>
                                    <return_code><![CDATA[FAIL]]></return_code>
                                    <return_msg><![CDATA[支付失败]]></return_msg>
                                </xml>";
					echo $xml;
					return error();
				}
			}
		} catch (\Exception $e) {
			return error();
		}
	}
	
	/**
	 * 同步回调支付结果
	 * array $param
	 */
	public function payReturn($param = [])
	{
		$out_trade_no = $param['out_trade_no'];
		$msg = $param['msg'];
		
		return [ 'status' => $msg, 'out_trade_no' => $out_trade_no ];
	}
	
	/**
	 * 退款
	 * @param array $param
	 */
	public function refund($param = [])
	{
		$res = parent::refund($param);
		if (!$res) return error();
		$alipay_config = new WechatConfig();
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