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
	'menu' => [
		[
			'name' => 'NC_EMAIL',
			'title' => '邮件',
			'url' => 'email://sitehome/config/index',
			'parent' => 'ADDON_ROOT',
			'is_menu' => 1,
			'child_list' => [
				[
					'name' => 'NC_EMAIL_CONFIG',
					'title' => '邮件设置',
					'url' => 'email://sitehome/config/index',
				],
				[
					'name' => 'NC_EMAIL_EDIT',
					'title' => '编辑邮件消息模板',
					'is_menu' => 0,
					'url' => 'email://sitehome/config/edit',
				],
			]
		],
	],
	'admin_menu' => [
		[
			'name' => 'ADMIN_EMAIL',
			'title' => '邮件管理',
			'url' => 'email://admin/config/config',
			'parent' => 'ADMIN_CONFIG',
			'is_menu' => 1
		],
	],
];