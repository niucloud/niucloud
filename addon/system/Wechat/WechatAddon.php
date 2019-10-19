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
namespace addon\system\Wechat;

use addon\system\Wechat\common\model\Message;
use addon\system\Wechat\common\model\WechatMessage;
use app\common\controller\BaseAddon;
use addon\system\Wechat\common\model\Wechat;
use addon\system\Wechat\sitehome\controller\Material;

/**
 * 微信插件
 */
class WechatAddon extends BaseAddon
{
	
	public $info = array(
		'name' => 'Wechat',
		'title' => '微信公众号',
		'description' => '微信公众号管理',
		'status' => 1,
		'author' => '',
		'version' => '1.0',
		'visble' => 0,
		'type' => 'ADDON_SYSTEM',
		'category' => 'SYSTEM',
		'content' => '微信公众号管理',
		//预置插件，多个用英文逗号分开
		'preset_addon' => '',
		'support_addon' => '',
		'support_app_type' => 'wap,weapp'
	);
	
	public $config;
	
	public function __construct()
	{
		parent::__construct();
		$this->config = $this->config_info;
	}
	
	/**
	 * 安装
	 */
	public function install()
	{
		return success();
	}
	
	/**
	 * 卸载
	 */
	public function uninstall()
	{
		return error('', 'System addon can not be uninstalled!');
	}
	
	/**
	 * 初始化站点数据，在添加站点的时候用
	 * @param integer $site_id
	 * @return boolean
	 */
	public function addToSite($site_id)
	{
		$wechat_config = new Wechat();

        $json_data = array(
            "appid" => '',
            "appsecret" => '',
            "token" => '',
            "encodingaeskey" => '',
        );
        $data = array(
            "site_id" => $site_id,
            "value" => json_encode($json_data),
            "create_time" => time(),
            "status" => 1
        );
		$res = $wechat_config->setWechatConfig($data);
		if ($res['code'] != 0) {
			return $res;
		}
		$res = $wechat_config->setWechatMenu($site_id, '');
		
		$data = [
			'site_id' => $site_id,
			'rule_name' => '',
			'rule_type' => 'AFTER',
			'keywords_json' => '',
			'replay_json' => '',
			'create_time' => time()
		];
		$wechat_config->addRule($data);
		
		return $res;
	}
	
	/**
	 * 删除站点数据--删除站点时调用
	 *
	 * @param integer $site_id
	 * @return boolean
	 */
	public function delFromSite($site_id)
	{
		$wechat_config = new Wechat();
		$wechat_config->deleteSite($site_id);
		return success();
	}
	
	/**
	 * 复制站点数据--复制站点时调用
	 *
	 * @param integer $site_id
	 * @param integer $new_site_id
	 * @return boolean
	 */
	public function copyToSite($site_id, $new_site_id)
	{
		$wechat_config = new Wechat();
		
		$wechat_config_info = $wechat_config->getWechatConfigInfo($site_id);
		if (!empty($wechat_config_info['data'])) {
			$info = $wechat_config_info['data']['value'];
			$appid = $info['appid'];
			$appsecret = $info['appsecret'];
			$token = $info['token'];

			$json_data = array(
			    "appid" => $info['appid'],
                "appsecret" => $info['appsecret'],
                "token" => $info['token'],
                "encodingaeskey" => $info['encodingaeskey'],
            );
			$data = array(
			    "site_id" => $site_id,
                "value" => json_encode($json_data),
                "create_time" => time(),
                "status" => $wechat_config_info['data']["status"]
            );
			$wechat_config->setWechatConfig($data);
		}
		
		$wechat_menu_info = $wechat_config->getWechatMenuInfo($site_id);
		if (!empty($wechat_menu_info['data'])) {
			$info = $wechat_menu_info['data']['value'];
			$wechat_config->setWechatMenu($new_site_id, $info);
		}
		return success();
	}
	
