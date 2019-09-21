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
			'name' => 'NC_DIYVIEW_SYSTEM_PAGE',
			'title' => '基础页面',
			'url' => 'diyview://sitehome/design/diyview',
			'is_menu' => 0,
			'sort' => 0,
			'parent' => 'DIYVIEW_ROOT',
			'icon' => 'addon/system/DiyView/sitehome/view/public/img/decorate_page.png',
			'icon_selected' => '',
			'child_list' => []
		],
		[
			'name' => 'NC_DIYVIEW_H5_TEMPLATE',
			'title' => '模板选择',
			'url' => 'diyview://sitehome/design/templateSetting',
			'is_menu' => 1,
			'sort' => 2,
			'parent' => 'DIYVIEW_ROOT',
			'icon' => '',
			'icon_selected' => '',
			'child_list' => []
		],
		[
			'name' => 'NC_DIYVIEW_H5_FEATURE',
			'title' => '微页面',
			'url' => 'diyview://sitehome/design/feature',
			'is_menu' => 1,
			'sort' => 3,
			'parent' => 'DIYVIEW_ROOT',
			'icon' => '',
			'icon_selected' => '',
			'child_list' => [
				[
					'name' => 'NC_DIYVIEW_H5_DIYVIEW_EDIT',
					'title' => '装修编辑',
					'url' => 'diyview://sitehome/design/edit',
					'is_menu' => 0
				],
			]
		],
		[
			'name' => 'NC_DIYVIEW_COMPMENT',
			'title' => '自定义模块',
			'url' => 'diyview://sitehome/design/compment',
			'is_menu' => 0,
			'sort' => 4,
			'parent' => 'DIYVIEW_ROOT',
			'icon' => '',
			'icon_selected' => '',
			'child_list' => [
				[
					'name' => 'NC_DIYVIEW_COMPMENT_ADD',
					'title' => '添加模块',
					'url' => 'diyview://sitehome/design/compmentadd',
					'is_menu' => 0
				],
				[
					'name' => 'NC_DIYVIEW_COMPMENT_EDIT',
					'title' => '修改模块',
					'url' => 'diyview://sitehome/advertisement/compmentedit',
					'is_menu' => 0
				],
				[
					'name' => 'NC_DIYVIEW_COMPMENT_DEL',
					'title' => '删除模块',
					'url' => 'diyview://sitehome/advertisement/deletecompment',
					'is_menu' => 0
				],
			
			]
		],
		[
			'name' => 'NC_DIYVIEW_AD',
			'title' => '公共广告',
			'url' => 'diyview://sitehome/advertisement/index',
			'is_menu' => 0,
			'sort' => 5,
			'parent' => 'DIYVIEW_ROOT',
			'icon' => '',
			'icon_selected' => '',
			'child_list' => [
				[
					'name' => 'NC_DIYVIEW_AD_EDIT',
					'title' => '编辑广告',
					'url' => 'diyview://sitehome/advertisement/edit',
					'is_menu' => 0
				],
			
			]
		],
		[
			'name' => 'NC_BOTTOM_NAV_DESIGN',
			'title' => '底部导航',
			'url' => 'diyview://sitehome/design/bottomnavdesign',
			'is_menu' => 1,
			'sort' => 6,
			'parent' => 'DIYVIEW_ROOT',
			'icon' => '',
			'icon_selected' => '',
			'child_list' => []
		],
	],
	
	'diy' => [
        'link' => [
            [
                'name' => 'NC_LOGIN',
                'title' => '登录',
                'design_url' => '',
                'h5_url' => 'wap/login/login',
                'weapp_url' => '/pages/login/login/login',
                'web_url' => ''
            ],
            [
                'name' => 'NC_REGISTER',
                'title' => '注册',
                'design_url' => '',
                'h5_url' => 'wap/login/register',
                'weapp_url' => '/pages/login/register/register',
                'web_url' => ''
            ],
        ],
		'util' => [
			[
				'name' => 'TEXT',
				'title' => '文本',
				'type' => 'SYSTEM',
				'controller' => 'Text',
				'value' => '{ title : "『文本』", subTitle : "", textAlign : "left", backgroundColor : "#ffffff", "link" : {},"fontSize" : 16 }',
				'sort' => '10000',
				'support_diy_view' => '',
				'max_count' => 0
			],
			[
				'name' => 'TEXT_NAV',
				'title' => '文本导航',
				'type' => 'SYSTEM',
				'controller' => 'TextNav',
				'value' => '{ fontSize : 14, textColor : "#333333", textAlign : "left", backgroundColor : "#ffffff", arrangement : "vertical", list : [{ text : "『文本导航』","link" : {}}] }',
				'sort' => '10001',
				'support_diy_view' => '',
				'max_count' => 0
			],
			[
				'name' => 'NOTICE',
				'title' => '公告',
				'type' => 'SYSTEM',
				'controller' => 'Notice',
				'value' => '{ "backgroundColor": "#ffffff","textColor": "#333333","fontSize": 14,"image_url": "","list": [{"title": "公告","link": {}},{"title": "公告","link": {}}]}',
				'sort' => '10002',
				'support_diy_view' => '',
				'max_count' => 0
			],
			[
				'name' => 'GRAPHIC_NAV',
				'title' => '图文导航',
				'type' => 'SYSTEM',
				'controller' => 'GraphicNav',
				'value' => '{ "textColor": "#666666","backgroundColor": "#ffffff","selectedTemplate": "imageNavigation","scrollSetting": "fixed","imageScale": 100,padding : 0,"list": [{"imageUrl": "","title": "","link": {}},{"imageUrl": "","title": "","link": {}},{"imageUrl": "","title": "","link": {}},{"imageUrl": "","title": "","link": {}}]}',
				'sort' => '10003',
				'support_diy_view' => '',
				'max_count' => 0
			],
			[
				'name' => 'IMAGE_ADS',
				'title' => '图片广告',
				'type' => 'SYSTEM',
				'controller' => 'ImageAds',
				'value' => '{ selectedTemplate : "carousel-posters", imageClearance : 0, list : [ { imageUrl : "", title : "", "link" : {}} ] }',
				'sort' => '10004',
				'support_diy_view' => '',
				'max_count' => 0
			],
			[
				'name' => 'SEARCH',
				'title' => '顶部搜索',
				'type' => 'SYSTEM',
				'controller' => 'Search',
				'value' => '{ "left_img_url": "","left_link" : {},"right_img_url": "","right_link" : {},"background_color" : "#e43130"}',
				'sort' => '10005',
				'support_diy_view' => '',
				'max_count' => 1
			],
			[
				'name' => 'TITLE',
				'title' => '顶部标题',
				'type' => 'SYSTEM',
				'controller' => 'Title',
				'value' => '{ "title": "『顶部标题』","backgroundColor": "#ffffff","textColor": "#000000","isOpenOperation" : false,"leftLink" : {},"rightLink" : {},"operation_name" : "操作","fontSize" : 16}',
				'sort' => '10006',
				'support_diy_view' => '',
				'max_count' => 1
			],
			[
				'name' => 'RICH_TEXT',
				'title' => '富文本',
				'type' => 'SYSTEM',
				'controller' => 'RichText',
				'value' => '{ "html" : "" }',
				'sort' => '10007',
				'support_diy_view' => '',
				'max_count' => 0
			],
			[
				'name' => 'RUBIK_CUBE',
				'title' => '魔方',
				'type' => 'SYSTEM',
				'controller' => 'RubikCube',
				'value' => '{ "selectedTemplate": "row1-of2","list": [{ imageUrl : "", link : {} },{ imageUrl : "", link : {} }], "selectedRubikCubeArray" : [] ,"diyHtml": "","customRubikCube": 4,"heightArray": ["74.25px","59px","48.83px","41.56px"],"imageGap": 0}',
				'sort' => '10008',
				'support_diy_view' => '',
				'max_count' => 0
			],
//			[
//				'name' => 'CUSTOM_MODULE',
//				'title' => '自定义模块',
//				'type' => 'SYSTEM',
//				'controller' => '',
//				'value' => '',
//				'sort' => '10009',
//				'support_diy_view' => '',
//				'max_count' => 0
//			],
			[
				'name' => 'POP_WINDOW',
				'title' => '弹窗广告',
				'type' => 'SYSTEM',
				'controller' => 'PopWindow',
				'value' => '{ "image_url":"","link":{}}',
				'sort' => '10011',
				'support_diy_view' => '',
				'max_count' => 1
			],
			[
				'name' => 'HORZ_LINE',
				'title' => '辅助线',
				'type' => 'SYSTEM',
				'controller' => 'HorzLine',
				'value' => '{ color : "#e5e5e5", padding : "no-padding", borderStyle : "solid" }',
				'sort' => '10012',
				'support_diy_view' => '',
				'max_count' => 0
			],
			[
				'name' => 'HORZ_BLANK',
				'title' => '辅助空白',
				'type' => 'SYSTEM',
				'controller' => 'HorzBlank',
				'value' => '{ height : 10, backgroundColor : "#ffffff" }',
				'sort' => '10013',
				'support_diy_view' => '',
				'max_count' => 0
			],
			[
				'name' => 'VIDEO',
				'title' => '视频',
				'type' => 'SYSTEM',
				'controller' => '',
				'value' => '',
				'sort' => '10014',
				'support_diy_view' => '',
				'max_count' => 0
			],
			[
				'name' => 'VOICE',
				'title' => '语音',
				'type' => 'SYSTEM',
				'controller' => '',
				'value' => '',
				'sort' => '10015',
				'support_diy_view' => '',
				'max_count' => 0
			],
		]
	],
];