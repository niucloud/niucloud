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

namespace addon\system\Email\admin\controller;

use app\common\controller\BaseAdmin;
use addon\system\Email\common\model\EmailConfig;

class Config extends BaseAdmin
{
    protected $replace = [];

    public function __construct()
    {
        parent::__construct();
        $this->siteId = 0;
        $this->replace = [
            'ADDON_EMAIL_IMG' => __ROOT__ . '/addon/system/Email/admin/view/public/img',
            'ADDON_EMAIL_JS' => __ROOT__ . '/addon/system/Email/admin/view/public/js',
        ];
    }
	
	/**
	 * 设置
	 */
	public function config()
	{
		$site_config_model = new EmailConfig();
		$site_id = 0;
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
			return $this->fetch('config/config',[],$this->replace);
		}
	}
	
}