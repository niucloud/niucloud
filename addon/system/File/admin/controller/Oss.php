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

namespace addon\system\File\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\model\Site;

class Oss extends BaseAdmin
{
	protected $replace = [];
	
	public function __construct()
	{
		parent::__construct();
		$this->siteId = 0;
		$this->replace = [
			'ADDON_FILE_IMG' => __ROOT__ . '/addon/system/File/admin/view/public/img',
			'ADDON_FILE_JS' => __ROOT__ . '/addon/system/File/admin/view/public/js',
		];
	}
	
	/**
	 * 远程附件
	 */
	public function index()
	{
		if (IS_AJAX) {
			$config_list = hook('getFileConfig', [ "site_id" => 0 ]);//默认查询site_id为0的配置来作为平台配置
			return success(["list" => $config_list, 'count' => count($config_list)]);
		} else {
			return $this->fetch('oss/index', [], $this->replace);
		}
	}
	
	/**
	 * 开启或关闭云存储上传方式
	 */
	public function modifyFileTypeIsOpen()
	{
		$site_model = new Site();
		$status = input("status", 0);
		$name = input('name', '');
		if ($status == 1) {
			hook("closeFileType", [ "site_id" => 0 ]);
		}
		$result = $site_model->setSiteConfig([ "name" => $name, "site_id" => 0, "status" => $status ]);
		return $result;
	}
	
}