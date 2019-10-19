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
			'name' => 'NC_CMS',
			'title' => '文章管理',
			'url' => 'article://sitehome/article/articlelist',
			'parent' => 'ADDON_ROOT',
			'is_menu' => 1,
			'icon' => 'addon/module/Article/sitehome/view/public/img/menu_icon/article_management.png',
			'child_list' => [
				[
					'name' => 'NC_CMS_ARTICLE',
					'title' => '文章管理',
					'url' => 'article://sitehome/article/articlelist',
					'icon' => 'addon/module/Article/sitehome/view/public/img/menu_icon/article_management.png',
					'icon_selected' => '',
					'is_menu' => 1,
					'sort' => 0,
					'child_list' => [
						[
							'name' => 'NC_CMS_ADD',
							'title' => '添加',
							'url' => 'article://sitehome/article/addarticle',
							'is_menu' => 0,
						],
						[
							'name' => 'NC_CMS_EDIT',
							'title' => '编辑',
							'url' => 'article://sitehome/article/editarticle',
							'is_menu' => 0,
						],
						[
							'name' => 'NC_CMS_DELETE',
							'title' => '删除',
							'url' => 'article://sitehome/article/deletearticle',
							'is_menu' => 0,
						],
					],
				],
				[
					'name' => 'NC_CMS_ARTICLE_CAREGORY',
					'title' => '文章分类',
					'url' => 'article://sitehome/article/articlecategorylist',
					'icon' => 'addon/module/Article/sitehome/view/public/img/menu_icon/article_class.png',
					'icon_selected' => '',
					'is_menu' => 1,
					'sort' => 1,
					'child_list' => [
						[
							'name' => 'NC_CMS_CAREGORY_ADD',
							'title' => '添加',
							'url' => 'article://sitehome/article/addarticlecategory',
							'is_menu' => 0,
						],
						[
							'name' => 'NC_CMS_CAREGORY_EDIT',
							'title' => '编辑',
							'url' => 'article://sitehome/article/editarticlecategory',
							'is_menu' => 0,
						],
						[
							'name' => 'NC_CMS_CAREGORY_DELETE',
							'title' => '删除',
							'url' => 'article://sitehome/article/deletearticlecategory',
							'is_menu' => 0,
						],
					],
				
				],
				[
					'name' => 'NC_CMS_ARTICLE_COMMENT',
					'title' => '文章评论',
					'url' => 'article://sitehome/comment/commentlist',
					'icon' => 'addon/module/Article/sitehome/view/public/img/menu_icon/article_comments.png',
					'icon_selected' => '',
					'is_menu' => 1,
					'sort' => 2
				],
				[
					'name' => 'NC_CMS_ARTICLE_REWARD',
					'title' => '赞赏',
					'icon' => 'addon/module/Article/sitehome/view/public/img/menu_icon/article_appreciate.png',
					'icon_selected' => '',
					'url' => 'article://sitehome/reward/rewardlist',
					'is_menu' => 1,
					'sort' => 3
				],
			],
		]
	],
	'diy' => [
		
		'view' => [
			[
				'name' => 'NC_ARTICLE_H5_LIST',
				'title' => '文章列表',
				'value' => '{"global":{"title":"文章列表","bgColor":"#ffffff","bgUrl":"","openBottomNav" : true},"value":[{"addon_name":"Article","controller":"ArticleList","type":"NC_ARTICLE_LIST","name":"文章列表"}]}',
				'type' => 'H5',
				'icon' => 'addon/module/Article/sitehome/view/public/img/menu_icon/article_list.png'
			],
			[
				'name' => 'NC_ARTICLE_H5_DETAIL',
				'title' => '文章详情',
				'value' => '{"global":{"title":"文章详情","bgColor":"#ffffff","bgUrl":"","openBottomNav" : false},"value":[{"title":"文章详情","backgroundColor":"#ffffff","textColor":"#000000","isOpenOperation":false,"link":{},"operation_name":"操作","fontSize":16,"addon_name":"DiyView","type":"TITLE","name":"顶部标题","controller":"Title"},{"addon_name":"Article","controller":"ArticleDetail","type":"NC_ARTICLE_DETAIL","name":"文章详情"},{"addon_name":"Article","type":"NC_ARTICLE_REWARD","name":"赞赏","controller":"ArticleReward"},{"addon_name":"Article","type":"NC_ARTICLE_COMMENT","name":"评论","controller":"ArticleComment"}]}',
				'type' => 'H5',
				'icon' => 'addon/module/Article/sitehome/view/public/img/menu_icon/article_detail.png'
			],
		],
		'link' => [
			[
				'name' => 'NC_ARTICLE_INDEX',
				'title' => '文章列表',
				'design_url' => '',
				'h5_url' => 'Article://wap/article/index',
				'weapp_url' => '/pages/article/list/list',
				'web_url' => '',
				'icon' => 'addon/module/Article/sitehome/view/public/img/menu_icon/article_list.png'
			],
			[
				'name' => 'NC_ARTICLE_CATEGORY',
				'title' => '文章分类',
				'design_url' => '',
				'h5_url' => 'Article://wap/category/category',
				'weapp_url' => '/pages/article/category/category',
				'web_url' => '',
				'icon' => 'addon/module/Article/sitehome/view/public/img/menu_icon/article_category.png'
			],
			[
				'name' => 'NC_ARTICLE_MY_COMMENT',
				'title' => '我的评论',
				'design_url' => '',
				'h5_url' => 'Article://wap/comment/mycomment',
				'weapp_url' => '/pages/article/comment/comment',
				'web_url' => '',
				'icon' => 'addon/module/Article/sitehome/view/public/img/menu_icon/my_comment.png'
			],
			[
				'name' => 'NC_ARTICLE_MY_REWARD',
				'title' => '我的赞赏',
				'design_url' => '',
				'h5_url' => 'Article://wap/reward/myreward',
				'weapp_url' => '/pages/article/reward/reward',
				'web_url' => '',
				'icon' => 'addon/module/Article/sitehome/view/public/img/menu_icon/my_reward.png'
			]
		],
		'util' => [
			[
				'name' => 'NC_ARTICLE_LIST',
				'title' => '文章列表',
				'type' => 'OTHER',
				'controller' => 'ArticleList',
				'value' => '{}',
				'sort' => '30024',
				'support_diy_view' => 'NC_ARTICLE_H5_LIST',
				'max_count' => 1
			],
			[
				'name' => 'NC_ARTICLE_DETAIL',
				'title' => '文章详情',
				'type' => 'OTHER',
				'controller' => 'ArticleDetail',
				'value' => '{}',
				'sort' => '30025',
				'support_diy_view' => 'NC_ARTICLE_H5_DETAIL',
				'max_count' => 1
			],
			[
				'name' => 'NC_ARTICLE_COMMENT',
				'title' => '评论',
				'type' => 'OTHER',
				'controller' => 'ArticleComment',
				'value' => '{}',
				'sort' => '30026',
				'support_diy_view' => 'NC_ARTICLE_H5_DETAIL',
				'max_count' => 1
			],
			[
				'name' => 'NC_ARTICLE_REWARD',
				'title' => '赞赏',
				'type' => 'OTHER',
				'controller' => 'ArticleReward',
				'value' => '{}',
				'sort' => '30027',
				'support_diy_view' => 'NC_ARTICLE_H5_DETAIL',
				'max_count' => 1
			],
		]
	],
	'message_template' => [
		[
			"keyword" => "COMMENT_SUCCESS",
			"title" => "用户评论",
			"port" => "Sms,Email,Wechat",
			"addon" => "Article",
			"var_json" => '{"content":"评论内容","time":"评论时间"}',
			"wechat_json" => '{"template_no":"OPENTM413134192","title":"评论成功","remark":"您的订单评价得到了回复\n消费门店：{{keyword1.DATA}}\n服务导购：{{keyword2.DATA}}\n订单编号：{{keyword3.DATA}}\n回复人：{{keyword4.DATA}}\n感谢您的对我们的支持。" }',
		],
		[
			"keyword" => "AWARD_SUCCESS",
			"title" => "用户赞赏",
			"port" => "Sms,Email,Wechat",
			"addon" => "Article",
			"var_json" => '{"content":"赞赏金额","time":"赞赏时间"}',
			"wechat_json" => '{"template_no":"OPENTM200564144","title":"赞赏成功","remark":"你好，你已成功点赞。\n商品名称:{{keyword1.DATA}}\n点赞时间:{{keyword2.DATA}}\n目前点赞次数:{{keyword3.DATA}}\n谢谢您的点赞" }',
		]
	],
];