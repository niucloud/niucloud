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
namespace addon\system\OAuthLoginWechat;

use addon\system\OAuthLoginWechat\common\model\WechatLoginConfig;
use addon\system\OAuthLoginWechat\common\sdk\WechatLogin;
use app\common\controller\BaseOAuthLoginAddon;

/**
 * 微信开放平台登录插件
 */
class OAuthLoginWechatAddon extends BaseOAuthLoginAddon
{
	public $info = array(
		'name' => 'OAuthLoginWechat',
		'title' => '微信开放平台登录',
		'description' => '微信开放平台登录',
		'status' => 1,
		'author' => '',
		'version' => '1.0',
		'visble' => 1,
		'type' => 'ADDON_SYSTEM',
		'category' => 'SYSTEM',
		'content' => 'this is a file!',
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
	 * 初始化站点数据，在添加站点的时候用
	 * @param integer $site_id
	 * @return boolean
	 */
	public function addToSite($site_id)
	{
		return success();
	}
	
	/**
	 * 删除站点数据--删除站点时调用
	 * @param integer $site_id
	 * @return boolean
	 */
	public function delFromSite($site_id)
	{
		$wechat_login_config_model = new WechatLoginConfig();
		$wechat_login_config_model->deleteWechatLoginConfig($site_id);
		return success();
	}
	
	/**
	 * 复制站点数据--复制站点时调用
	 * @param integer $site_id
	 * @param integer $new_site_id
	 * @return boolean
	 */
	public function copyToSite($site_id, $new_site_id)
	{
		$wechat_login_config_model = new WechatLoginConfig();
		$config_info = $wechat_login_config_model->getWechatLoginConfig($site_id);
		if (!empty($config_info['data'])) {
			$config_info['data']['site_id'] = $new_site_id;
			unset($config_info['data']['id']);
			$wechat_login_config_model->setWechatLoginConfig($new_site_id, json_encode($config_info['data']['value']), $config_info['data']['status']);
		}
		return success();
	}
	
	/**
	 * 获取微信登录配置信息(non-PHPdoc)
	 */
	public function getOAuthLoginConfig($param = [])
	{
		//针对非手机浏览器
		if (IS_MOBILE) {
			return [];
		}
		$config_info = [];
		$config_info["info"] = $this->info;
		$wechat_login_config_model = new WechatLoginConfig();
		$config_result = $wechat_login_config_model->getWechatLoginConfig($param['site_id']);
		$config_info['config'] = $config_result["data"];
		$config_info["info"]['icon'] = 'http://b2c.niuteam.cn/template/wap/default_new/public/images/weixin.png';
		$config_info["info"]['config_url'] = addon_url('OAuthLoginWchat://sitehome/index/config');
		$config_info["info"]['cn'] = '微信开放平台';
		$config_info["info"]['en'] = 'wechat';
		$config_info["info"]["url"] = addon_url("OAuthLoginWechat://sitehome/index/config");
		return $config_info;
		
	}
	
	/**
	 * 获取第三方登录类型，用于前端调用第三方登录相关类型，注意端口配置
	 * @param array $param
	 */
	public function getOAuthLoginType($param = [])
	{
	    //针对非手机浏览器
	    if (IS_MOBILE) {
	        return [];
	    }
	    $config_model = new WechatLoginConfig();
	    $config_result = $config_model->getWechatLoginConfig($param['site_id']);
		
		if($config_result["data"]["status"] == 1){
		    $config_info = $this->info;
		    $config_info['icon'] = 'http://b2c.niuteam.cn/template/wap/default_new/public/images/weixin.png';
		    $config_info['cn'] = '微信开放平台';
		    $config_info['en'] = 'wechat';
		    
		    return $config_info;
		}
	}
	
	
	/**
	 * 实现钩子
	 * @param unknown $param
	 */
	public function oAuthLogin($param = [])
	{
		if ($param['name'] == $this->info['name']) {
			
			$wechat_login_config_model = new WechatLoginConfig();
			$wchat_login_config = $wechat_login_config_model->getWechatLoginConfig($param['site_id']);
			
			if (empty($wchat_login_config['data']['value'])) {
				$this->error('站点未配置微信登录');
			} else {
				$value = $wchat_login_config['data']['value'];
				if (empty($value['app_key']) || empty($value['app_secret'])) {
					$this->error('请配置您申请的APP_KEY和APP_SECRET');
				} else {
					$redirect_url = addon_url('OAuthLoginWechat://common/login/callback', [ 'site_id' => $param['site_id'] ]);
					$wchat_login_api = new WechatLogin($value['app_key'], $value['app_secret'], $redirect_url);
					$request_url = $wchat_login_api->getRequestCodeURL();
					$this->redirect($request_url);
				}
			}
		}
	}
	
}