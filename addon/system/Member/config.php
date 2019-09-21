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
			'name' => 'NC_MEMBER_INDEX',
			'title' => '会员管理',
			'url' => 'member://sitehome/member/memberlist',
			'is_menu' => 1,
			'parent' => 'MEMBER_ROOT',
			'icon' => 'addon/system/Member/sitehome/view/public/img/member_management.png',
			'icon_selected' => '',
			'sort' => 1,
			'child_list' => [
				[
					'name' => 'NC_MEMBER_LIST',
					'title' => '会员列表',
					'url' => 'member://sitehome/member/memberlist',
					'is_menu' => 1,
					'sort' => 1,
					'child_list' => [
						[
							'name' => 'NC_MEMBER_ADD',
							'title' => '会员添加',
							'url' => 'member://sitehome/member/addmember',
							'is_menu' => 0,
						],
						[
							'name' => 'NC_MEMBER_EDIT',
							'title' => '会员修改',
							'url' => 'member://sitehome/member/editmember',
							'is_menu' => 0,
						],
						[
							'name' => 'NC_MEMBER_DELETE',
							'title' => '会员删除',
							'url' => 'member://sitehome/member/deletemember',
							'is_menu' => 0,
						],
						[
							'name' => 'NC_MEMBER_ACCOUNT_DETAIL',
							'title' => '会员账户明细',
							'url' => 'member://sitehome/member/accountdetail',
							'is_menu' => 0,
						],
						[
							'name' => 'NC_MEMBER_DETAIL',
							'title' => '会员详情',
							'url' => 'Member://sitehome/member/memberdetails',
							'is_menu' => 0,
						]
					]
				],
//				[
//					'name' => 'NC_MEMBER_LEVEL',
//					'title' => '会员等级',
//					'sort' => 2,
//					'url' => 'Member://sitehome/member/levellist',
//					'is_menu' => 0,
//					'child_list' => [
//						[
//							'name' => 'NC_MEMBER_LEVEL_ADD',
//							'title' => '等级添加',
//							'url' => 'Member://sitehome/member/addlevel',
//							'is_menu' => 0,
//						],
//						[
//							'name' => 'NC_MEMBER_LEVEL_EDIT',
//							'title' => '等级修改',
//							'url' => 'Member://sitehome/member/editlevel',
//							'is_menu' => 0,
//						],
//						[
//							'name' => 'NC_MEMBER_LEVEL_DELETE',
//							'title' => '等级删除',
//							'url' => 'member://sitehome/member/deletelevel',
//							'is_menu' => 0,
//						],
//					]
//				],
				[
					'name' => 'NC_MEMBER_GROUP',
					'title' => '会员组',
					'sort' => 3,
					'url' => 'member://sitehome/group/grouplist',
					'is_menu' => 1,
					'child_list' => [
						[
							'name' => 'NC_MEMBER_GROUP_ADD',
							'title' => '会员组添加',
							'url' => 'member://sitehome/group/addgroup',
							'is_menu' => 0,
						],
						[
							'name' => 'NC_MEMBER_GROUP_EDIT',
							'title' => '会员组修改',
							'url' => 'member://sitehome/group/editgroup',
							'is_menu' => 0,
						],
						[
							'name' => 'NC_MEMBER_GROUP_DELETE',
							'title' => '会员组删除',
							'url' => 'member://sitehome/group/deletegroup',
							'is_menu' => 0,
						],
					]
				],
				[
					'name' => 'NC_MEMBER_LABEL',
					'title' => '会员标签',
					'sort' => 3,
					'url' => 'member://sitehome/member/labellist',
					'is_menu' => 1,
					'child_list' => [
						[
							'name' => 'NC_MEMBER_LABEL_ADD',
							'title' => '标签添加',
							'url' => 'member://sitehome/member/addlabel',
							'is_menu' => 0,
						],
						[
							'name' => 'NC_MEMBER_LABEL_EDIT',
							'title' => '标签修改',
							'url' => 'member://sitehome/member/editlabel',
							'is_menu' => 0,
						],
						[
							'name' => 'NC_MEMBER_LABEL_DELETE',
							'title' => '标签删除',
							'url' => 'member://sitehome/member/deletelabel',
							'is_menu' => 0,
						],
					]
				],
				[
					'name' => 'NC_MEMBER_ACCOUNT_CONFIG',
					'title' => '账户配置',
					'url' => 'member://sitehome/member/accountconfig',
					'parent' => 'SITEHOME_MEMBER',
					'is_menu' => 1,
					'sort' => 4,
				],
                [
                    'name' => 'NC_LOGIN_REG_AGREEMENT',
                    'title' => '注册协议',
                    'url' => 'member://sitehome/login/agreement',
                    'parent' => 'NC_MEMBER_INDEX',
                    'is_menu' => 1,
                    'icon' => 'addon/system/member/sitehome/view/public/image/menu_icon/membership_registration.png',
                    'sort' => 6,
                ],
                [
                    'name' => 'NC_LOGIN_REGISTER_AND_VISIT',
                    'title' => '注册设置',
                    'url' => 'member://sitehome/login/registerAndVisit',
                    'parent' => 'NC_MEMBER_INDEX',
                    'is_menu' => 1,
                    'sort' => 7,
                ],
			],
		]
	
	],
	'diy' => [
		'util' => [
			[
				'name' => 'NC_MEMBER_CENTER',
				'title' => '会员中心',
				'type' => 'SYSTEM',
				'controller' => 'Member',
				'value' => '{ "background_image": "","member_info_location": "left","textColor" : "#333333"}',
				'sort' => '30001',
				'support_diy_view' => '',
				'max_count' => 1
			],
			[
				'name' => 'NC_MEMBER_ACCOUNT',
				'title' => '会员账户',
				'type' => 'SYSTEM',
				'controller' => 'MemberAccount',
				'value' => '{}',
				'sort' => '30003',
				'support_diy_view' => '',
				'max_count' => 1
			]

		],
        'link' => [
            [
                'name' => 'NC_MEMBER_ACCOUNT_BALANCE',
                'title' => '余额明细',
                'design_url' => '',
                'h5_url' => 'Member://wap/account/balance',
                'weapp_url' => '',
                'web_url' => '',
            ],
            [
                'name' => 'NC_MEMBER_ACCOUNT_INTEGRAL',
                'title' => '积分明细',
                'design_url' => '',
                'h5_url' => 'Member://wap/account/integral',
                'weapp_url' => '',
                'web_url' => '',
            ],
            [
                'name' => 'NC_MEMBER_INFO',
                'title' => '积分明细',
                'design_url' => '',
                'h5_url' => 'Member://wap/member/member',
                'weapp_url' => '',
                'web_url' => '',
            ],
        ],

	],
    'message_template' => [
        array(
            "title" => "注册验证",
            "keyword" => "REGISTER",
            "port" => "Sms,Email",
            "addon" => "Member",
            "var_json" => '{"code":"验证码"}',
            "wechat_json" => ''
        ),
        array(
            "title" => "找回密码验证",
            "keyword" => "FIND_PASSWORD",
            "port" => "Sms,Email",
            "addon" => "Member",
            "var_json" => '{"code":"验证码"}',
            "wechat_json" => ''
        ),
        array(
            "title" => "邮箱绑定",
            "keyword" => "BIND_EMAIL",
            "port" => "Email",
            "addon" => "Member",
            "var_json" => '{"code":"验证码"}',
            "wechat_json" => ''
        ),
        array(
            "title" => "手机绑定",
            "keyword" => "BIND_MOBILE",
            "port" => "Sms",
            "addon" => "Member",
            "var_json" => '{"code":"验证码"}',
            "wechat_json" => ''
        ),
        array(
            "title" => "注册成功",
            "keyword" => "REGISTER_SUCCESS",
            "port" => "Sms,Email",
            "addon" => "Member",
            "var_json" => '{"username":"用户名"}',
            "wechat_json" => ''
        )
    ],

];