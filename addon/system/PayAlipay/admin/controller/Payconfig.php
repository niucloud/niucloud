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

namespace addon\system\PayAlipay\admin\controller;

use addon\system\PayAlipay\common\model\AlipayConfig;
use app\common\controller\BaseAdmin;

/**
 * 支付设置
 */
class Payconfig extends BaseAdmin
{
    /**
     * 支付宝支付配置
     */
    public function index(){
        $alipay_config = new AlipayConfig();
        $site_id = request()->siteid();
        if(IS_AJAX){
            $value = input('value', '');
            $status = input('status', 1);
            $result = $alipay_config->setAlipayConfig($site_id, $value, $status);
            return $result;
        }else{
            $config = $alipay_config->getAlipayConfig($site_id);
            $this->assign('info',$config['data']);
            $this->assign('status',$config['data']['status']);
            return $this->fetch('pay_config/index');
        }
    }
 
}
