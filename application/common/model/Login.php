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
namespace app\common\model;

/**
 * 注册与登录
 *
 * @author Administrator
 *
 */
class Login
{
    public $site_model;

    public function __construct()
    {
        $this->site_model = new Site();
    }
	/******************************************注册********************************************************************/
	/**
	 * 注册方法
	 * @param int $site_id 站点id
	 * @param array $data
	 */
	public function register($site_id, $data)
	{
		//判断用户名输入
		$username = isset($data['username']) ? $data['username'] : '';
		if (!empty($username)) {
			$member_info = model('nc_member')->getInfo([ 'username' => $username, 'site_id' => $site_id ], 'member_id');
			if (!empty($member_info)) {
				return error('', 'USERNAME_EXISTED');
			}
		}
		
		//判断手机输入
		$mobile = isset($data['mobile']) ? $data['mobile'] : '';
		if (!empty($mobile)) {
			$member_info = model('nc_member')->getInfo([ 'mobile' => $mobile, 'site_id' => $site_id ], 'member_id');
			if (!empty($member_info)) {
				return error('', 'MOBILE_EXISTED');
			}
		}
		
		//判断邮箱输入
		$email = isset($data['email']) ? $data['email'] : '';
		if (!empty($email)) {
			$member_info = model('nc_member')->getInfo([ 'email' => $email, 'site_id' => $site_id ], 'member_id');
			if (!empty($member_info)) {
				return error('', 'EMAIL_EXISTED');
			}
		}
		
		//整体判断
		if (empty($username) && empty($mobile) && empty($email)) {
			return error();
		}
		
		//插入数据
		$data['site_id'] = $site_id;
		$data['password'] = data_md5($data['password']);
		$data['register_time'] = time();
		$data['nick_name'] = $username;
		$res = model('nc_member')->add($data);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		
		//设置随机用户名
		if (empty($username)) {
			$site_model = new Site();
			$info = $site_model->getSiteConfigInfo([ 'site_id' => $site_id, 'name' => 'SITE_REGISTER_CONFIG' ]);
			
			$config_info = [
				'random_name_prefix' => ''
			];
			if (!empty($info['data']) && !empty($info['data']['value'])) {
				$config_info = json_decode($info['data']['value'], true);
			}
			
			if (!empty($config_info['random_name_prefix'])) {
				
				$username = $config_info['random_name_prefix'] . "_" . $res;
				model('nc_member')->update([ 'username' => $username, 'nick_name' => $username ], [ 'site_id' => $site_id, 'member_id' => $res ]);
				
			}
		}
		return success($res);
	}
	
