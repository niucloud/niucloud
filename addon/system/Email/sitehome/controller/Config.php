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

namespace addon\system\Email\sitehome\controller;

use addon\system\Email\common\model\EmailConfig;
use addon\system\Email\common\model\Message as EmailMessage;
use app\common\controller\BaseSiteHome;
use addon\system\Message\common\model\SiteMessage;

class Config extends BaseSiteHome
{
	/**
	 * 设置
	 */
	public function index()
	{
		$site_config_model = new EmailConfig();
		$site_id = $this->siteId;
		if (IS_AJAX) {
			$server = input('server', '');
            $port = input('port', '');
            $email = input('email', '');
            $username = input('username', '');
            $password = input('password', '');
            $status = input('status', 0);
            $value_array = array(
                "server" => $server,
                "port" => $port,
                "email" => $email,
                "username" => $username,
                "password" => $password
            );
            $value_json = json_encode($value_array);
            $data = array(
                "site_id" => $site_id,
                "value" => $value_json,
                "status" => $status
            );
			$res = $site_config_model->setEmailConfig($data);
			return $res;
		} else {
			$list = $site_config_model->getEmailConfig($site_id);
			$this->assign('list', $list['data']['value']);
            $this->assign('status', $list['data']['status']);
			return $this->fetch('config/index');
		}
	}
	
	public function edit()
	{
		$email_messae_model = new EmailMessage();
		$message_model = new SiteMessage();
		$site_id = $this->siteId;
		if (IS_AJAX) {
			$keyword = input("keyword", "");
            $status = input("status", "");
			$content = input("content", "");
            $title = input("title", "");
			$data = array(
				'site_id' => $site_id,
				'title' => $title,
				'content' => $content,
				'var_parse' => '',
				'keyword' => $keyword
			);
            $res = $email_messae_model->editEmailMessage($data, [ 'site_id' => $site_id, "keyword" => $keyword ]);

            //开启或关闭本消息类型邮箱的启用状态
            $type_data = array(
                "email_is_open" => $status
            );
            $message_model->editSiteMessageType($type_data, ["site_keyword" => $keyword, "site_id" => $site_id]);
			return $res;
		} else {
			$keyword = input("keyword", "");
			if (empty($keyword)) {
				$this->redirect(addon_url('Message://sitehome/Index/index'));
			}
			$this->assign("keyword", $keyword);
			$message_type_info = $message_model->getSiteMessageTypeViewInfo([ 'site_id' => $site_id, 'keyword' => $keyword ]);
			$this->assign("message_type_info", $message_type_info['data']);
			$email_message_info = $email_messae_model->getEmailMessageInfo([ 'site_id' => $site_id, 'keyword' => $keyword ]);
			$this->assign("email_message_info", $email_message_info['data']);
			return $this->fetch('config/edit');
		}
	}
	
}