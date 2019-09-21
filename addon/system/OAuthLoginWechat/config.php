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
			'name' => 'NC_OAUTHLOGINWECHAT_CONFIG',
			'title' => '微信开放平台登录配置',
			'url' => 'oauthloginwechat://sitehome/index/config',
			'parent' => 'NC_OAUTHLOGIN_LIST',
			'is_menu' => 0
		]
	],
	'autoload' => 1,
];