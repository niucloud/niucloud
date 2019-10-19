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

namespace addon\system\Sms\sitehome\controller;

use app\common\controller\BaseSiteHome;


class Template extends BaseSiteHome
{
	protected $replace = [];
	
	public function __construct()
	{
		parent::__construct();
		$this->replace = [
			'ADDON_NS_SMS_IMG' => __ROOT__ .'/addon/system/Sms/sitehome/view/public/image',
		];
	}
	
	public function edit()
	{
		$keyword = input("keyword", "");
		$addon_name = input("addon_name", "");
		//查询当前默认短信服务器商
		$res = hook("doEditMessage", [ 'keyword' => $keyword, 'site_id' => SITE_ID, "name" => $addon_name ]);
		if (empty($res)) {
			$this->error("您选择的短信发送方式暂未开启!");
		}
	}
	
}