	/**
	 * 图文消息管理
	 */
	public function materialMannager($param = [])
	{
		$material = new Material();
		$result = $material->materialMannager();
		$return_array = array_merge($result[1], $param);
		return $this->fetch($result[0], $return_array, $result[2]);
	}
	
	public function wechatMsg($param = [])
	{
		$wechat_config = new Wechat();
		$res = $wechat_config->sendTemplateMsg(request()->siteid(), $param);
		return $res;
	}
	
	/**
	 * 授权登录
	 * @param array $params
	 */
	public function oAuthLogin($params = [])
	{
		if ($params['name'] == $this->info['name']) {
			$wechat_model = new Wechat();
			$wechat_config = $wechat_model->getWechatConfigInfo($params['site_id']);
			
			if (empty($wechat_config['data']['value'])) {
				$this->error('站点未配置微信公众号');
			} else {
				$value = $wechat_config['data']['value'];
				if (empty($value['appid']) || empty($value['appsecret'])) {
					$this->error('请配置您公众号的AppID和AppSecret');
				} else {
					$redirect_url = addon_url('Wechat://common/login/callback', [ 'site_id' => $params['site_id'] ]);
					$get_request_code_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $value['appid'] . '&redirect_uri=' . $redirect_url . '&response_type=code&scope=snsapi_userinfo&state=niucloud#wechat_redirect';
					$this->redirect($get_request_code_url);
				}
			}
		}
	}
	
	/**
	 * 获取qq登录配置信息(non-PHPdoc)
	 */
	public function getOAuthLoginConfig($param = [])
	{
	    $config_info = [];
		$config_info["info"] = $this->info;
		$config_info["info"]['title'] = "微信公众号登录";
		$config_model = new Wechat();
		
		$config_result = $config_model->getWechatConfigInfo($param['site_id']);
		$config_info['config'] = $config_result["data"];
		$config_info["info"]['icon'] = __ROOT__ . '/addon/system/Wechat/sitehome/view/public/img/wechat.png';
		$config_info["info"]['config_url'] = addon_url('Wechat://sitehome/config/config');
		$config_info["info"]['cn'] = '微信公众号';
		$config_info["info"]['en'] = 'wechat';
		$config_info["info"]["url"] = addon_url("Wechat://sitehome/config/config");
		return $config_info;
		
	}
	
	/**
	 * 获取第三方登录类型，用于前端调用第三方登录相关类型，注意端口配置
	 * @param array $param
	 */
	public function getOAuthLoginType($param = [])
	{
	    $config_model = new Wechat();
	    $config_result = $config_model->getWechatConfigInfo($param['site_id']);
	    if($config_result["data"]["status"] == 1){
	        $config_info = $this->info;
	        $config_info['title'] = "微信公众号登录";
	        $config_info['icon'] = __ROOT__ . '/addon/system/Wechat/sitehome/view/public/img/wechat.png';
	        $config_info['cn'] = '微信公众号';
	        $config_info['en'] = 'wechat';
	        return $config_info;
	    }
	}

    /**
     * 获取watch的AccessToken(包含openid)
     * @param array $param
     */
	public function getOAuthAccessToken($param = []){
        $weatch_model = new Wechat();
        $res = $weatch_model->getOAuthAccessToken(["site_id" => $param["site_id"]]);
        return $res;
    }

    /**
     * 微信模板消息
     * @param array $param
     */
    public function wechatMessage($param = []){
        $wechat_message = new WechatMessage();
        $res = $wechat_message->sendMessage($param);
        return $res;
    }

    /**
     * 编辑消息模板
     * @param array $param
     */
    public function doEditMessage($param = [])
    {
        if ($param["name"] == "Wechat") {
            $this->redirect(addon_url('Wechat://sitehome/message/edit', [ 'keyword' => $param['keyword'] ]));
        }
    }

    /**
     * 邮箱消息延时发送
     * @param array $param
     */
    public function cronMessageSend($param = [])
    {
        $wechat_message = new WechatMessage();
        $res = $wechat_message->cronMessageSend($param);
        return $res;
    }
}