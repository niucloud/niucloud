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

namespace addon\system\SmsNiucloud\common\model;


use addon\system\Sms\common\model\SiteSmsList;

/**
 * 牛云短信
 * @author lzw
 */
class SmsNiuYun
{
	private $corpid; // 账号
	private $pwd; // 密码
	private $content; //发送内容
	private $cell = ''; // 扩展号(必须是数字或为空)
	private $sendTime = ''; //定时发送时间(可为空)
	private $site_id = 1;
	private $sys_version = 'NcWeb';
	private $signature;
	
	public function __construct()
	{
		switch (SYS_VERSION) {
			case 'NiuCloud':
				$this->site_id = 2;
				$this->sys_version = 'NiuCloud';
				break;
			case 'NcWeb':
				$this->site_id = 1;
				$this->sys_version = 'NcWeb';
				break;
		}
	}
	
	/**
	 * 获取牛云短信配置（只能由后台进行配置）
	 */
	private function setSmsConfig()
	{
		$config_model = new SmsNiucloudConfig();
		$config = $config_model->getNiuCloudSmsConfigInfo($this->site_id);
		if (!empty($config['data']['value'])) {
			if (!$config['data']['status']) {
				return error('', '系统未启用牛云短信');
			}
			$this->corpid = $config['data']['value']['corp_id'];
			$this->pwd = $config['data']['value']['pwd'];
			$this->signature = $config['data']['value']['signature'];
		} else {
			return error('', '系统未配置牛云短信');
		}
	}
	
	/**
	 * 牛云短信发送
	 * @param array $params
	 */
	public function niuSmsSend($params = [])
	{
		$res = $this->setSmsConfig();
		if (!empty($res) && $res['code'] != 0) return $res;
		
		// 非系统发送短信时验证用户可使用短信条数
		if ($params['site_id'] != $this->site_id) {
			if (empty($params['app_key'])) return error('', 'missing parameter app_key');
			
			$check_result = $this->checkUserSurplusSmsNum($params['app_key']);
			if ($check_result['code'] != 0) {
				return $check_result;
			}
		}
		
		// 模板信息
		$tpl_info = $this->getTplInfo($params['name'], $params['site_id']);
		if ($tpl_info['code'] != 0) return $tpl_info;
		
		// 发送内容
		$content_data = $this->getSmsContent($params['name'], $params['sms_param']);
		if ($content_data['code'] != 0) {
			return $content_data;
		}
		$content = $content_data['data'];
		
		$data = [
			'corpid' => $this->corpid,
			'pwd' => $this->pwd,
			'mobile' => $params['account'],
			'content' => $content,
			'cell' => '',
			'sendTime' => '',
		];
		// 发送短信
		$send_res = $this->send($data);
		
		if ($send_res > 0) {
			$status = 1;
			$send_res_arr = [
				'code' => $status,
				'msg' => '发送成功'
			];
			if ($params['site_id'] != $this->site_id) {
				$this->sendSuccessSetDecSmsNum($params['app_key']);
			}
		} else {
			$status = -1;
			$send_res_arr = [
				'code' => $send_res,
				'msg' => $this->getErrorMsg(abs($send_res))
			];
		}
		
		$site_sms_list = new SiteSmsList();
		$res = $site_sms_list->addSiteSmsList($params['site_id'], $params['name'], $params['account'], '', '', $params['sms_param'], $status, json_encode($send_res), 'niuyun', 0, $params['app_key']);
		
		if ($status == 1) {
			return $res;
		} else {
			return error('', $this->getErrorMsg(abs($send_res)));
		}
	}
	
	/**
	 * 获取模板信息
	 * @param unknown $name
	 * @param unknown $site_id
	 */
	private function getTplInfo($name, $site_id)
	{
		$msg_tpl_info = model('nc_site_msg_tpl')->getInfo([ 'name' => $name, 'site_id' => $site_id ], 'sms_is_enabled');
		if (empty($msg_tpl_info)) {
			return error('', '站点未配置该模板');
		}
		if (!$msg_tpl_info['sms_is_enabled']) {
			return error('', '站点未启用该模板');
		}
		return success($msg_tpl_info);
	}
	
	/**
	 * 检测用户当前剩余短信条数
	 * @param unknown $site_id
	 */
	private function checkUserSurplusSmsNum($app_key)
	{
		$user_info = model('nc_user')->getInfo([ 'app_key' => $app_key ], 'sms_num');
		if (!empty($user_info) && $user_info['sms_num'] > 0) {
			return success();
		} else {
			return error('', '您的短信已用完，请先进行购买');
		}
	}
	
	/**
	 * 发送成功短信条数自减
	 * @param unknown $site_id
	 */
	private function sendSuccessSetDecSmsNum($app_key)
	{
		model('nc_user')->setDec([ 'app_key' => $app_key ], 'sms_num', 1);
	}
	
	/**
	 * 获取发送内容
	 * @param unknown $site_id
	 * @param unknown $sms_param
	 */
	private function getSmsContent($name, $sms_param)
	{
		$sms_param = json_decode($sms_param, true);
		$tpl_content = model('nc_msg_tpl')->getInfo([ 'name' => $name ], 'sms_content');
		if (empty($tpl_content) || empty($tpl_content['sms_content'])) {
			return error('', '系统未配置该模板');
		}
		$content = strtr($tpl_content['sms_content'], $sms_param);
		$content .= '【' . $this->signature . '】';
		return success($content);
	}
	
	/**
	 * 获取错误描述
	 * @param unknown $code
	 * @return string
	 */
	private function getErrorMsg($code)
	{
		$error_msg_arr = [
			1 => '账号未注册',
			2 => '其他错误',
			3 => '帐号或密码错误',
			5 => '余额不足，请充值',
			6 => '定时发送时间不是有效的时间格式',
			7 => '提交信息末尾未签名，请添加中文的企业签名【 】',
			8 => '发送内容需在1到300字之间',
			9 => '发送号码为空',
			10 => '定时时间不能小于系统当前时间',
			100 => 'IP黑名单',
			102 => '账号黑名单',
			103 => 'IP未导白',
		];
		return $error_msg_arr[ $code ];
	}
	
	private function send($data = [])
	{
		$url = "http://sdk2.028lk.com:9880/utf8/BatchSend2.aspx";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_URL, $url);                                      // 需要获取的 URL 地址
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                           // TRUE 将curl_exec()获取的信息以字符串返回
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);          // 在HTTP请求中包含一个"User-Agent: "头的字符串。
		curl_setopt($ch, CURLOPT_POST, true);                                     // TRUE 时会发送 POST 请求
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);                              // 发送的文件
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);                                      // 允许 cURL 函数执行的最长秒数
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                          // false禁止对证书的验证
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);                          // false禁止对证书状态的验证
		$result = curl_exec($ch);
		return $result;
	}
}