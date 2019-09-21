<?php

namespace addon\system\Email\common\model;

use addon\system\Email\common\model\Message as EmailMessage;
use addon\system\Email\common\sdk\Email as EmailServer;
use addon\system\Message\common\model\SiteMessage;

class Email
{
	
	
	/**
	 * 邮箱消息发送
	 * @param unknown $param
	 */
	public function send($param)
	{
		if (empty($param["account"])) return error('', "发送邮箱时接收人不可为空!");//判断接收人
		try{
    		$message_model = new SiteMessage();
    		$type_info = $message_model->getSiteMessageTypeViewInfo([ "keyword" => $param["keyword"], "site_id" => $param["site_id"] ]);
    		if (((!empty($param["support_type"]) && stripos($param["support_type"], "Email") !== false) || empty($param["support_type"])) && stripos($type_info["data"]["port"], "Email") !== false && $type_info["data"]["email_is_open"] == 1) {
    			$email_message_model = new EmailMessage();
    			$message_info = $email_message_model->getEmailMessageInfo([ "keyword" => $param["keyword"], "site_id" => $param["site_id"] ]);
    			
    			if (empty($message_info["data"]))
    				return error();
    			
    			if (empty($message_info["data"]["title"]))
    				return error();
    			
    			if (empty($message_info["data"]["content"]))
    				return error();
    			
    			if (empty($param["var_parse"]))
    				return error();
    			
    			$var_parse = $param["var_parse"];
    			$title = $message_info["data"]["title"];
    			$content = $message_info["data"]["content"];
    			foreach ($type_info["data"]["var_json"] as $k => $v) {
    				$content = str_replace("{" . $v . "}", $var_parse[ $k ], $content);
    				$title = str_replace("{" . $v . "}", $var_parse[ $k ], $title);
    			}
    			
    			$email_param = array(
    				"site_id" => $param["site_id"],
    				"title" => $title,
    				"content" => $content,
    				"account" => $param["account"]
    			);
    			
    			//添加发送记录(未即时发送的记录 发送状态为为发送)
    			$message_records_model = new  MessageRecords();
    			$data = array(
    				"site_id" => $param["site_id"],
    				"account" => $param["account"],
    				"title" => $title,
    				"content" => $content,
    				"keyword" => $param["keyword"],
    				"create_time" => time()
    			);
    			
    			//是否即时发送
    			if ($type_info["data"]["is_instant"] == 1) {
    				$result = $this->emailSend($email_param);
    				if ($result["code"] == 0) {
    					$status = 1;
    					$data["send_time"] = time();
    				} else {
    					$status = -1;
    				}
    				
    			} else {
    				$status = 0;
    			}
    			$data["status"] = $status;
    			$result = $message_records_model->addEmailMessageRecords($data);
    			return $result;
    		} else {
    			return error();
    		}
		} catch (\Exception $e) { 
		    return error($e->getMessage());
		}
	}
	
	
	/**
	 * 公共的邮箱发送
	 * @param unknown $param
	 * @return string[]|mixed[]
	 */
	public function emailSend($param)
	{
		$email_config_model = new EmailConfig();
		$email_config = $email_config_model->getEmailConfig($param["site_id"]);
		
		if (empty($email_config["data"])) return error('', '站点尚未配置邮箱');
		if (empty($email_config["data"]['status'])) return error('', '站点尚未启用邮箱');
		$config_info = $email_config["data"]['value'];//邮箱配置
		
		if (empty($config_info)) return error('', '站点尚未配置邮箱');
		$site_name = model('nc_site')->getInfo([ 'site_id' => $param['site_id'] ], 'site_name')['site_name'];
		$result = $this->email($config_info["server"], $config_info["username"], $config_info["password"], $config_info["port"], $param["account"], $site_name, $param["title"], $param["content"]);
		
		$status = $result ? 1 : -1;// $status 为1 发送成功  为2 发送失败
		
		if ($status > 0) {
			return success();
		} else {
			return error('', '邮件发送失败');
		}
	}
	
	
	/**
	 * 邮件发送
	 * @param  $server
	 * @param  $username
	 * @param  $password
	 * @param  $port
	 * @param  $account
	 * @param  $site_name
	 * @param  $title
	 * @param  $content
	 * @return string[]|mixed[]|unknown
	 */
	public function email($server, $username, $password, $port, $account, $site_name, $title, $content)
	{
		$mail = new EmailServer();
		$mail->_siteName = $site_name;
		$mail->setServer($server, $username, $password, $port, true);
		$mail->setCc($username);
		$mail->setFrom($username);
		$mail->setReceiver($account);
		$mail->setMail($title, $content);
		$result = $mail->sendMail();
		return $result;
		
	}
}