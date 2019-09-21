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
	//系统插件分类
	'addon_category' => [
		'SYSTEM' => [ 'category_name' => 'SYSTEM', 'category_title' => '系统工具', 'desc' => '系统工具化插件' ],
		'CHANNEL' => [ 'category_name' => 'CHANNEL', 'category_title' => '运营渠道', 'desc' => '多渠道运营你的系统' ],
		'OTHER' => [ 'category_name' => 'OTHER', 'category_title' => '其他模块', 'desc' => '其他模块' ],
	],
	//插件类型
	'addon_type' => [
		'ADDON_APP' => '应用',
		'ADDON_MODULE' => '模块',
		'ADDON_SYSTEM' => '系统'
	],
	//系统端口支持类型
	'support_app_type' => [
		'wap' => [ 'name' => '微信公众号', 'logo' => 'public/static/img/wx_public_number.png' ],
		'weapp' => [ 'name' => '微信小程序', 'logo' => 'public/static/img/wx_small_procedures.png' ],
		'aliapp' => [ 'name' => '支付宝小程序', 'logo' => 'public/static/img/alipay_small_procedures.png' ],
		'baiduapp' => [ 'name' => '百度小程序', 'logo' => 'public/static/img/baidu_small_procedures.png' ],
	]
];