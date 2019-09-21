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
namespace addon\system\Email\api\controller;

use app\common\controller\BaseApi;

use addon\system\Email\common\model\Email as EmailModel;
use addon\system\Email\common\model\SiteEmailList;


/**
 * 邮箱接口
 */
class Email extends BaseApi
{
	/**
	 * 发送邮件
	 * @param unknown $params
	 */
	public function send($params = [])
	{
		$email_model = new EmailModel();
		$res = $email_model->send($params);
		return $res;
	}
	
	/**
	 * 获取发送记录信息
	 * @param unknown $params
	 */
	public function getSendInfo($params = [])
	{
		$smsSendListModel = new SiteEmailList();
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
		$send_info['send_param'] = json_decode($send_info['send_param'], true);
		if ($send_info['send_account'] != $params['account']) return error('', '该邮箱与验证时的邮箱不一致');
		if ($send_info['send_param']['code'] != $params['verify_code']) return error('', '动态码错误');
		if ($send_info['create_time'] < (time() - 6000)) return error('', '该动态码已过期');
		
		return success();
	}
	
}