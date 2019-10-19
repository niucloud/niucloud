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

namespace addon\system\Pay\common\model;

use think\Cache;
use addon\system\Pay\common\model\PayList;
/**
 * 支付
 */
class Pay
{
    /**
     * 生成支付
     * @param $params
     * @return array|string|void
     */
    public function pay($params){
        //写入日志
        $pay_model = new PayList();
        $pay_model->writePay($params['site_id'], $params['out_trade_no'], $params['type'], $params['pay_body'],$params['pay_detail'], $params['pay_money'], '', $params['notify_url'], $params['return_url']);

        //跳转钩子支付
//        addon_url("Article://wap/reward/rewardSuccess", ['out_trade_no'=>$out_trade_no])
        $notify_url = __ROOT__."/pay.php";
        $return_url = addon_url("Pay://common/pay/payReturn");
        $res = hook('pay', ['site_id'=> $params['site_id'], 'name' => $params['type'], 'pay_body' => $params['pay_body'], 'pay_detail' => $params['pay_detail'],'out_trade_no' => $params['out_trade_no'], 'pay_scene' => $params['pay_scene'], "pay_money" => $params['pay_money'], "notify_url" => $notify_url, "return_url" => $return_url]);
        return $res[0];
    }

    /**
     * 获取支付方式
     * @param unknown $params   "pay_scene" => ["wap", "wechat", "app", "pc", "wechat_applet"]
     */
    public function getPayType($params = []){
        $res = hook('getPayType', ['site_id'=> $params['site_id'], 'pay_scene' => $params['pay_scene']]);
        return success($res);
    }
}
