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

namespace app\api\controller;

use app\common\model\Login as LoginModel;
use app\common\model\Member as MemberModel;
use app\common\controller\BaseApi;
use app\common\model\Site;
use think\Cache;

/**
 * 控制器
 */
class Login extends BaseApi
{
	
	/**
	 * 注册配置
	 * @param unknown $params
	 */
	public function registerConfig($params)
	{
		$site_model = new Site();
		$info = $site_model->getSiteConfigInfo([ 'site_id' => $params['site_id'], 'name' => 'SITE_REGISTER_CONFIG' ]);
		
		$config_info = [
			'is_allow_register' => '1',
			'register_type_common' => '1',
			'is_automatic' => 1
		];
		if (!empty($info['data']) && !empty($info['data']['value'])) {
			$config_info = json_decode($info['data']['value'], true);
		}
		return success($config_info);
	}
	
	/**
	 * 注册
	 * @param $params
	 * @return array
	 */
	public function register($params)
	{
		if (empty($params['username']) && empty($params['mobile']) && empty($params['email'])) {
			return error('', 'request parameter field!');
		}
		$login_model = new LoginModel();

		$data = [
			'password' => $params['password']
		];
		if (!empty($params['username'])) {
			$data['username'] = $params['username'];
		}
		if (!empty($params['mobile'])) {
			$data['mobile'] = $params['mobile'];
		}
		if (!empty($params['email'])) {
			$data['email'] = $params['email'];
		}
		
		$verify = $this->verifyRegisterData($params);
		
		if ($verify['code'] == 0) {
			$res = $login_model->register($params['site_id'], $data);
			return $res;
		} else {
			return $verify;
		}
	}
	
	/**
	 * 验证注册数据
	 */
	private function verifyRegisterData($params)
	{
//	    $login_model = new LoginModel();
//        $resister_config_result = $login_model->getRegisterConfig($params["site_id"]);
//        $resister_config = $resister_config_result["data"];
//        if($resister_config["value"]['register_type_common'])
		if (!empty($params['email']) && !empty($params['email_code'])) {
			$key = md5("email_code_" . $params['site_id'] . "_" . $params['email']);
			$code = Cache::get($key);
			if (empty($code)) {
				return error("", "邮箱动态码已失效");
			}
			if ($params['email_code'] != $code) {
				return error("", "邮箱动态码错误");
			}
		}
		
		if (!empty($params['mobile']) && !empty($params['sms_code'])) {
			$key = md5("mobile_code_" . $params['site_id'] . "_" . $params['mobile']);
			$code = Cache::get($key);
			if (empty($code)) {
				return error("", "短信动态码已失效");
			}
			if ($params['sms_code'] != $code) {
				return error("", "短信动态码错误");
			}
		}
		
		if (!empty($params['captcha']) && !captcha_check($params['captcha'])) {
			return error("", "验证码错误");
		}
		return success();
	}
	
	/**
	 * 用户名密码登录
	 */
	public function login($params)
	{
		$username = isset($params['username']) ? $params['username'] : '';
		$password = isset($params['password']) ? $params['password'] : '';
		if (empty($username)) {
			return error('', 'request parameter username!');
		}
		$member_model = new LoginModel();
		$login_info = $member_model->login($params['site_id'], $username, $password);
		return $login_info;
	}

    /**
     * 发送注册手机验证码
     * @param $params
     * @return mixed
     */
	public function sendCode($params)
	{
		//格式：md5(email/mobile_code_site_id_value)，例如：email_code_1031_1518079521@qq.com
		$code = rand(100000, 999999);
		$data = [ "keyword" => "REGISTER", "site_id" => $params['site_id'], 'code' => $code ];
        $member_model = new MemberModel();
		if (!empty($params['email'])) {
            $condition = array(
                "email" => $params['email'],
                "site_id" => $params["site_id"]
            );
            $member_info_result = $member_model->getMemberInfo($condition);
            if(!empty($member_info_result["data"]))
                return error("", "当前邮箱号已存在账号");

			$key = md5("email_code_" . $params['site_id'] . "_" . $params['email']);
			$data['email'] = $params['email'];
            $data['support_type'] = "email";
		}
		if (!empty($params['mobile'])) {
            $condition = array(
                "mobile" => $params['mobile'],
                "site_id" => $params["site_id"]
            );
            $member_info_result = $member_model->getMemberInfo($condition);
            if(!empty($member_info_result["data"]))
                return error("", "当前手机号已存在账号");

			$key = md5("mobile_code_" . $params['site_id'] . "_" . $params['mobile']);
			$data['mobile'] = $params['mobile'];
            $data['support_type'] = "Sms";
		}
		$res = hook("sendMessage", $data);
		Cache::set($key, $code, 180);
		return $res[0];
	}

