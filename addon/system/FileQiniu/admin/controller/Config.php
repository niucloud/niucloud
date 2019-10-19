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

namespace addon\system\FileQiniu\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\model\Site;

class Config extends BaseAdmin
{
	
	/**
	 * 附件上传配置
	 */
	public function config()
	{
		$site_model = new Site();
		$site_id = 0;
		if (IS_AJAX) {
			$bucket = input("bucket", "");
			$access_key = input("access_key", "");
			$secret_key = input("secret_key", "");
			$domain = input("domain", "");
			$status = input("status", 0);
			$json_data = array(
				"bucket" => $bucket,
				"access_key" => $access_key,
				"secret_key" => $secret_key,
				"domain" => $domain,
			);
			$value = json_encode($json_data);
			$data = array(
				"value" => $value,
				"site_id" => $site_id,
				"name" => "NC_FILE_UPLOAD_QINIU_CONFIG",
				"status" => $status
			);
			if ($status == 1) {
				hook("closeFileType", [ "site_id" => $this->siteId ]);
			}
			
			$res = $site_model->setSiteConfig($data);
			return $res;
		} else {
			$condition = array(
				"site_id" => $site_id,
				"name" => "NC_FILE_UPLOAD_QINIU_CONFIG"
			);
			$config_info = $site_model->getSiteConfigInfo($condition);
			$config_value = [];
			if (!empty($config_info["data"]["value"])) {
				$config_value = json_decode($config_info["data"]["value"], true);
			}
			$this->assign("config_info", $config_value);
			$this->assign("status", $config_info["data"]["status"]);
			return $this->fetch('config/config');
		}
	}
}