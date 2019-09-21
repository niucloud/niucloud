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

namespace addon\system\OAuthLoginQQ\common\controller;

use app\common\controller\BaseSite;
use addon\system\OAuthLoginQQ\common\model\QQLoginConfig;
use addon\system\OAuthLoginQQ\common\sdk\QQLogin;

/**
 * QQ登录控制器
 */
class Login extends BaseSite
{
	/**
	 * 第三方登录回调
	 */
	public function callBack()
	{
		$code = input('code', '');
		$error_msg = input("error", '');
		$site_id = request()->siteid();
		
		if (empty($code)) {
			die(json_encode(error()));
		} else {
			$qqlogin_config_model = new QQLoginConfig();
			$qq_login_config = $qqlogin_config_model->getQQLoginConfig($site_id);
			$value = $qq_login_config['data']['value'];
			
			$qq_login_api = new QQLogin($value['app_key'], $value['app_secret'], addon_url('OAuthLoginQQ://common/login/callBack'));
			$token = $qq_login_api->getAccessToken($code);
			
			if (isset($token['openid'])) {
				$qq_info = $qq_login_api->call('user/get_user_info');
				$data = [
					'nick_name' => $qq_info['nickname'],
					'head_img' => !empty($qq_info['figureurl_qq_2']) ? $qq_info['figureurl_qq_2'] : $qq_info['figureurl_qq_1'],
					'openid' => $token['openid'],
					'token' => $token,
					'info' => $qq_info
				];
				
				$data = urlencode(json_encode($data));
				
				$this->redirect(append_url_params(addon_url('OAuthLogin://common/login/callBack'), [ 'data' => $data, 'tag' => 'qq_openid' ]));
			} else {
				die(json_encode(error('', json_encode($token))));
			}
		}
	}
	
}