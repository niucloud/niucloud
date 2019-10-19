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

namespace addon\system\Wechat\common\controller;

use addon\system\Wechat\common\model\Wechat;
use app\common\controller\BaseSite;
use util\weixin\Weixin;

/**
 * 微信公众号授权回调
 */
class Login extends BaseSite
{
	/**
	 * 微信公众号授权回调
	 */
	public function callBack()
	{
		$code = input('code', '');
		$site_id = request()->siteid();
		
		if (empty($code)) {
			$this->error('未获取到code');
		} else {
			$wechat_model = new Wechat();
			$wechat_config = $wechat_model->getWechatConfigInfo($site_id);
			$value = $wechat_config['data']['value'];
			
			$weixin_api = new Weixin('public');
			$weixin_api->initWechatPublicAccount($value['appid'], $value['appsecret']);
			$token = $weixin_api->getOAuthAccessTokenByCode($code);
			
			if (isset($token['openid'])) {
				$wechat_info = $weixin_api->getOAuthUserInfo($token);
				$wechat_info = json_decode($wechat_info, true);
				if (isset($wechat_info['errcode'])) {
					die(json_encode($wechat_info));
				} else {
					
					$data = [
						'nick_name' => $wechat_info['nickname'],
						'head_img' => $wechat_info['headimgurl'],
						'openid' => $wechat_info['openid'],
						'token' => $token,
						'info' => $wechat_info
					];
					$data = urlencode(json_encode($data));
					
					$this->redirect(append_url_params(addon_url('OAuthLogin://common/login/callBack'), [ 'data' => $data, 'tag' => 'wx_openid' ]));
				}
			} else {
				die(json_encode(error('', json_encode($token))));
			}
		}
	}
}