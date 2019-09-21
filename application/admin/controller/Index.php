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

namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\model\Addon as AddonModel;
use app\common\model\User as UserModel;
use app\common\model\Site;
use think\Cache;
use app\common\model\User;

/**
 * 首页 控制器
 */
class Index extends BaseAdmin
{
	
	/**
	 * 首页
	 */
	public function index()
	{
		$condition = [ 'nsu.uid' => UID ];
		
		$user_model = new UserModel();
		$addon_model = new AddonModel();
		
		$addon_name = input('addon_name', '');
		if ($addon_name) {
			$condition['ns.addon_app'] = $addon_name;
		}
		$this->assign('addon_name', $addon_name);
		
		$site_name = input('site_name', '');
		
		if ($site_name) {
			$condition['ns.site_name'] = [
				'like',
				'%' . $site_name . '%'
			];
		}
		
		$list = $user_model->getUserSiteList($condition);
		
		$list_new = $list;
		foreach ($list['data'] as $k => $v) {
			if ($v['type'] == 'ADDON_APP') {
				$app_class = get_addon_class($v['addon_app']);
				$app_class = new $app_class();
				$style = isset($app_class->css) ? $app_class->css : '';
				$v['style'] = $style;
			} else {
				$v['style'] = '';
			}
			$v["support_app_type"] = getSupportPort($v["support_app_type"]);
			
			$list_new['data'][ $k ] = $v;
			unset($v);
		}
		
		if (IS_AJAX) {
			return $list_new;
		} else {
			
			$res = $addon_model->getAddonList([ 'type' => 'ADDON_APP', 'visble' => 1 ], 'name, title');
			$apps = $res['data'];
			$this->assign('app_list', $apps);
			
			$this->assign('site_list', $list_new['data']);
			return $this->fetch('index/index');
		}
	}
	
	/**
	 * 用户修改密码
	 */
	public function modifyPassword()
	{
		if (IS_AJAX) {
			$uid = UID;
			$old_pass = input('old_pass', '');
			$new_pass = input('new_pass', '');
			$user_model = new UserModel();
			$res = $user_model->modifyUserPassword($uid, $old_pass, $new_pass);
			$user_model->refreshUserInfoSession($uid);
			return $res;
		}
	}
	
	/**
	 * 账号及安全
	 */
	public function security()
	{
		$user_model = new UserModel();
		$bind_mobile_info = $user_model->getUserBindMobileInfo(UID);
		$this->assign('bind_mobile_info', $bind_mobile_info);
		$this->assign('uid', UID);
		$user_info = $user_model->getUserInfo([ 'uid' => UID ]);
		$this->assign('user_info', $user_info['data']);
		return $this->fetch('index/security');
	}
	
	/**
	 * 校验当前域名
	 */
	public function checkSiteDomain()
	{
		$site_model = new Site();
		//查询所有站点绑定域名
		$domains = cache('domains');
		if (!$domains) {
			
			$domains = $site_model->getSiteDomains();
			cache('domains', $domains['data']);
		}
		//获取当前域名用于检测当前域名是否是站点绑定域名
		$domain = request()->domain();
		//检测是否存在绑定站点域名
		if (array_key_exists($domain, $domains)) {
			request()->siteid($domains[ $domain ]);
			$site_info = $site_model->getSiteInfo([ 'site_id' => $domains[ $domain ] ]);
			if (!empty($site_info)) {
				$this->error("当前站点域名已配置！", $domain . "/sitehome/index");
			}
		}
	}
	
	/**
	 * 用户绑定手机操作
	 * @return mixed
	 */
	public function bindMobile()
	{
		if (IS_AJAX) {
			$mobile = input('mobile', '');
            $sms_code = input('sms_code', '');
			if(empty($mobile)){
			    return error('', "手机号不可以为空");
            }
            $key = md5("bind_mobile_code_" . 0 . "_" . $mobile);
            $code = Cache::get($key);
            if (empty($code)) {
                return error("", "短信动态码已失效");
            }
            if ($sms_code != $code) {
                return error("", "短信动态码错误");
            }

			$user_model = new UserModel();
			$res = $user_model->bindMobile($mobile, UID);
			return $res;
		}
	}

    /**
     * 更改绑定手机号
     * @return \multitype
     */
	public function updateMobile(){
        if (IS_AJAX) {
            $mobile = input('mobile', '');
            $sms_code = input('sms_code', '');
            if(empty($mobile)){
                return error('', "手机号不可以为空");
            }
            //验证当前手机号
            $key = md5("bind_mobile_code_" . 0 . "_" . $mobile);
            $code = Cache::get($key);
            if (empty($code)) {
                return error("", "短信动态码已失效");
            }
            if ($sms_code != $code) {
                return error("", "短信动态码错误");
            }

            $user_model = new UserModel();
            $res = $user_model->bindMobile($mobile, UID);
	        $user_model->refreshUserInfoSession(UID);
            return $res;
        }
    }
	/**
	 * 检测手机号是否存在
	 */
	public function checkMobileIsExist()
	{
		if (IS_AJAX) {
			$mobile = input('mobile', '');
			$user_model = new UserModel();
			$res = $user_model->checkMobileIsExist($mobile, UID);
			return $res;
		}
	}

    /**
     * 绑死手机发送验证码
     */
	public function sendSmsCode(){
        $mobile = input("mobile", '');
        if (empty($mobile)) {
            return error([], "手机号不可以为空!");
        }

        $user_model = new User();
        $exist_result = $user_model->checkMobileIsExist($mobile);
        $exist_count = $exist_result["data"];
        if($exist_count > 0){
            return error([], "当前手机号已存在!");
        }

       $code = rand(100000, 999999);
       $data = [ "keyword" => "BIND_MOBILE", "site_id" => 0, 'code' => $code, 'support_type' => "Sms", 'mobile' => $mobile ];//仅支持短信发送
       $res = hook("sendMessage", $data);
       if($res[0]["code"] == 0){
           $key = md5("bind_mobile_code_" . 0 . "_" . $mobile);
           Cache::set($key, $code, 3600);
       }
       return $res[0];
    }
	
}