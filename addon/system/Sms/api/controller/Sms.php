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
namespace addon\system\Sms\api\controller;

use app\common\controller\BaseApi;

use addon\system\Sms\common\model\SiteSmsList;
use addon\system\Sms\common\model\SmsSend;

/**
 * 短信接口
 */
class Sms extends BaseApi
{
	/**
	 * 发送短信
	 * @param unknown $params
	 */
	public function send($params = [])
	{
		$smsSendModel = new SmsSend();
		$res = $smsSendModel->send($params);
		return $res;
	}
	
	/**
	 * 获取发送记录信息
	 * @param unknown $params
	 */
	public function getSendInfo($params = [])
	{
		$smsSendListModel = new SiteSmsList();
		$info = $smsSendListModel->getSendInfoById($params['id']);
		return $info;
	}
	
	/**
	 * 获取动态码验证结果
	 * @param array $params
	 */
	public function getVerifyCodeCheckResult($params = [])
	{
		$send_info = $this->getSendInfo($params);
		$send_info = $send_info['data'];
		
		if (empty($send_info) || $send_info['code'] < 0) return error('', '未获取到发送记录');
		$send_info['notice_context'] = json_decode($send_info['notice_context'], true);
		if ($send_info['send_account'] != $params['mobile']) return error('', '该手机号与验证时的手机号不一致');
		if ($send_info['notice_context']['code'] != $params['verify_code']) return error('', '动态码错误');
		if ($send_info['create_time'] < (time() - 6000)) return error('', '该动态码已过期');
		
		return success();
	}
	
}