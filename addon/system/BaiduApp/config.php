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
			'name' => 'NC_BAIDU_APPLET_SETTING',
			'title' => '功能设置',
			'url' => 'baiduapp://sitehome/config/setting',
			'is_menu' => 1,
			'icon' => 'application/sitehome/view/public/img/wx_feature_set.png',
			'icon_selected' => '',
			'parent' => 'NC_BAIDU_APPLET',
			'sort' => 0,
			'child_list' => [
				[
					'name' => 'NC_BAIDU_APPLET_CONFIG',
					'title' => '小程序管理',
					'url' => 'baiduapp://sitehome/config/config',
					'is_menu' => 0
				],
				[
					'name' => 'NC_BAIDU_APPLET_VERSION',
					'title' => '版本管理',
					'url' => 'baiduapp://sitehome/config/version',
					'is_menu' => 0
				],
			]
		],
		[
			'name' => 'NC_BAIDU_APPLET_ACCESS_STATISTICS',
			'title' => '访问统计',
			'url' => 'baiduapp://sitehome/config/accessstatistics',
			'is_menu' => 1,
			'parent' => 'NC_BAIDU_APPLET',
			'icon' => 'application/sitehome/view/public/img/statistical.png',
			'icon_selected' => '',
			'sort' => 1,
		],
	]
];