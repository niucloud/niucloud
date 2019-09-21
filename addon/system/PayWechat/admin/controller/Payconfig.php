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

namespace addon\system\PayWechat\admin\controller;

use app\common\controller\BaseAdmin;
use addon\system\PayWechat\common\model\WechatConfig;

/**
 * 支付设置
 */
class Payconfig extends BaseAdmin
{
    /**
     * 微信支付配置
     */
    public function index(){
        $wechat_config = new WechatConfig();
        $site_id = request()->siteid();
        if(IS_AJAX){
            $value = input('value', '');
            $status = input('status', 1);
            $result = $wechat_config->setWechatConfig($site_id, $value, $status);
            return $result;
        }else{
            $config = $wechat_config->getWechatConfig($site_id);
            $this->assign('info',$config['data']);
            $this->assign('status',$config['data']['status']);
            return $this->fetch('pay_config/index');
        }
    }
 
}
