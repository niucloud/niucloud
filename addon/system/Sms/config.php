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
			'name' => 'ADMIN_SMS',
			'title' => '短信管理',
			'url' => 'sms://admin/config/index',
			'parent' => 'ADMIN_CONFIG',
			'is_menu' => 1,
			'child_list' => [
				[
					'name' => 'ADMIN_SMS_INDEX',
					'title' => '短信配置',
					'url' => 'sms://admin/config/index',
					'is_menu' => 1,
				],
				[
					'name' => 'ADMIN_SMS_RECORD',
					'title' => '短信记录',
					'url' => 'sms://admin/config/smslist',
					'is_menu' => 1,
				],
				[
					'name' => 'ADMIN_SMS_TEMPLATE',
					'title' => '短信模板',
					'url' => 'sms://admin/config/template',
					'is_menu' => 1,
				],
			]
		],
	],
	'menu' => [
		[
			'name' => 'NC_SMS_CONFIG',
			'title' => '短信管理',
			'url' => 'sms://sitehome/config/smslist',
			'parent' => 'ADDON_ROOT',
			'is_menu' => 1
		],
	],
	'autoload' => 1,
];