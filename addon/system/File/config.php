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
			'name' => 'NC_FILE_INDEX',
			'title' => '文件管理',
			'url' => 'file://sitehome/file/image',
			'parent' => 'CONFIG_ROOT',
			'is_menu' => 1,
			'icon' => 'addon/system/File/sitehome/view/public/img/menu_icon/file_management.png',
			'child_list' => [
				[
					'name' => 'NC_FILE_IMAGE_INDEX',
					'title' => '文件管理',
					'url' => 'file://sitehome/file/image',
					'is_menu' => 1,
					'sort' => 100,
					'child_list' => [
						[
							'name' => 'NC_FILE_IMAGE',
							'title' => '图片',
							'url' => 'file://sitehome/file/image',
							'is_menu' => 1,
							'sort' => 1,
						],
						[
							'name' => 'NC_FILE_AUDIO',
							'title' => '音频',
							'url' => 'file://sitehome/file/audio',
							'is_menu' => 1,
							'sort' => 2,
						],
						[
							'name' => 'NC_FILE_VIDEO',
							'title' => '视频',
							'url' => 'file://sitehome/file/video',
							'is_menu' => 1,
							'sort' => 3,
						]
					]
				],
				[
					'name' => 'NC_FILE_CONFIG',
					'title' => '上传设置',
					'sort' => 200,
					'url' => 'file://sitehome/config/config',
					'is_menu' => 1,
				]
			]
		],
	],
	'admin_menu' => [
		[
			'name' => 'ADMIN_FILE_OSS',
			'title' => '远程附件',
			'url' => 'file://admin/Oss/index',
			'parent' => 'ADMIN_CONFIG',
			'is_menu' => 1
		],
	],
];