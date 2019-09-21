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

namespace addon\system\PayAlipay\common\model;

use addon\system\PayAlipay\common\sdk\AlipaySubmit;
use addon\system\PayAlipay\common\sdk\AopClient;
use addon\system\PayAlipay\common\sdk\request\AlipayTradeWapPayRequest;
use addon\system\PayAlipay\common\sdk\request\AlipayTradePagePayRequest;
use addon\system\PayAlipay\common\sdk\request\AlipayTradeAppPayRequest;

/**
 * 支付宝支付 
 */
class Pay
{
    
    public $aop;
    
    function __construct($site_id)
    {
        // 获取支付宝支付参数(统一支付到平台账户)
        $alipay_config_service = new AlipayConfig();
        $result = $alipay_config_service->getAlipayConfig($site_id);
        if(!empty($result["data"])){
            $alipay_new_config = $result["data"];
        }
        // 获取支付宝支付参数(统一支付到平台账户)
        $this->aop = new AopClient();
        $this->aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $this->aop->appId = $alipay_new_config['value']["appid"];
        $this->aop->rsaPrivateKey = $alipay_new_config['value']['private_key'];
        $this->aop->alipayrsaPublicKey = $alipay_new_config['value']['public_key'];
        $this->aop->alipayPublicKey = $alipay_new_config['value']['alipay_public_key'];
        $this->aop->apiVersion = '1.0';
        $this->aop->signType = 'RSA2';
        $this->aop->postCharset = 'UTF-8';
        $this->aop->format = 'json';
    }
    
    
    /**
     * 支付宝统一下单
     * @param unknown $out_trade_no 商户订单号,64个字符以内、可包含字母、数字、下划线；需保证在商户端不重复
     * @param unknown $subject 订单标题
     * @param unknown $body 订单描述
     * @param unknown $total_fee 订单总金额，单位为元，精确到小数点后两位，取值范围[0.01,100000000]。
     * @param unknown $notify_url 支付宝服务器主动通知商户服务器里指定的页面http/https路径。
     * @param unknown $return_url HTTP/HTTPS开头字符串
     * @param unknown $scene 使用场景
     * @return string|\addon\system\PayAlipay\common\sdk\提交表单HTML文本
     */
    public function doPay($out_trade_no, $subject, $body, $total_fee, $notify_url, $return_url, $scene)
    {
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "out_trade_no" => $out_trade_no,
            "subject" => $subject,
            "total_amount" => (float)$total_fee,
            "body" => $body,
            "product_code" => 'FAST_INSTANT_TRADE_PAY',
        );
        $parameter = json_encode($parameter);
        $parArr = [];
        switch ($scene)
        {
            case "wap":
                $request = new AlipayTradeWapPayRequest();
                break;
            case "pc":
                $request = new AlipayTradePagePayRequest();
                break;
            case "app":
                $request = new AlipayTradeAppPayRequest();
                break;
        }
        
        $request->setBizContent($parameter);
        $request->SetReturnUrl($return_url);
        $request->SetNotifyUrl($notify_url);
        $result = $this->aop->pageExecute($request, 'get');
        return $result;
    }
    
    /**
     * 获取配置参数是否正确
     *
     * @return unknown
     */
    public function getVerifyResult($params, $type)
    {
        $res = $this->aop->rsaCheckV1($params, $this->aop->alipayrsaPublicKey, $this->aop->signType);
        return $res;
    }
    
    
    /**
     * 支付宝原路退款
     * @param array $param
     */
    public function doRefundPay($param = [])
    {
        $alipay_config = $this->getAlipayConfig($param["site_id"]);
        $service = 'refund_fastpay_by_platform_nopwd';
        // 防钓鱼时间戳-安全
        $anti_phishing_key = "";
        // 若要使用请调用类文件submit中的query_timestamp函数
        
        // 客户端的IP地址-
        $exter_invoke_ip = "";
        // 非局域网的外网IP地址，如：221.0.0.1
        // 构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => $service,
            "partner" => trim($alipay_config["data"]['value']['parentid']),
            "seller_email" => trim($alipay_config["data"]['value']['appid']),
            "_input_charset" => trim(strtolower($alipay_config["data"]['value']['input_charset'])),
            "batch_no" => $param["refund_no"],
            "batch_num" => 1,
            "refund_date" => date("Y-m-d H:i:s", time()),
            "detail_data" => $param["trade_no"] . '^' . $param["refund_fee"] . '^' . '协商退款'
        );
        // 建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config["data"]['value']);
        
        $html_text = $alipaySubmit->buildRequestForm($parameter, "get", "确认");
        $test = $this->getHttpResponse($html_text);
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($test, 'SimpleXMLElement', LIBXML_NOCDATA);
        $retval = json_decode(json_encode($xmlstring), true);
        \think\Log::write("支付宝返回json:" . json_encode($xmlstring));
        \think\Log::write("支付宝返回params:" . json_encode($parameter));
        if ($retval['is_success'] == "T") {
            return success();
        } else {
            return error($retval['error']);
        }
    }
    
    /**
     * 远程获取数据
     * $url 指定URL完整路径地址
     *
     * @param $time_out 超时时间。默认值：60
     *            return 远程输出的数据
     */
    private function getHttpResponse($url, $time_out = "60")
    {
        $urlarr = parse_url($url);
        $errno = "";
        $errstr = "";
        $transports = "";
        $responseText = "";
        if ($urlarr["scheme"] == "https") {
            $transports = "ssl://";
            $urlarr["port"] = "443";
        } else {
            $transports = "tcp://";
            $urlarr["port"] = "80";
        }
        $fp = @fsockopen($transports . $urlarr['host'], $urlarr['port'], $errno, $errstr, $time_out);
        if (!$fp) {
            die("ERROR: $errno - $errstr<br />\n");
        } else {
            if (trim('utf-8') == '') {
                fputs($fp, "POST " . $urlarr["path"] . " HTTP/1.1\r\n");
            } else {
                fputs($fp, "POST " . $urlarr["path"] . '?_input_charset=' . 'utf-8' . " HTTP/1.1\r\n");
            }
            fputs($fp, "Host: " . $urlarr["host"] . "\r\n");
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-length: " . strlen($urlarr["query"]) . "\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $urlarr["query"] . "\r\n\r\n");
            while (!feof($fp)) {
                $responseText .= @fgets($fp, 1024);
            }
            fclose($fp);
            $responseText = trim(stristr($responseText, "\r\n\r\n"), "\r\n");
            return $responseText;
        }
    }
}
