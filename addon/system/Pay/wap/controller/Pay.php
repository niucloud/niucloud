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

namespace addon\system\Pay\wap\controller;

use app\common\controller\BaseSite;
use addon\system\Pay\api\controller\Pay as PayApi;
use addon\system\Pay\common\model\PayList;

/**
 * 支付管理控制器
 */
class Pay extends BaseSite
{

    /**
     * 支付信息页
     * @param number $site_id
     * @param string $out_trade_no
     * @param string $product_name
     * @param string $out_trade_no
     * @param string $pay_money
     * @param string $pay_no 支付的会员唯一值
     * @param string $notify_url 异步回调地址
     * @param string $return_url 同步回调地址
     */
    public function index()
    {
        $pay_data = input('data', '');
        $pay_info = decrypt($pay_data, $this->site_info['app_secret']);
        $pay_info = json_decode($pay_info, true);
        $this->assign('pay_info', $pay_info);
        $pay_type_result = api("Pay.Pay.getPayType", ['site_id' => $pay_info["site_id"], 'pay_scene' => "wap"]);
        $pay_type = $pay_type_result["data"];
        $this->assign('pay_type', $pay_type);
        $pay_info = json_encode($pay_info);
        $encrypt_data = encrypt($pay_info, $this->site_info['app_secret']);
        $this->assign('pay_data', $encrypt_data);
        return $this->fetch('pay/index');
    }

    /**
     * 支付
     */
    public function payment()
    {
        //组装需要的的各项数据
        $pay_data = input('data', '');
        $pay_info = decrypt($pay_data, $this->site_info['app_secret']);
        $pay_info = json_decode($pay_info, true);
        $pay_info['type'] = input('type', '');
        //调用api接口
        $res = api("Pay.Pay.doPay",$pay_info);
        if($res['code'] == 0){
            if($res['data']["type"] == "url"){
                $this->redirect($res['data']["url"]);
            }
        }
    }
    
    /**
     * 微信公众号支付
     */
    public function wechatJsapi(){
        $pay_list_model = new PayList();
        
        $jsApiParams = decrypt(input('jsApiParams', ''));
        $out_trade_no = decrypt(input('out_trade_no', ''));
        
        $pay_data = $pay_list_model->readPay($out_trade_no);
        if(empty($pay_data)){
            $this->error('未获取到支付信息');
        }

        $this->assign("pay_data",$pay_data);
        $this->assign('jsApiParams', $jsApiParams);
        
        return $this->fetch('pay/wechatJsapi');
    }

}