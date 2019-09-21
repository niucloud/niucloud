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
			'name' => 'SNS_INFO',
			'title' => '信息管理',
			'url' => 'sns://sitehome/info/infolist',
			'parent' => '',
			'is_menu' => 1,
			'icon' => '',
			'icon_selected' => '',
			'sort' => 1,
			'child_list' => [
				[
					'name' => 'SNS_INFO_LIST',
					'title' => '信息列表',
					'url' => 'sns://sitehome/info/infolist',
					'is_menu' => 1,
					'icon' => 'addon/module/Sns/sitehome/view/public/img/info_list.png',
					'icon_selected' => '',
					'sort' => 0,
					'child_list' => [
						[
							'name' => 'SNS_INFO_ADD',
							'title' => '信息添加',
							'url' => 'sns://sitehome/info/addinfo',
							'is_menu' => 0,
						],
						[
							'name' => 'SNS_INFO_AUDIT',
							'title' => '审核信息',
							'url' => 'sns://sitehome/info/auditinfo',
							'is_menu' => 0,
						],
						[
							'name' => 'SNS_INFO_EDIT',
							'title' => '编辑信息',
							'url' => 'sns://sitehome/info/editinfo',
							'is_menu' => 0,
						],
						[
							'name' => 'SNS_INFO_DELETE',
							'title' => '删除信息',
							'url' => 'sns://sitehome/info/deleteinfo',
							'is_menu' => 0,
						],
					]
				],
				[
					'name' => 'SNS_INFO_CATEGORY',
					'title' => '栏目管理',
					'url' => 'sns://sitehome/info/category',
					'is_menu' => 1,
					'icon_selected' => '',
					'icon' => 'addon/module/Sns/sitehome/view/public/img/category_manage.png',
					'sort' => 1,
					'child_list' => [
						[
							'name' => 'SNS_INFO_CATEGORY_MANAGE',
							'title' => '管理',
							'url' => 'sns://sitehome/info/categorymanage',
							'is_menu' => 0,
						],
					],
				],
				[
					'name' => 'SNS_COMMENT',
					'title' => '评论列表',
					'url' => 'sns://sitehome/manage/commentlist',
					'is_menu' => 1,
					'icon_selected' => '',
					'icon' => 'addon/module/Sns/sitehome/view/public/img/comment.png',
					'sort' => 2,
					'child_list' => [
						[
							'name' => 'SNS_COMMENT_DELETE',
							'title' => '评论删除',
							'url' => 'sns://sitehome/manage/commentdelete',
							'is_menu' => 0,
							'sort' => 0
						],
						[
							'name' => 'SNS_COMMENT_SHOW',
							'title' => '评论隐藏',
							'url' => 'sns://sitehome/manage/commentshow',
							'is_menu' => 0,
							'sort' => 1
						],
						[
							'name' => 'SNS_COMMENT_REPLY',
							'title' => '评论回复',
							'url' => 'sns://sitehome/manage/commentreply',
							'is_menu' => 0,
							'sort' => 2
						],
					],
				],
				[
					'name' => 'SNS_REPORT',
					'title' => '举报列表',
					'url' => 'sns://sitehome/manage/reportlist',
					'is_menu' => 1,
					'icon' => 'addon/module/Sns/sitehome/view/public/img/report.png',
					'icon_selected' => '',
					'sort' => 3,
					'child_list' => [
						[
							'name' => 'SNS_REPORT_DELETE',
							'title' => '举报删除',
							'url' => 'sns://sitehome/manage/reportdelete',
							'is_menu' => 0,
							'sort' => 0
						],
						[
							'name' => 'SNS_REPORT_EXAMINE',
							'title' => '举报审核',
							'url' => 'sns://sitehome/manage/reportexamine',
							'is_menu' => 0,
							'sort' => 1
						],
					],
				],
				[
					'name' => 'SNS_CONFIG',
					'title' => '信息设置',
					'url' => 'sns://sitehome/info/config',
					'is_menu' => 1,
					'icon' => 'addon/module/Sns/sitehome/view/public/img/info_config.png',
					'icon_selected' => '',
					'sort' => 4,
					'child_list' => [],
				],
			],
		],
		[
			'name' => 'ADDON_APP_BOTTOM_NAV_DESIGN',
			'title' => '底部导航',
			'url' => 'sns://sitehome/info/bottomnavdesign',
			'is_menu' => 1,
			'icon' => '',
			'icon_selected' => '',
			'sort' => 2,
			'parent' => 'DIYVIEW_SETTING',
			'child_list' => [],
		],
	],
	
	'diy' => [
		
		'view' => [
			[
				'name' => 'DIYVIEW_SITE',
				'title' => '网站主页',
				'value' => '{"global":{"title":"网站主页","openBottomNav":true,"bgColor":"#ffffff","bgUrl":""},"value":[{"title":"分类信息","subTitle":"","textAlign":"center","backgroundColor":"#ffffff","link":{"label":"链接地址"},"fontSize":16,"addon_name":"DiyView","type":"TEXT","name":"文本","controller":"Text"}]}',
				'type' => 'H5',
				'icon' => 'addon/module/Sns/sitehome/view/public/img/diy_icon/index.png'
			],
			[
				'name' => 'DIYVIEW_MEMBER',
				'title' => '会员中心',
				'value' => '{"global":{"title":"会员中心","openBottomNav":true,"bgColor":"#ffffff","bgUrl":""},"value":[{"addon_name":"Member","controller":"Member","background_image":"","member_info_location":"left","textColor":"#333333","type":"NC_MEMBER_CENTER","name":"会员中心"},{"addon_name":"Member","controller":"MemberAccount","type":"NC_MEMBER_ACCOUNT","name":"会员账户"}]}',
				'type' => 'H5',
				'icon' => 'addon/module/Sns/sitehome/view/public/img/diy_icon/member_index.png'
			],
		],
		'link' => [
			[
				'name' => 'SNS_INDEX',
				'title' => '首页',
				'design_url' => '',
				'h5_url' => 'Sns://wap/index/index',
				'weapp_url' => '',
				'web_url' => '',
				'icon' => 'addon/module/Sns/sitehome/view/public/img/menu_icon/sns_index.png'
			],
			[
				'name' => 'SNS_INFO_ADD',
				'title' => '发布信息',
				'design_url' => '',
				'h5_url' => 'Sns://wap/member/publish',
				'weapp_url' => '',
				'web_url' => '',
				'icon' => 'addon/module/Sns/sitehome/view/public/img/menu_icon/sns_index.png'
			],
			[
				'name' => 'SNS_HISTORY',
				'title' => '浏览历史',
				'design_url' => '',
				'h5_url' => 'Sns://wap/member/history',
				'weapp_url' => '',
				'web_url' => '',
				'icon' => 'addon/module/Sns/sitehome/view/public/img/menu_icon/sns_index.png'
			],
			[
				'name' => 'SNS_COLLECTION',
				'title' => '收藏记录',
				'design_url' => '',
				'h5_url' => 'Sns://wap/member/collection',
				'weapp_url' => '',
				'web_url' => '',
				'icon' => 'addon/module/Sns/sitehome/view/public/img/menu_icon/sns_index.png'
			],
			[
				'name' => 'SNS_COMMENT',
				'title' => '我的评论',
				'design_url' => '',
				'h5_url' => 'Sns://wap/member/comment',
				'weapp_url' => '',
				'web_url' => '',
				'icon' => 'addon/module/Sns/sitehome/view/public/img/menu_icon/sns_index.png'
			],
			[
				'name' => 'SNS_CATEGORY',
				'title' => '栏目',
				'design_url' => '',
				'h5_url' => 'Sns://wap/category/index',
				'weapp_url' => '',
				'web_url' => '',
				'icon' => 'addon/module/Sns/sitehome/view/public/img/menu_icon/sns_category.png'
			],
			[
				'name' => 'SNS_MEMBER',
				'title' => '我的',
				'design_url' => '',
				'h5_url' => 'Sns://wap/member/index',
				'weapp_url' => '',
				'web_url' => '',
				'icon' => 'addon/module/Sns/sitehome/view/public/img/menu_icon/sns_category.png'
			],
			[
				'name' => 'SNS_MEMBER_PUBLISH',
				'title' => '我的发布',
				'design_url' => '',
				'h5_url' => 'Sns://wap/member/mypublish',
				'weapp_url' => '',
				'web_url' => '',
				'icon' => 'addon/module/Sns/sitehome/view/public/img/menu_icon/sns_category.png'
			],
		],
		'util' => [
			[
				'name' => 'SNS_PLATE',
				'title' => 'sns板块',
				'type' => 'SYSTEM',
				'controller' => 'SnsPlate',
				'value' => '{ title : "『sns板块』", subTitle : "", textAlign : "left", backgroundColor : "#ffffff", "link" : {},"fontSize" : 16 }',
				'sort' => '10000',
				'support_diy_view' => '',
				'max_count' => 0
			],
		]
	
	],

];