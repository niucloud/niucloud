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
			'name' => 'WECHAT_ROOT',
			'title' => '微信公众号',
			'url' => 'wechat://sitehome/config/setting',
			'is_menu' => 1,
			'icon' => 'addon/system/DiyView/sitehome/view/public/img/menu_icon/renovation_system.png',
			'icon_selected' => '',
			'sort' => 1,
			'parent' => '',
			'child_list' => [
			],
		],
		[
			'name' => 'WEAPP_ROOT',
			'title' => '微信小程序',
			'url' => 'weapp://sitehome/config/setting',
			'is_menu' => 1,
			'icon' => 'addon/system/DiyView/sitehome/view/public/img/menu_icon/renovation_system.png',
			'icon_selected' => '',
			'sort' => 2,
			'parent' => '',
			'child_list' => [],
		],
		[
			'name' => 'NC_ALI_APPLET',
			'title' => '支付宝小程序',
			'url' => 'aliApp://sitehome/config/setting',
			'is_menu' => 1,
			'parent' => '',
			'icon' => 'addon/system/AliApp/sitehome/view/public/img/menu_icon/menu_wechat.png',
			'icon_selected' => 'addon/system/AliApp/sitehome/view/public/img/menu_icon/menu_wechat_selected.png',
			'sort' => 3,
			'child_list' => [],
		],
		[
			'name' => 'NC_BAIDU_APPLET',
			'title' => '百度小程序',
			'url' => 'baiduapp://sitehome/config/setting',
			'is_menu' => 1,
			'parent' => '',
			'icon' => 'addon/system/BaiduApp/sitehome/view/public/img/menu_icon/menu_wechat.png',
			'icon_selected' => 'addon/system/BaiduApp/sitehome/view/public/img/menu_icon/menu_wechat_selected.png',
			'sort' => 4,
			'child_list' => [],
		],
		[
			'name' => 'MEMBER_ROOT',
			'title' => '会员',
			'url' => 'member://sitehome/member/memberlist',
			'parent' => '',
			'is_menu' => 1,
			'icon' => 'addon/system/Member/sitehome/view/public/img/menu_icon/menu_member.png',
			'icon_selected' => 'addon/system/Member/sitehome/view/public/img/menu_icon/menu_member_selected.png',
			'sort' => 2,
			'child_list' => [
				[
					'name' => 'MEMBER_WECHAT_FANS',
					'title' => '粉丝管理',
					'url' => 'wechat://sitehome/fans/index',
					'icon' => 'addon/system/Wechat/sitehome/view/public/img/wx_feature_set.png',
					'icon_selected' => '',
					'is_menu' => 1
				],
			],
		],
		[
			'name' => 'DIYVIEW_MEMBER',
			'title' => '会员中心',
			'url' => 'sitehome/diy/memberindex',
			'parent' => 'NC_MEMBER_INDEX',
			'is_menu' => 1,
			'icon' => '',
			'icon_selected' => '',
			'sort' => 5,
			'child_list' => [],
		],
