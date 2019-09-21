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
			'name' => 'NC_WECHAT_APPLET_SETTING',
			'title' => '功能设置',
			'url' => 'weapp://sitehome/config/setting',
			'is_menu' => 1,
			'icon' => 'application/sitehome/view/public/img/wx_feature_set.png',
			'icon_selected' => '',
			'parent' => 'WEAPP_ROOT',
			'sort' => 0,
			'child_list' => [
				[
					'name' => 'NC_WECHAT_APPLET_CONFIG',
					'title' => '小程序管理',
					'url' => 'weapp://sitehome/config/config',
					'is_menu' => 0
				],
				[
					'name' => 'NC_WECHAT_APPLET_PACK_DOWNLOAD',
					'title' => '打包下载',
					'url' => 'weapp://sitehome/config/packDownload',
					'is_menu' => 0
				],
			]
		],
		[
			'name' => 'NC_WECHAT_APPLET_ACCESS_STATISTICS',
			'title' => '访问统计',
			'url' => 'weapp://sitehome/config/accessStatistics',
			'is_menu' => 1,
			'icon' => 'application/sitehome/view/public/img/statistical.png',
			'icon_selected' => '',
			'parent' => 'WEAPP_ROOT',
			'sort' => 1,
		],
	
	],
	'root_menu' => ''
];