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

namespace addon\system\Member\sitehome\controller;

use app\common\controller\BaseSiteHome;
use app\common\model\Login as LoginModel;

/**
 * 会员登录
 * @author Administrator
 *
 */
class Login extends BaseSiteHome
{
	protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'
	
	public function __construct()
	{
		parent::__construct();
        $this->replace = [
            'ADDON_NS_MEMBER_CSS' => __ROOT__ . '/addon/system/Member/sitehome/view/public/css',
            'ADDON_NS_MEMBER_JS' => __ROOT__ . '/addon/system/Member/sitehome/view/public/js',
            'ADDON_NS_MEMBER_IMG' => __ROOT__ . '/addon/system/Member/sitehome/view/public/img',
        ];
	}
	
	/**
	 * 注册协议
	 */
	public function agreement()
	{
		$login_model = new LoginModel();
		if (IS_AJAX) {
			$value = input('data', '');//信息
			$data = [
				'site_id' => SITE_ID,
				'value' => $value,
				'title' => '注册协议',
				'remark' => '注册协议',
				'update_time' => time()
			];
			$res = $login_model->setRegisterAgreementConfig($data);
			return $res;
		} else {
			$config_result = $login_model->getRegisterAgreementConfig(SITE_ID);
			$this->assign('info', $config_result["data"]["value"]);
			return $this->fetch('login/agreement', [], $this->replace);
		}
	}
	
	/**
	 * 注册与访问
	 */
	public function registerAndVisit()
	{
		$login_model = new LoginModel();
		if (IS_AJAX) {
			$value = input('data', '');//备案信息
			$data = [
				'site_id' => SITE_ID,
				'value' => $value,
				'title' => '注册与访问设置',
				'remark' => '注册与访问设置',
			];
			$res = $login_model->setRegisterConfig($data);
			return $res;
		} else {
			$config_result = $login_model->getRegisterConfig(SITE_ID);
			$this->assign('info', $config_result["data"]["value"]);
			return $this->fetch('login/register_and_visit');
		}
	}
	
}