<?php

namespace addon\system\PayWechat\common\sdk;

use addon\system\PayWechat\common\sdk\WxPayApi as WxPayApi;
use addon\system\PayWechat\common\sdk\WxPayData\WxPayUnifiedOrder;
use addon\system\PayWechat\common\sdk\WxPayData\WxPayRefund;
use addon\system\PayWechat\common\sdk\WxPayData\WxPayCloseOrder;
use addon\system\PayWechat\common\model\WechatConfig;
use addon\system\PayWechat\common\sdk\WxPayData\WxPayJsApiPay;
use addon\system\Wechat\common\model\Wechat;

/**
 * 功能说明：微信支付接口(应用于微信公众平台)
 * (只适用于参考，系统开发完成后删除)
 *
 */
class WeiXinPay
{
	private $token;     //access_token
	
	private $values;
	
	/**
	 * *********************************************微信支付参数******************************************
	 */
	protected $pay_appid;
	// 用于微信支付的公众号appid
	protected $pay_appsecret;
	// 用于微信支付的公众号appkey（在jsapi支付中使用获取openid，扫码支付不使用）
	protected $pay_mchid;
	// 用于微信支付的商户号
	protected $pay_mchkey;
	// 用于微信支付的商户秘钥
	
	protected $apiclient_cert;
	// 数字证书密钥
	protected $apiclient_key;
	// 数字证书key
	
	/**
	 * ********************************************小程序支付参数支付参数******************************************
	 */
	//小程序appid
	protected $applet_appid;
	//支付商户号
	protected $applet_mchid;
	//小程序key
	protected $applet_key;
	
	/**
	 * ********************************************微信支付参数结束****************************************
	 */
	
	function __construct($site_id = 0)
	{

	    //查询微信公众号的参数
	    $wechat_config_model = new Wechat();
	    $wechat_config = $wechat_config_model->getWechatConfigInfo($site_id);
	    //查询微信支付的参数
	    $wxpay_config_service = new WechatConfig();
	    $result = $wxpay_config_service->getWechatConfig($site_id);
        $wchat_config = [];
	    if(!empty($result["data"])){
	        $wchat_config = $result["data"];
	    }
	    $this->pay_appid = $wechat_config["data"]["value"]['appid'];
	    $this->pay_appsecret = $wechat_config["data"]["value"]['appsecret'];
	    $this->pay_mchid = $wchat_config['value']['mchid'];
	    $this->pay_mchkey = $wchat_config['value']['app_paysignkey'];

	}
	
