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

namespace addon\system\Wechat\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\model\Config as  ConfigModel;
class Config extends BaseAdmin
{
	protected $replace = [];
	
	public function __construct()
	{
		parent::__construct();
        $this->replace = [
            'ADDON_WECHAT_NIUCLUD_JS' => __ROOT__ . '/addon/system/Wechat/admin/view/public/js'
        ];
	}

    /**
     * 微信开放平台配置
     */
    public function wechat()
    {
        $config_model = new ConfigModel();
        if (IS_AJAX) {
            $app_id = input('app_id', '');
            $app_secret = input('app_secret', '');
            $token = input('token', '');
            $encodingaeskey = input('encodingaeskey', '');
            $status = input('status', 0);
            $json_data = array(
                "app_id" => $app_id,
                "app_secret" => $app_secret,
                "token" => $token,
                "encodingaeskey" => $encodingaeskey,
            );

            $wechat_config_result = $config_model->getConfigInfo(['name' => "WECHAT_PLATFORM_CONFIG"]);
            if(empty($wechat_config_result["data"])){
                $data = [
                    'value' => json_encode($json_data),
                    'name' => "WECHAT_PLATFORM_CONFIG",
                    'type' => 0,
                    'title' => "微信开放平台",
                    'remark' => "微信开放平台配置",
                    'status' => $status,
                    'create_time' => time()
                ];
                $res = $config_model->addConfig($data);
            }else{
                $data = array(
                    "value" => json_encode($json_data),
                    "status" => $status,
                    'name' => "WECHAT_PLATFORM_CONFIG"
                );
                $res = $config_model->editConfig($data, [ 'name' => "WECHAT_PLATFORM_CONFIG" ]);
            }
            return $res;
        }else{
            $wechat_info_result = $config_model->getConfigInfo([ 'name' => 'WECHAT_PLATFORM_CONFIG' ]);
            $wechat_info_result['data']['value'] = json_decode($wechat_info_result['data']['value'], true);
            $this->assign('wechat_info', $wechat_info_result['data']);
            $host = request()->host();
            $root = request()->root();
            $data_url = array(
                'host' => $host,
                'auth_msg_url' => $host . '/wechat/common/config/index',
                'msg_url' => $host . '/wechat/common/config/getPlatformMessage/$APPID$/',
                'open_url' => $host
            );
            $this->assign("data_url", $data_url);
            return $this->fetch('config/wechat',[],$this->replace);
        }
    }
}