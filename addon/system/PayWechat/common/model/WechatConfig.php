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
use addon\system\PayWechat\common\sdk\WxPayApi;
use addon\system\PayWechat\common\sdk\WxPayData\WxPayRefund;
use app\common\model\Site;

/**
 * 微信支付
 * @author lzw
 */
class WechatConfig
{

    
    public $site_model;
    public function __construct()
    {
        $this->site_model = new Site();
    }
   /**
     * 设置指定站点的微信支付配置
     * @param number $site_id
     * @param string $value
     * @param number $status
     */
    public function setWechatConfig($data){     
        
        $data["name"] = 'NC_PAYWECHAT_CONFIG';
        $condition = array(
            "name" => 'NC_PAYWECHAT_CONFIG'
        );
        $res = $this->site_model->setSiteConfig($data);
        return $res;
    }
    
    /**
     * 获取指定站点的微信支付配置
     * @param number $site_id
     */
    public function getWechatConfig($site_id){
        
        $config = $this->site_model->getSiteConfigInfo(["site_id" => $site_id, "name" => "NC_PAYWECHAT_CONFIG"]);
        $value = [];
        if(!empty($config["data"]["value"])){
            $value = json_decode($config["data"]["value"], true);
        }
        $config["data"]["value"] = $value;
        return $config;
    }
    
    
    /**
     * 删除该站点的支付宝配置
     * @param number $site_id
     * Returns:['code' => 0|1, 'message' => '', 'data' => []]
     */
    public function delWechatConfig($site_id){
    
        $res = $this->site_model->deleteSiteConfig([ 'name' => 'NC_PAYWECHAT_CONFIG', 'site_id' => $site_id ]);
        return $res;
    }

    /**
     * 退款
     * @param array $param
     * @return array
     */
    public function doRefundPay($param = []){
        $weixin_config = $this->getWechatConfig($param["site_id"]);
        $WxPayApi = new WxPayApi();
        $WxPayApi->initWxPay($weixin_config["data"]["value"]["app_id"], $weixin_config["data"]["value"]["num"],$weixin_config["data"]["value"]["app_PaySignKey"], $weixin_config["data"]["value"]["apiclient_cert"], $weixin_config["data"]["value"]["apiclient_key"]);
        $input = new WxPayRefund();
        $input->SetOut_refund_no($param["refund_no"]);
        $input->SetOut_trade_no($param["out_trade_no"]);
        $input->SetRefund_fee($param["refund_fee"] * 100);
        $input->SetTotal_fee($param["total_money"] * 100);
        // $input->SetTransaction_id($transaction_id);
        try {
            $order = $WxPayApi->refund($input, 30);
            $msg = '操作成功';
            // 检测签名配置是否正确
            if ($order['return_code'] == "FAIL") {

                $is_success = 0;
                $msg = $order['return_msg'];
            } else {
                // 检查退款业务是否正确
                if ($order['result_code'] == "FAIL") {

                    $is_success = 0;
                    $msg = $order['err_code_des'];
                } else {
                    $is_success = 1;
                }
            }
            if($is_success){
                return success();
            }else {
                return error();
            }
            
            
        } catch (\Exception $e) {
            return error($e->getMessage());
        }
    }
    
    
}
