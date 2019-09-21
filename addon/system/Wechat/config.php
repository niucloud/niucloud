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
            'name' => 'ADMIN_CONFIG_WECHAT',
            'title' => '微信开放平台',
            'url' => 'wechat://admin/config/wechat',
            'parent' => 'ADMIN_CONFIG',
            'is_menu' => 1,
        ]
    ],
	'menu' => [
		[
			'name' => 'NC_WECHAT_SETTING',
			'title' => '功能设置',
			'url' => 'wechat://sitehome/config/setting',
			'parent' => 'WECHAT_ROOT',
			'is_menu' => 1,
			'icon' => 'application/sitehome/view/public/img/wx_feature_set.png',
			'icon_selected' => '',
			'sort' => 0,
			'child_list' => [
				[
					'name' => 'NC_WECHAT_CONFIG',
					'title' => '公众号管理',
					'url' => 'wechat://sitehome/config/index',
					'is_menu' => 0,
					'child_list' => []
				],
				[
					
					'name' => 'WCHAT_CONFIG',
					'title' => '公众平台配置',
					'url' => 'wechat://sitehome/config/config',
					'is_menu' => 0
				],
				[
					
					'name' => 'NC_WECHAT_AUTH',
					'title' => '公众平台授权',
					'url' => 'wechat://sitehome/config/author',
					'is_menu' => 0
				],
				
				[
					'name' => 'NC_WECHAT_MATERIAL',
					'title' => '消息素材',
					'url' => 'wechat://sitehome/material/index',
					'is_menu' => 0,
					'child_list' => [
						[
							'name' => 'NC_WECHAT_ADD_GRAPHIC_MEESSAGE',
							'title' => '添加图文消息',
							'url' => 'wechat://sitehome/material/addgraphicmessage',
							'is_menu' => 0
						],
						[
							'name' => 'NC_WECHAT_EDIT_GRAPHIC_MEESSAGE',
							'title' => '修改图文消息',
							'url' => 'wechat://sitehome/material/editgraphicmessage',
							'is_menu' => 0
						],
					]
				],
				
				[
					'name' => 'NC_WECHAT_MENU',
					'title' => '菜单管理',
					'url' => 'wechat://sitehome/menu/index',
					'is_menu' => 0
				],
				[
					'name' => 'NC_WECHAT_QRCODE',
					'title' => '推广二维码管理',
					'url' => 'wechat://sitehome/qrcode/index',
					'is_menu' => 0
				],
				[
					'name' => 'NC_WECHAT_SHARE',
					'title' => '分享内容设置',
					'url' => 'wechat://sitehome/share/index',
					'is_menu' => 0
				],
				
				[
					'name' => 'NC_WECHAT_REPLAY_INDEX',
					'title' => '回复设置',
					'url' => 'wechat://sitehome/replay/index',
					'is_menu' => 0,
					'child_list' => []
				],
				[
					'name' => 'NC_WECHAT_REPLAY_KEYS',
					'title' => '关键词自动回复',
					'url' => 'wechat://sitehome/replay/index',
					'is_menu' => 0
				],
				[
					'name' => 'NC_WECHAT_REPLAY_FOLLOW',
					'title' => '关注后自动回复',
					'url' => 'wechat://sitehome/replay/follow',
					'is_menu' => 0
				],
				
				[
					'name' => 'NC_WECHAT_MASS_INDEX',
					'title' => '群发设置',
					'url' => 'wechat://sitehome/mass/index',
					'is_menu' => 0
				],
				[
					'name' => 'NC_WECHAT_FANS',
					'title' => '粉丝管理',
					'url' => 'wechat://sitehome/fans/index',
					'icon' => 'addon/system/Wechat/sitehome/view/public/img/wx_feature_set.png',
					'icon_selected' => '',
					'is_menu' => 0
				],
				[
					'name' => 'NC_WECHAT_FANS_TAG',
					'title' => '粉丝标签',
					'url' => 'Wechat://sitehome/fans/fanstag',
					'is_menu' => 0
				],
				[
					'name' => 'NC_WECHAT_LEAVE_MSG',
					'title' => '留言管理',
					'url' => 'wechat://sitehome/leavemsg/index',
					'is_menu' => 0
				],
				[
					'name' => 'NC_WECHAT_MESSAGE_CONFIG',
					'title' => '微信消息设置',
					'url' => 'wechat://sitehome/message/config',
					'is_menu' => 0
				],
				[
					'name' => 'NC_WECHAT_MESSAGE_EDIT',
					'title' => '编辑微信消息模板',
					'url' => 'wechat://sitehome/message/edit',
					'is_menu' => 0
				]
			]
		],
		[
			'name' => 'NC_WECHAT_ACCESS_STATISTICS',
			'title' => '访问统计',
			'url' => 'wechat://sitehome/config/accessStatistics',
			'parent' => 'WECHAT_ROOT',
			'icon_selected' => '',
			'icon' => 'application/sitehome/view/public/img/statistical.png',
			'is_menu' => 1,
			'sort' => 1,
		],
	],
	'root_menu' => ''
];