    /**
     * 发送忘记密码验证码
     * @param $param
     */
	public function sendFindCode($params){
		$code = rand(100000, 999999);
		$data = [ "keyword" => "REGISTER", "site_id" => $params['site_id'], 'code' => $code ];
		$member_model = new MemberModel();
		if (!empty($params['email'])) {
            $condition = array(
                "email" => $params['email'],
                "site_id" => $params["site_id"]
            );
            $member_info_result = $member_model->getMemberInfo($condition);
            if(empty($member_info_result["data"]))
                return error("", "当前邮箱号不存在账号");

            $key = md5("email_find_code_" . $params['site_id'] . "_" . $params['email']);
            $data['email'] = $params['email'];
            $data['support_type'] = "email";
        }
		if (!empty($params['mobile'])) {
            $condition = array(
                "mobile" => $params['mobile'],
                "site_id" => $params["site_id"]
            );
            $member_info_result = $member_model->getMemberInfo($condition);
            if(empty($member_info_result["data"]))
                return error("", "当前手机号不存在账号");


            $key = md5("mobile_find_code_" . $params['site_id'] . "_" . $params['mobile']);
            $data['mobile'] = $params['mobile'];
            $data['support_type'] = "Sms";
        }
		$res = hook("sendMessage", $data);
		Cache::set($key, $code, 180);
		return $res[0];
    }
	
	/**
	 * 第三方登录
	 * @param unknown $params
	 */
	public function oauthLogin($params)
	{
		$openid_tag = isset($params['openid_tag']) ? $params['openid_tag'] : '';
		$openid = isset($params['openid']) ? $params['openid'] : '';
		if (empty($openid_tag) || empty($openid)) {
			return error('', 'request parameter openid_tag and openid!');
		}
		$member_model = new LoginModel();
		$login_info = $member_model->oauthLogin($params['site_id'], $openid_tag, $openid);
		return $login_info;
	}
	
	/**
	 * 第三方注册
	 * @param array $params
	 */
	public function oauthRegister($params)
	{
		$openid_tag = isset($params['openid_tag']) ? $params['openid_tag'] : '';
		$openid = isset($params['openid']) ? $params['openid'] : '';
		if (empty($openid_tag) || empty($openid)) {
			return error('', 'request parameter openid_tag and openid!');
		}
		$nickname = isset($params['nickname']) ? $params['nickname'] : '';
		$headimg = isset($params['headimg']) ? $params['headimg'] : '';
		$member_model = new LoginModel();
		$reg_info = $member_model->oauthRegister($params['site_id'], $nickname, $openid_tag, $openid, $headimg);
		return $reg_info;
	}
	
	/**
	 * 获取第三方登录配置
	 * 创建时间：2018年9月18日20:04:29
	 */
	public function getOAuthLoginType($params)
	{
		$oauth_login_config = hook('getOAuthLoginType', [ 'site_id' => $params['site_id'] ]);
		return success($oauth_login_config);
	}
	
	/**
	 * 第三方注册完善信息
	 * @param array $params
	 */
	public function perfectInfo($params)
	{
		$verify_result = $this->registerVerify($params, '', false);
		if ($verify_result['code'] != 0) return $verify_result;
		
		$member_model = new MemberModel();
		$res = $member_model->perfectInfo($params['username'], $params['password'], $params['nick_name'], $params['head_img'], $params['tag'], $params['openid'], $params['site_id']);
		return $res;
	}
	
	
	/**
	 * 第三方注册绑定账号
	 * @param array $params
	 */
	public function bindAccount($params)
	{
		$tag = isset($params['tag']) ? $params['tag'] : '';
		$openid = isset($params['openid']) ? $params['openid'] : '';
		if (empty($tag) || empty($openid)) {
			return error('', 'request parameter openid_tag and openid!');
		}
		$nick_name = isset($params['nick_name']) ? $params['nick_name'] : '';
		$head_img = isset($params['head_img']) ? $params['head_img'] : '';
		$member_model = new MemberModel();
		
		$res = $member_model->bindAccount($params['username'], $params['password'], $nick_name, $head_img, $tag, $openid, $params['site_id']);
		return $res;
	}
	
