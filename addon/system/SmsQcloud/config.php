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

return [
	'admin_menu' => [
		[
			'name' => 'ADMIN_SMS_QCLOUD',
			'title' => '腾讯云短信配置',
			'url' => 'smsqcloud://admin/index/config',
			'parent' => 'ADMIN_SMS_INDEX',
			'is_menu' => 0,
		]
	]
];