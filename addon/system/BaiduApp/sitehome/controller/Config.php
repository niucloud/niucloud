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
namespace addon\system\BaiduApp\sitehome\controller;

use addon\system\BaiduApp\common\model\BaiduApp as BaiduAppModel;
use app\common\model\Site;
use app\common\model\Addon;
use app\common\model\DiyView;

/**
 * 微信小程序基础配置
 */
class Config extends Base
{
	/**
	 * 功能设置
	 */
	public function setting()
	{
		return $this->fetch('config/setting', [], $this->replace);
	}
	
	/**
	 * 访问统计
	 * @return mixed
	 */
	public function accessStatistics()
	{
		return $this->fetch('config/access_statistics', [], $this->replace);
		
	}
	
	/**
	 * 版本管理
	 */
	public function version()
	{
		
		return $this->fetch('config/version', [], $this->replace);
	}
	
	/**
	 * 小程序管理
	 */
	public function management()
	{
		
		return $this->fetch('config/management', [], $this->replace);
	}

    /**
     * 百度小程序配置
     */
	public function config(){
        $baiduapp_model = new BaiduAppModel();
        if (IS_AJAX) {
            $baiduapp_name = input('baiduapp_name', '');
            $baiduapp_code = input('baiduapp_code', '');
            $appid = input('appid', '');
            $appkey = input('appkey', '');
            $appsecret = input('appsecret', '');
            $json_data = array(
                "baiduapp_name" => $baiduapp_name,
                "baiduapp_code" => $baiduapp_code,
                "appkey" => $appkey,
                "appid" => $appid,
                "appsecret" => $appsecret,
            );
            $data = array(
                "site_id" => SITE_ID,
                "value" => json_encode($json_data)
            );
            $res = $baiduapp_model->setBaiduAppConfig($data);
            return $res;
        } else {
            $config_info_result = $baiduapp_model->getBaiduAppConfigInfo($this->siteId);
            $config_info = $config_info_result['data']['value'];
            $this->assign("config_info", $config_info);
            return $this->fetch('config/config', [], $this->replace);
        }

    }
	
}