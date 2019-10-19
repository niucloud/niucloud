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
namespace addon\system\Message\sitehome\controller;

use addon\system\Message\sitehome\model\MsgTpl;

class Sendsms extends Base
{
	public function send($param = [])
	{
		if (IS_AJAX) {
			$field = json_decode(input('field'), true);
			$sms_code = $field['sms_code'];
			$sms_content = $field['sms_content'];
			$sms_sign = $field['sms_signature'];
			$temp_id = $field['temp_id'];
			$sms_code = preg_replace("/(\n)|(\s)|(\t)|(\')|(')|(，)|(\.)/", ',', $sms_code);
			$param = array(
				'account' => $sms_code,  //接收的账号
				'template_code' => $temp_id,  //模板
				'signature' => $sms_sign, //签名
				'sms_param' => ''
			);
			$res = hook('sms', $param);
			return $res;
		}
		return $this->fetch('sendsms/send', [], $this->replace);
	}
	
	public function getSmsMsgList()
	{
		if (IS_AJAX) {
			$limit = input('limit', PAGE_LIST_ROWS);
			$page = input('page', 1);
			$msgtpl_model = new MsgTpl();
			$list = $msgtpl_model->getSiteMsgTplPageList([ 'site_id' => $this->siteId ], '*', '', $page, $limit, '', '');
			return $list;
		}
	}
	
	public function getSmsMsgInfo()
	{
		if (IS_AJAX) {
			$id = input('id', PAGE_LIST_ROWS);
			$msgtpl_model = new MsgTpl();
			$list = $msgtpl_model->getSiteMsgTplInfo([ 'id' => $id ]);
			return $list;
		}
	}
	
	public function renewSend()
	{
		
		if (IS_AJAX) {
			$id = input('id', 10);
			$msgtpl_model = new MsgTpl();
			$info = $msgtpl_model->getSiteSmsInfo([ 'id' => $id ]);
			$param = array(
				'account' => $info['data']['send_account'],  //接收的账号
				'template_code' => $info['data']['template_code'],  //模板
				'signature' => $info['data']['signature'], //签名
				'sms_param' => $info['data']['notice_context']
			);
			
			$res = hook('sms', $param);
			return $res;
		}
	}
}