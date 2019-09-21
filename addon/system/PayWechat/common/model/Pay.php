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

namespace addon\system\PayWechat\common\model;
use addon\system\PayWechat\common\sdk\WeiXinPay;

/**
 * 微信支付
 * @author lzw
 */
class Pay
{

    
    /**
     * 执行微信支付
     */
    public function doPay($param)
    {
        
        $weixin_pay = new WeiXinPay($param["site_id"]);
        
        if ($param["trade_type"] == 'JSAPI') {
            //获取用户的open_id
            $open_result = hook("getOAuthAccessToken", ["site_id" => $param["site_id"]]);
            if($open_result["code"] != 0){
                return error();
            }
            $openid = $open_result["data"]["open_id"];
            $product_id = '';
        }
        if ($param["trade_type"] == 'NATIVE') {
            $openid = '';
            $product_id = $param['out_trade_no'];
        }
        if ($param["trade_type"] == 'MWEB') {
            $openid = '';
            $product_id = $param['out_trade_no'];
        }
        if ($param["trade_type"] == 'APPLET') {
            $openid = $param["applet_openid"];
            $product_id = '';
        }
        if ($param["trade_type"] == 'APP') {
            $openid = "";
            $product_id = '';
        }
        $retval = $weixin_pay->setWeiXinPay($param["pay_body"], $param["pay_detail"], $param['pay_money'] * 100, $param['out_trade_no'], $param['notify_url'], $param["trade_type"], $openid, 200, $param['pay_name']);
        return $retval;
    }
    /**
     * 获取微信jsapi
     */
    public function getWxJsApi($UnifiedOrderResult, $site_id)
    {
        $weixin_pay = new WeiXinPay($site_id);
        $retval = $weixin_pay->GetJsApiParameters($UnifiedOrderResult);
        return $retval;
    }
    
    /**
     * 微信支付检测签名串
     */
    public function checkSign($post_obj, $sign, $site_id)
    {
        $weixin_pay = new WeiXinPay($site_id);
        $retval = $weixin_pay->checkSign($post_obj, $sign);
        return $retval;
    }
    
    /**
     * 微信退款
     */
    public function setWeiXinRefund($refund_no, $out_trade_no, $refund_fee, $total_fee, $site_id)
    {
        $weixin_pay = new WeiXinPay($site_id);
        $retval = $weixin_pay->setWeiXinRefund($refund_no, $out_trade_no, $refund_fee, $total_fee);
        return $retval;
    }
    
    /**
     * 提现 微信转账
     */
    public function wechatTransfers($openid, $partner_trade_no, $amount, $realname, $desc, $site_id)
    {
        $weixin_pay = new WeiXinPay($site_id);
        $retval = $weixin_pay->EnterprisePayment($openid, $partner_trade_no, $amount, $realname, $desc);
        return $retval;
    }
    
    /**
     * 订单关闭
     * @param $out_trade_no
     * @return unknown
     */
    public function setOrderClose($out_trade_no, $site_id){
        $weixin_pay = new WeiXinPay($site_id);
        $retval = $weixin_pay->setOrderClose($out_trade_no);
        return $retval;
    }
}
