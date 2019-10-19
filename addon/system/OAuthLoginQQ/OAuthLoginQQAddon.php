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
namespace addon\system\OAuthLoginQQ;

use app\common\controller\BaseOAuthLoginAddon;
use addon\system\OAuthLoginQQ\common\model\QQLoginConfig;
use addon\system\OAuthLoginQQ\common\sdk\QQLogin;


/**
 * QQ互联登录插件
 */
class OAuthLoginQQAddon extends BaseOAuthLoginAddon
{
	public $info = array(
		'name' => 'OAuthLoginQQ',
		'title' => 'QQ互联登录',
		'description' => 'QQ互联登录',
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
	 * 卸载
	 */
	public function uninstall()
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
		$qqlogin_config_model = new QQLoginConfig();
		$res = $qqlogin_config_model->deleteQQLoginConfig($site_id);
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
		$qqlogin_config_model = new QQLoginConfig();
		$config_info = $qqlogin_config_model->getQQLoginConfig($site_id);
		if (!empty($config_info['data'])) {
			$config_info['data']['site_id'] = $new_site_id;
			unset($config_info['data']['id']);
			$qqlogin_config_model->setQQLoginConfig($new_site_id, json_encode($config_info['data']['value']), $config_info['data']['status']);
		}
		
		return true;
	}
	
	/**
	 * 获取qq登录配置信息(non-PHPdoc)
	 */
	public function getOAuthLoginConfig($param = [])
	{
	    $config_info = [];
		$config_info["info"] = $this->info;
		$qqlogin_config_model = new QQLoginConfig();
		$config_result = $qqlogin_config_model->getQQLoginConfig($param['site_id']);
		$config_info['config'] = $config_result["data"];
		$config_info["info"]['icon'] = __ROOT__ . '/addon/system/OAuthLoginQQ/sitehome/view/public/img/qq.png';
		$config_info["info"]['config_url'] = addon_url('OAuthLoginQQ://sitehome/index/config');
		$config_info["info"]['cn'] = 'QQ';
		$config_info["info"]['en'] = 'qq';
		$config_info["info"]["url"] = addon_url("OAuthLoginQQ://sitehome/index/config");
		return $config_info;
		
	}
	
	/**
	 * 获取第三方登录类型，用于前端调用第三方登录相关类型，注意端口配置
	 * @param array $param
	 */
	public function getOAuthLoginType($param = [])
	{
	    $qqlogin_config_model = new QQLoginConfig();
	    $config_result = $qqlogin_config_model->getQQLoginConfig($param['site_id']);
	    
	    if($config_result["data"]["status"] == 1){
	        $config_info = $this->info;
	        $config_info['icon'] = __ROOT__ . '/addon/system/OAuthLoginQQ/sitehome/view/public/img/qq.png';
	        $config_info['cn'] = 'QQ';
	        $config_info['en'] = 'qq';
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
			$qqlogin_config_model = new QQLoginConfig();
			$qq_login_config = $qqlogin_config_model->getQQLoginConfig($param['site_id']);
			
			if (empty($qq_login_config['data']['value'])) {
				$this->error('站点未配置QQ登录');
			} else {
				$value = $qq_login_config['data']['value'];
				if (empty($value['app_key']) || empty($value['app_secret'])) {
					$this->error('请配置您申请的APP_KEY和APP_SECRET');
				} else {
					$redirect_url = addon_url('OAuthLoginQQ://common/login/callback', [ 'site_id' => $param['site_id'] ]);
					$qq_login_api = new QQLogin($value['app_key'], $value['app_secret'], $redirect_url);
					$request_url = $qq_login_api->getRequestCodeURL();
					$this->redirect($request_url);
				}
			}
		}
	}
	
}