	/*************认证接口*******************************************************************************************/

	
	/**
	 * 产生随机字符串，不长于32位
	 * @param int $length
	 * @return 产生的随机字符串
	 */
	public static function getNonceStr($length = 32)
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}	
	
	/**
	 * 格式化参数格式化成url参数
	 */
	public function ToUrlParams()
	{
		$buff = "";
		foreach ($this->values as $k => $v) {
			if ($k != "sign" && $v != "" && !is_array($v)) {
				$buff .= $k . "=" . $v . "&";
			}
		}
		$buff = trim($buff, "&");
		return $buff;
	}
	
	
	/**
	 * 设统一下单
	 * @param unknown $body 订单描述
	 * @param unknown $detail 订单详情
	 * @param unknown $total_fee 订单金额
	 * @param unknown $out_trade_no 订单编号
	 * @param unknown $red_url 异步回调域名
	 * @param unknown $trade_type 交易类型JSAPI、NATIVE、APP
	 * @param unknown $openid 支付人openid（jsapi支付必填）
	 * @param unknown $product_id 商品id(扫码支付必填)
	 * @return unknown
	 */
	public function setWeiXinPay($body, $detail, $total_fee, $out_trade_no, $notify_url, $trade_type, $openid, $product_id, $pay_name = '')
	{
		$WxPayApi = new WxPayApi();
		//②、统一下单
		$input = new WxPayUnifiedOrder();
		$input->SetBody($body);    //订单项描述
		$input->SetDetail($detail);
		$input->SetTotal_fee($total_fee);  //总金额
		$input->SetAttach(1);  //附加数据orderId
		$input->SetOut_trade_no($out_trade_no);  //商户订单流水号
		$input->SetTime_start(date("YmdHis"));  //交易起始时间
		$input->SetTime_expire(date("YmdHis", time() + 600));   //交易结束时间
		$input->SetGoods_tag("商品标记");   //商品标记
		$input->SetNotify_url($notify_url);   //接收微信支付成功通知地址
		$input->SetTrade_type($trade_type); //交易类型JSAPI、NATIVE、APP
		$input->SetOpenid($openid); //用户标识
		$input->SetProduct_id($product_id); //产品标识
		$input->SetSpbill_create_ip($this->getIp());
		if($trade_type == 'MWEB')
		{
		    $h5_array = array(
		        "h5_info" => array(
		            "type" => 'Wap',
		            "wap_url" =>  $notify_url,
		            "wap_name" => $pay_name
		        )
		    );
		    $h5_json =  json_encode($h5_array);
		    $input->SetScene_info($h5_json);
		}
        $WxPayApi->setConfig($this->pay_appid, $this->pay_mchid, $this->pay_mchkey, $this->apiclient_cert, $this->apiclient_key);
		$order = $WxPayApi->unifiedOrder($input, 30);
		return $order;
	}
	
	/**
	 * 订单项目退款
	 * @param unknown $refund_no
	 * @param unknown $out_trade_no
	 * @param unknown $refund_fee
	 * @param unknown $total_fee
	 * @param unknown $transaction_id
	 * @return \addon\system\PayWechat\common\sdk\weixin\成功时返回，其他抛异常
	 */
	public function setWeiXinRefund($refund_no, $out_trade_no, $refund_fee, $total_fee)
	{
		$WxPayApi = new WxPayApi();
		$input = new WxPayRefund();
		$input->SetOut_refund_no($refund_no);
		$input->SetOut_trade_no($out_trade_no);
		$input->SetRefund_fee($refund_fee);
		$input->SetTotal_fee($total_fee);
		// $input->SetTransaction_id($transaction_id);
		$order = $WxPayApi->refund($input, 30);
		return $order;
	}
    /**
     * 获取ip
     * @return mixed
     */
	public function getIp()
	{
	    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
	        $ip = $_SERVER['HTTP_CLIENT_IP'];
	    } else {
	        $ip = $_SERVER['REMOTE_ADDR'];
	    }
	    $ip_arr = explode(',', $ip);
	    return $ip_arr[0];
	}
	
	/**
	 * 订单关闭
	 * @param unknown $orderNumber
	 * @return unknown
	 */
	public function setOrderClose($orderNumber)
	{
	    try {
	        $WxPayApi = new WxPayApi();
	        $input = new WxPayCloseOrder();
	        $input->SetOut_trade_no($orderNumber);
	        $result = $WxPayApi->closeOrder($input);
	        return $result;
	    } catch (\Exception $e)
	    {
	        return $e->getMessage();
	    }
	    
	}
	
	/**
	 * 检测签名串
	 *
	 * @param unknown $postObj
	 */
	public function checkSign($postObj, $sign)
	{
	    $jsapi = new WxPayJsApiPay();
	    $this->values = json_decode(json_encode($postObj), true);
	    $make_sign = $this->MakeSign();
	    if ($make_sign == $sign) {
	        return 1;
	    } else {
	        return 0;
	    }
	}
	
	
	/**
	 * 生成签名
	 *
	 * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
	 */
	public function MakeSign()
	{
	    // 签名步骤一：按字典序排序参数
	    ksort($this->values);
	    $string = $this->ToUrlParams();
	    // 签名步骤二：在string后加入KEY
	    $string = $string . "&key=" . $this->pay_mchkey;
	    
	    // 签名步骤三：MD5加密
	    $string = md5($string);
	    // 签名步骤四：所有字符转为大写
	    $result = strtoupper($string);
	    return $result;
	}
	/**
	 *
	 * 获取jsapi支付的参数
	 *
	 * @param array $UnifiedOrderResult
	 *            统一支付接口返回的数据
	 * @throws WxPayException
	 * @return json数据，可直接填入js函数作为参数
	 */
	public function GetJsApiParameters($UnifiedOrderResult)
	{
	    if (! array_key_exists("appid", $UnifiedOrderResult) || ! array_key_exists("prepay_id", $UnifiedOrderResult) || $UnifiedOrderResult['prepay_id'] == "") {
	        return json_encode($UnifiedOrderResult);
	    }
	    $jsapi = new WxPayJsApiPay();
	    $jsapi->SetAppid($this->pay_appid);
	    $jsapi->SetTimeStamp(date("YmdHis"));
	    $jsapi->SetNonceStr($this->getNonceStr());
	    $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
	    $jsapi->SetSignType("MD5");
	    $jsapi->SetPaySign($jsapi->MakeSign($this->pay_mchkey));
	    $parameters = json_encode($jsapi->GetValues());
	    return $parameters;
	}
}