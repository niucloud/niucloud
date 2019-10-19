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
namespace addon\system\OAuthLogin\api\controller;

use addon\system\OAuthLoginQQ\common\model\QQLoginConfig;
use addon\system\OAuthLoginWechat\common\model\WechatLoginConfig;
use app\common\controller\BaseApi;

/**
 * 控制器
 */
class Loginconfig extends BaseApi
{

    /**
     * 获取第三方登录配置
     */
    public function getQQLoginConfig($params)
    {

        $qqlogin_config_model = new QQLoginConfig();
        $config_info = $qqlogin_config_model->getQQLoginConfig($params['site_id']);
        return $config_info;
    }
    public function getWechatLoginConfig($params){
        
        $wechatlogin_config_model = new WechatLoginConfig();
        $config_info = $wechatlogin_config_model->getWechatLoginConfig($params['site_id']);
        return $config_info;
    }
}