	/**
	 * 注册验证
	 */
	private function registerVerify($params = [], $type = '', $is_need_open_register = true)
	{
		$site_model = new Site();
		$info = $site_model->getSiteConfigInfo([ 'site_id' => $params['site_id'], 'name' => 'SITE_REGISTER_CONFIG' ]);
		
		$config_info = [
			'is_allow_register' => '1',
			'register_type_common' => '1',
			'is_automatic' => 1
		];
		if (!empty($info['data'])) {
			$config_info = json_decode($info['data']['value'], true);
		}
		
		if ($is_need_open_register) {
			if (!isset($config_info['is_allow_register'])) return error('', '站点未启用注册功能');
			if (!isset($config_info['register_type_common']) && $type == 'account') return error('', '站点未启用账号注册功能');
			if (!isset($config_info['register_type_email']) && $type == 'email') return error('', '站点未启用邮箱注册功能');
			if (!isset($config_info['register_type_mobile']) && $type == 'mobile') return error('', '站点未启用手机注册功能');
		}
		
		if (!empty($params['username']) && !empty($config_info['name_keyword'])) {
			$name_keyword = explode(',', $config_info['name_keyword']);
			foreach ($name_keyword as $v) {
				if (strstr($params['username'], $v)) {
					return error('', '用户名不可使用"' . $config_info['name_keyword'] . '"这些关键字');
				}
			}
		}
		if (!empty($config_info['pwd_length']) && mb_strlen($params['password']) < $config_info['pwd_length']) return error('', '密码不能小于' . $config_info['pwd_length'] . '位');
		if (isset($config_info['pwd_complexity_number']) && !preg_match('/[0-9]/', $params['password'])) return error('', '密码必须包含数字');
		if (isset($config_info['pwd_complexity_lowercase']) && !preg_match('/[a-z]/', $params['password'])) return error('', '密码必须包含小写字母');
		if (isset($config_info['pwd_complexity_uppercase']) && !preg_match('/[A-Z]/', $params['password'])) return error('', '密码必须包含大写字母');
		if (isset($config_info['pwd_complexity_symbol']) && preg_match('/^[A-Za-z0-9]+$/', $params['password'])) return error('', '密码必须包含特殊字符');
		
		return success();
	}
	
	/**
	 * 检测账号是否存在
	 */
	public function checkAccountIsExist($params)
	{
		$type = '';
		switch ($params['type']) {
			case 'username':
				$type = $params['type'];
				break;
			case 'mobile':
				$type = $params['type'];
				break;
			case 'email':
				$type = $params['type'];
				break;
		}
		
		if (empty($type)) return;
		
		$member_model = new MemberModel();
		$res = $member_model->checkAccountIsExist($type, $params['account'], $params['site_id']);
		return $res;
	}


    /**
     * 忘记密码重置密码
     * @param $params
     */
    public function passwordReset($params){
        if (empty($params['account']) && empty($params['type']) && empty($params['code']) && empty($params['password'])) {
            return error('', 'request parameter field!');
        }
        $member_model = new MemberModel();
        if($params["type"] == "email"){
            if (!empty($params['account']) && !empty($params['code'])) {
                $condition = array(
                    "email" => $params['account'],
                    "site_id" => $params["site_id"]
                );
                $member_info_result = $member_model->getMemberInfo($condition);
                if(empty($member_info_result["data"]))
                    return error("", "当前邮箱号不存在账号");

                $key = md5("email_find_code_" . $params['site_id'] . "_" . $params['account']);
                $code = Cache::get($key);
                if (empty($code)) {
                    return error("", "邮箱动态码已失效");
                }
                if ($params['code'] != $code) {
                    return error("", "邮箱动态码错误");
                }
            }else{
                return error('', "邮箱号和验证码不能为空");
            }

        }else{
            if (!empty($params['account']) && !empty($params['code'])) {
                $condition = array(
                    "mobile" => $params['account'],
                    "site_id" => $params["site_id"]
                );
                $member_info_result = $member_model->getMemberInfo($condition);
                if(empty($member_info_result["data"]))
                    return error("", "当前手机号不存在账号");

                $key = md5("mobile_find_code_" . $params['site_id'] . "_" . $params['account']);
                $code = Cache::get($key);
                if (empty($code)) {
                    return error("", "短信动态码已失效");
                }
                if ($params['code'] != $code) {
                    return error("", "短信动态码错误");
                }
            }else{
                return error('', "手机号和验证码不能为空");
            }
        }
        $res = $member_model->modifyMemberPassword($params['password'], $condition);
        return $res;


    }

}