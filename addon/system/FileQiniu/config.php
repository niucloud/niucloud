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
			'name' => 'ADMIN_QINIU_CONFIG',
			'title' => '七牛云存储',
			'url' => 'fileqiniu://admin/config/config',
			'parent' => 'ADMIN_FILE_OSS',
			'is_menu' => 0,
		],
	],
];