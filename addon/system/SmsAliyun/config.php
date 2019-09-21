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
			'name' => 'ADMIN_SMS_ALIYUN',
			'title' => '阿里云短信配置',
			'url' => 'smsaliyun://admin/index/config',
			'parent' => 'ADMIN_SMS_INDEX',
			'is_menu' => 0,
		],
		[
			'name' => 'ADMIN_SMS_ALIYUN_EDIT',
			'title' => '编辑短信消息模板',
			'url' => 'smsaliyun://admin/index/template',
			'parent' => 'ADMIN_SMS_INDEX',
			'is_menu' => 0,
		]
	],
	'menu' => [
		[
			'name' => 'NC_SMS_ALIYUN_EDIT',
			'title' => '编辑短信消息模板',
			'url' => 'smsaliyun://sitehome/index/edit',
			'parent' => 'NC_MSG_TPL_INDEX',
			'is_menu' => 0,
		],
	],
];