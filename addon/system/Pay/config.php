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
			'name' => 'NC_PAY',
			'title' => '支付管理',
			'url' => 'pay://sitehome/pay/lists',
			'parent' => 'ADDON_ROOT',
			'is_menu' => 1,
			'child_list' => [
				[
					'name' => 'NC_PAY_CONFIG',
					'title' => '支付管理',
					'url' => 'pay://sitehome/pay/lists',
					'is_menu' => 1,
				],
				[
					'name' => 'NC_PAY_LIST',
					'title' => '支付记录',
					'url' => 'pay://sitehome/pay/paylist',
					'is_menu' => 1,
				]
			]
		]
	],

];