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

namespace addon\system\OAuthLogin\common\controller;

use app\common\controller\BaseSite;
use app\common\model\Member;
use app\common\model\Login as LoginModel;

/**
 * 第三方登录控制器
 */
class Login extends BaseSite
{
	
	/**
	 * 第三方登录开始页
	 */
	public function login()
	{
		$addon_name = input('addon_name', '');
		$site_id = request()->siteid();
		$result = hook('oAuthLogin', [ 'name' => $addon_name, 'site_id' => $site_id ], null, true);
	}
	
	
	/**
	 * 第三方登录回调
	 */
	public function callback()
	{
		$params = input('data', '');
		$tag = input('tag', '');
		$data = !empty($params) ? json_decode(urldecode($params), true) : [];
		$site_id = request()->siteid();
		
		$login_redirect_url = session("login_redirect_url");
		if (empty($login_redirect_url)) {
			$login_redirect_url = addon_url('');
		}
		
		if (!empty($data)) {
			$member_model = new Member();
			$login_model = new LoginModel();
			$info = $member_model->getMemberInfo([ $tag => $data['openid'], 'site_id' => $site_id ]);
			
			if (!empty($info['data'])) {
				// 非首次登录
				$result = $login_model->oauthLogin($site_id, $tag, $data['openid']);
				if ($result['code'] == 0) {
					cookie("access_token_$site_id", $result['data']['access_token']);
					cache("member_info_$site_id", null);
					$this->redirect($login_redirect_url);
				}
			} else {
				// 首次登录
				$register_config = api('System.Login.registerConfig', [ 'site_id' => $site_id ]);
				if ($register_config['data']['is_automatic']) {
					// 非自动注册
					$this->redirect(addon_url('wap/login/register', [ 'data' => urlencode($params), 'tag' => $tag ]));
				} else {
					// 自动注册
					$reg_result = $login_model->oauthRegister($site_id, $data['nick_name'], $tag, $data['openid'], $data['head_img']);
					if ($reg_result['code'] == 0) {
						$result = $login_model->oauthLogin($site_id, $tag, $data['openid']);
						if ($result['code'] == 0) {
							cookie("access_token_$site_id", $result['data']['access_token']);
							cache("member_info_$site_id", null);
							$this->redirect($login_redirect_url);
						}
					}
				}
			}
		}
	}
}