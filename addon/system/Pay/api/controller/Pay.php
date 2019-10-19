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
namespace addon\system\Pay\api\controller;

use app\common\controller\BaseApi;
use addon\system\Pay\common\model\Pay as PayModel;

/**
 * 控制器
 */
class Pay extends BaseApi
{
    
    /**
     * 支付  
     * @param unknown $params ["port" => "端口", ]
     */
    public function doPay($params){
        
        $pay_model = new PayModel();
        $result = $pay_model->pay($params);
        return $result;
    }
    
    /**
     * 获取支付方式
     * @param unknown $params   "pay_scene" => ["wap", "wechat", "app", "pc", "wechat_applet"]
     */
    public function getPayType($params = []){
        $pay_model = new PayModel();
        $result = $pay_model->getPayType($params);
        return $result;
    }

    /**
     * 支付异步回调
     * @param array $param
     */
    public function payNotify($param = []){
        $res = hook('payNotify', []);
        return success($res);
    }
}