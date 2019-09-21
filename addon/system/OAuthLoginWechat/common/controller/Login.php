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

namespace addon\system\OAuthLoginWechat\common\controller;

use app\common\controller\BaseSite;
use addon\system\OAuthLoginWechat\common\model\WechatLoginConfig;
use addon\system\OAuthLoginWechat\common\sdk\WechatLogin;


/**
 * 微信登录回调
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
			$wechatlogin_config_model = new WechatLoginConfig();
			$wechat_login_config = $wechatlogin_config_model->getWechatLoginConfig(request()->siteid());
			$value = $wechat_login_config['data']['value'];
			
			$wechat_login_api = new WechatLogin($value['app_key'], $value['app_secret'], addon_url('OAuthLoginWechat://common/login/callBack'));
			$token = $wechat_login_api->getAccessToken($code);
			
			if (isset($token['unionid'])) {
				$wechat_info = $wechat_login_api->call('sns/userinfo');
				
				$data = [
					'nick_name' => $wechat_info['nickname'],
					'head_img' => $wechat_info['headimgurl'],
					'openid' => $wechat_info['unionid'],
					'token' => $token,
					'info' => $wechat_info
				];
				
				$data = urlencode(json_encode($data));
				
				$this->redirect(append_url_params(addon_url('OAuthLoginWechat://common/login/callBack'), [ 'data' => $data, 'tag' => 'wx_unionid' ]));
			} else {
				die(json_encode(error('', json_encode($token))));
			}
		}
	}
}