//		[
//			'name' => 'DIYVIEW_ROOT',
//			'title' => '微网站',
//			'url' => 'sitehome/diy/index',
//			'is_menu' => 1,
//			'icon' => 'addon/system/DiyView/sitehome/view/public/img/menu_icon/renovation_system.png',
//			'icon_selected' => '',
//			'sort' => 3,
//			'parent' => '',
//			'child_list' => [
//				[
//					'name' => 'DIYVIEW_SETTING',
//					'title' => '装修',
//					'url' => 'sitehome/diy/index',
//					'is_menu' => 1,
//					'sort' => 2,
//					'icon' => 'addon/system/DiyView/sitehome/view/public/img/menu_icon/decorate_set.png',
//					'icon_selected' => 'addon/system/DiyView/sitehome/view/public/img/renovation_system.png',
//					'child_list' => [
//						[
//							'name' => 'DIYVIEW_SITE',
//							'title' => '网站主页',
//							'url' => 'sitehome/diy/index',
//							'is_menu' => 1,
//							'sort' => 0,
//							'icon' => '',
//							'icon_selected' => '',
//							'child_list' => []
//						],
//						[
//							'name' => 'DIYVIEW_MODULE_ROOT',
//							'title' => '模块装修',
//							'url' => '',
//							'is_menu' => 0,
//							'sort' => 4,
//							'icon' => 'addon/system/DiyView/sitehome/view/public/img/menu_icon/renovation_system.png',
//							'child_list' => []
//						]
//					]
//				],
//			],
//		],
		[
			'name' => 'CONFIG_ROOT',
			'title' => '设置',
			'url' => 'sitehome/manager/sitesetting',
			'parent' => '',
			'is_menu' => 1,
			'icon' => 'application/sitehome/view/public/img/menu_icon/menu_overview.png',
			'icon_selected' => 'application/sitehome/view/public/img/menu_icon/menu_overview_selected.png',
			'sort' => 4,
			'child_list' => [
				[
					'name' => 'SITEHOME_MANAGER',
					'title' => '管理员',
					'url' => 'sitehome/manager/index',
					'parent' => '',
					'is_menu' => 1,
					'icon' => 'application/sitehome/view/public/img/menu_icon/site_administrators.png',
					'icon_selected' => '',
					'sort' => 1,
					'child_list' => [
						[
							'name' => 'SITEHOME_MANAGER_INDEX',
							'title' => '用户列表',
							'url' => 'sitehome/manager/index',
							'is_menu' => 1,
							'icon' => '',
							'icon_selected' => '',
							'sort' => 1,
							'child_list' => [
								[
									'name' => 'SITEHOME_MANAGER_ADD',
									'title' => '添加',
									'url' => 'sitehome/manager/adduser',
									'is_menu' => 0,
									'icon' => '',
									'icon_selected' => '',
									'sort' => 1,
									'child_list' => [],
								],
								[
									'name' => 'SITEHOME_MANAGER_EDIT',
									'title' => '编辑',
									'url' => 'sitehome/manager/edituser',
									'is_menu' => 0,
									'icon' => '',
									'icon_selected' => '',
									'sort' => 1,
									'child_list' => [],
								],
								[
									'name' => 'SITEHOME_MANAGER_BIND_SITE_USER',
									'title' => '关联操作员',
									'url' => 'sitehome/manager/bindSiteUser',
									'is_menu' => 0,
									'icon' => '',
									'icon_selected' => '',
									'sort' => 1,
									'child_list' => [],
								],
							],
						],
						[
							'name' => 'SITEHOME_MANAGER_GROUP',
							'title' => '用户组',
							'url' => 'sitehome/manager/group',
							'is_menu' => 1,
							'icon' => '',
							'icon_selected' => '',
							'sort' => 2,
							'child_list' => [
								[
									'name' => 'SITEHOME_MANAGER_GROUP_ADD',
									'title' => '添加',
									'url' => 'sitehome/manager/addgroup',
									'is_menu' => 0,
									'icon' => '',
									'icon_selected' => '',
									'sort' => 0,
									'child_list' => [],
								],
								[
									'name' => 'SITEHOME_MANAGER_GROUP_EDIT',
									'title' => '编辑',
									'url' => 'sitehome/manager/editgroup',
									'is_menu' => 0,
									'icon' => '',
									'icon_selected' => '',
									'sort' => 1,
									'child_list' => [],
								],
							],
						],
						[
							'name' => 'SITEHOME_AUTH_OPERATION',
							'title' => '操作日志',
							'url' => 'sitehome/auth/operation',
							'is_menu' => 1,
							'icon' => '',
							'icon_selected' => '',
							'sort' => 3,
							'child_list' => [],
						],
					],
				],
				[
					'name' => 'SITE_CONFIG_ROOT',
					'title' => '站点信息',
					'url' => 'sitehome/manager/sitesetting',
					'parent' => '',
					'is_menu' => 1,
					'icon' => 'application/sitehome/view/public/img/menu_icon/site_info.png',
					'icon_selected' => '',
					'sort' => 2,
					'child_list' => [
						[
							'name' => 'SITEHOME_SITE_SETTING',
							'title' => '站点信息',
							'url' => 'sitehome/manager/sitesetting',
							'is_menu' => 1,
							'icon' => '',
							'icon_selected' => '',
							'sort' => 1,
							'child_list' => [],
						],
						[
							'name' => 'SITEHOME_SITE_CONTACT',
							'title' => '联系我们',
							'url' => 'sitehome/manager/contactsetting',
							'is_menu' => 1,
							'icon' => '',
							'icon_selected' => '',
							'sort' => 2,
							'child_list' => [],
						],
						[
							'name' => 'SITEHOME_SITE_DOMAIN',
							'title' => '站点域名',
							'url' => 'sitehome/manager/sitesetdomain',
							'is_menu' => 1,
							'icon' => '',
							'icon_selected' => '',
							'sort' => 3,
							'child_list' => [],
						],
						[
							'name' => 'SITEHOME_SITE_SECRET_KEY',
							'title' => '站点秘钥',
							'url' => 'sitehome/manager/secretkeymanage',
							'is_menu' => 1,
							'icon' => '',
							'icon_selected' => '',
							'sort' => 4,
							'child_list' => [],
						],
						[
							'name' => 'SITEHOME_NOTICE',
							'title' => '公告管理',
							'url' => 'sitehome/notice/index',
							'is_menu' => 1,
							'sort' => 5,
							'child_list' => [
								[
									'name' => 'SITEHOME_NOTICE_ADD',
									'title' => '添加公告',
									'url' => 'sitehome/notice/addnotice',
									'is_menu' => 0,
								],
								[
									'name' => 'SITEHOME_NOTICE_EDIT',
									'title' => '编辑公告',
									'url' => 'sitehome/notice/editnotice',
									'is_menu' => 0,
								],
							]
						],
						[
							'name' => 'SITEHOME_HELP',
							'title' => '帮助中心',
							'url' => 'sitehome/help/index',
							'is_menu' => 1,
							'sort' => 6,
							'child_list' => [
								[
									'name' => 'SITEHOME_HELP_ADD',
									'title' => '添加',
									'url' => 'sitehome/help/addhelparticle',
									'is_menu' => 0,
								],
								[
									'name' => 'SITEHOME_HELP_EDIT',
									'title' => '修改',
									'url' => 'sitehome/help/edithelparticle',
									'is_menu' => 0,
								],
								[
									'name' => 'SITEHOME_HELP_DEL',
									'title' => '删除',
									'url' => 'sitehome/help/deletehelparticle',
									'is_menu' => 0,
								],
								[
									'name' => 'SITEHOME_HELP_CATEGROY',
									'title' => '帮助类型',
									'url' => 'sitehome/help/helpcategory',
									'is_menu' => 0,
								],
							],
						],
					],
				],
				[
					'name' => 'APPLET_CONFIG',
					'title' => '功能设置',
					'url' => '',
					'parent' => '',
					'sort' => 104,
					'is_menu' => 0,
					'icon' => 'application/sitehome/view/public/img/feature_set.png',
					'icon_selected' => '',
					'child_list' => []
				],
			
			],
		],
		[
			'name' => 'ADDON_ROOT',
			'title' => '应用',
			'url' => '',
			'parent' => '',
			'is_menu' => 1,
			'icon' => '',
			'icon_selected' => 'application/sitehome/view/public/img/menu_icon/menu_website_selected_01.png',
			'sort' => 9,
			'child_list' => [],
		],
	
	],
	'diy' => [
		'view' => [
//			[
//				'name' => 'DIYVIEW_SITE',
//				'title' => '网站主页',
//				'value' => '{"global":{"title":"网站主页","openBottomNav":true,"bgColor":"#ffffff","bgUrl":""},"value":[{"title":"演示案例（小程序版）","subTitle":"","textAlign":"center","backgroundColor":"#ffffff","link":{"label":"链接地址"},"fontSize":16,"addon_name":"DiyView","type":"TEXT","name":"文本","controller":"Text"}]}',
//				'type' => 'H5',
//				'icon' => 'addon/app/NcApplet/sitehome/view/public/img/diy_icon/index.png'
//			],
			[
				'name' => 'DIYVIEW_MEMBER',
				'title' => '会员中心',
				'value' => '{"global":{"title":"会员中心","openBottomNav":true,"bgColor":"#ffffff","bgUrl":""},"value":[{"addon_name":"Member","controller":"Member","background_image":"","member_info_location":"left","textColor":"#333333","type":"NC_MEMBER_CENTER","name":"会员中心"},{"addon_name":"Member","controller":"MemberAccount","type":"NC_MEMBER_ACCOUNT","name":"会员账户"}]}',
				'type' => 'H5',
				'icon' => 'addon/app/NcApplet/sitehome/view/public/img/diy_icon/member_index.png'
			],
		],
		'link' => [
//			[
//				'name' => 'NC_APPLET_INDEX',
//				'title' => '主页',
//				'design_url' => '',
//				'h5_url' => 'wap/index/index',
//				'weapp_url' => '/pages/index/index',
//				'web_url' => ''
//			],
			[
				'name' => 'NC_APPLET_MEMBER',
				'title' => '会员中心',
				'design_url' => '',
				'h5_url' => 'wap/member/index',
				'weapp_url' => '/pages/member/index/index',
				'web_url' => ''
			],
		],
	
	],
	'message_template' => [
	
	],
	
	// 默认模块名
	'default_module' => 'wap',
	
	// 默认控制器名
	'default_controller' => 'index',
	
	// 默认操作名
	'default_action' => 'index',
	
	// 默认操作名
	'default_weapp' => 'pages/index/index'
];