	/**
	 * 第三方自动注册会员
	 * @param int $site_id
	 * @param string $nick_name
	 * @param string $openid_tag
	 * @param string $openid
	 * @param string $headimg
	 */
	public function oauthRegister($site_id, $nick_name, $openid_tag, $openid, $headimg = '')
	{
		//是否已被注册
		$condition = array( $openid_tag => $openid, 'site_id' => $site_id );
		$member_info = model('nc_member')->getInfo($condition);
		if (!empty($member_info)) {
			return error('', 'RESULT_ERROR');
		}
		
		$data = array(
			'site_id' => $site_id,
			'nick_name' => $nick_name,
			$openid_tag => $openid,
			'headimg' => $headimg,
			'register_time' => time()
		);
		$res = model('nc_member')->add($data);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 绑定第三方信息（注意微信特殊的unionid存在同时绑定）
	 * @param unknown $site_id
	 * @param unknown $member_id
	 * @param unknown $openid_tag1
	 * @param unknown $openid1
	 * @param unknown $openid_tag2
	 * @param unknown $openid2
	 * @return multitype:string mixed
	 */
	public function bindOauthInfo($site_id, $member_id, $openid_tag1, $openid1, $openid_tag2, $openid2)
	{
		$condition = array( $openid_tag1 => $openid1 );
		$member_info = model('nc_member')->getInfo($condition);
		if (!empty($member_info)) {
			return error('', 'RESULT_ERROR');
		}
		$data[ $openid_tag1 ] = $openid1;
		if (!empty($openid_tag2)) {
			$condition2 = array( $openid_tag2 => $openid2 );
			$member_info = model('nc_member')->getInfo($condition2);
			if (!empty($member_info)) {
				return error('', 'RESULT_ERROR');
			}
			$data[ $openid_tag2 ] = $openid2;
		}
		$res = model('nc_member')->update($data, [ 'site_id' => $site_id, 'member_id' => $member_id ]);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	/******************************************注册结束*****************************************************************/
	/**
	 * 用户密码登录
	 *
	 * @param int $site_id
	 * @param string $name
	 * @param string $password
	 * @return unknown
	 */
	public function login($site_id, $name, $password)
	{
		$where = array(
			'site_id' => $site_id,
			'username|email|mobile' => $name,
			'password' => data_md5($password)
		);
		$member_name = model('nc_member')->getInfo([
			'username|email|mobile' => $name,
			'site_id' => $site_id
		], 'member_id');
		if (empty($member_name)) {
			return error('', 'USER_NOT_EXIST');
		}
		$info = model('nc_member')->getInfo($where);
		if (!empty($info)) {
			$login_info = $this->initLogin($info, $site_id);
			return success($login_info);
		} else {
			return error('', 'PASSWORD_ERROR');
		}
	}
	
	/**
	 * 第三方登录
	 * @param int $site_id
	 * @param string $nick_name
	 * @param string $openid_tag
	 * @param string $openid
	 * @param string $headimg
	 */
	public function oauthLogin($site_id, $openid_tag, $openid)
	{
		$info = model('nc_member')->getInfo([ 'site_id' => $site_id, $openid_tag => $openid ]);
		if (!empty($info)) {
			$login_info = $this->initLogin($info, $site_id);
			return success($login_info);
		} else
			return error('', 'PASSWORD_ERROR');
	}
	
	/**
	 * 初始化数据
	 * @param unknown $member_info
	 * @param unknown $site_id
	 * @return multitype:number unknown string
	 */
	private function initLogin($member_info, $site_id)
	{
		$site_info = model('nc_site')->getInfo([ 'site_id' => $site_id ]);
		$access_token = encrypt($member_info['member_id'], $site_info['app_secret']);
		$data = [
			'member_info' => $member_info,
			'access_token' => $access_token,
			'expire' => 0
		];
		return $data;
	}






    /**
     * 注册协议
     * @param unknown $site_id
     * @param unknown $name
     * @param unknown $value
     */
    public function setRegisterAgreementConfig($data)
    {
        $data["name"] = 'SITE_REGISTER_AGREEMENT';
        $res = $this->site_model->setSiteConfig($data);
        return $res;
    }

    /**
     * 查询注册协议
     * @param unknown $where
     * @param unknown $field
     * @param unknown $value
     */
    public function getRegisterAgreementConfig($site_id)
    {
        $config = $this->site_model->getSiteConfigInfo([ 'name' => 'SITE_REGISTER_AGREEMENT', 'site_id' => $site_id ]);
        $value = [];
        if (!empty($config["data"]["value"])) {
            $value = json_decode($config["data"]["value"], true);
        }
        $config["data"]["value"] = $value;
        return $config;
    }

    /**
     * 注册规则
     * @param unknown $site_id
     * @param unknown $name
     * @param unknown $value
     */
    public function setRegisterConfig($data)
    {
        $data["name"] = 'SITE_REGISTER_CONFIG';
        $res = $this->site_model->setSiteConfig($data);
        return $res;
    }

    /**
     * 查询注册规则
     * @param unknown $where
     * @param unknown $field
     * @param unknown $value
     */
    public function getRegisterConfig($site_id)
    {
        $config = $this->site_model->getSiteConfigInfo([ 'name' => 'SITE_REGISTER_CONFIG', 'site_id' => $site_id ]);
        $value = [];
        if (!empty($config["data"]["value"])) {
            $value = json_decode($config["data"]["value"], true);
        }
        $config["data"]["value"] = $value;
        return $config;
    }
}