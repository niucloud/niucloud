<?php

return [
	'SUCCESS' => '操作成功',
	'FAIL' => '操作失败',
	'SAVE_SUCCESS' => '保存成功',
	'SAVE_FAIL' => '保存失败',
	'REQUEST_SUCCESS' => '请求成功',
	'REQUEST_FAIL' => '请求失败',
	'UPLOAD_FAIL' => '上传失败',
	'DELETE_SUCCESS' => '删除成功',
	'DELETE_FAIL' => '删除失败',
	'UNKNOW_ERROR' => '未知错误',
	'PARAMETER_ERROR' => '参数错误',
	'PASSWORD_ERROR' => '密码错误',
	'NO_ACCESS_TOKEN' => '缺少必须参数access_token',
	
	'ADDON_NOT_EXIST' => '插件不存在',
	'ADDON_IS_EXIST' => '插件已经存在',
    'ADDON_ADD_CATEGORY_FAIL' => '插件分类安装失败',
	'ADDON_NAME_NOT_EXIST' => '插件标识已经存在，请重新输入',
	'ADDON_MISSIMG_ADDON_NAME' => '缺少插件标识',
	'ADDON_INFO_ERROR' => '插件信息有误，请检查信息缺失或插件标识重复',
	'PRESET_ADDON_NOT_INSTALL' => '安装插件失败：预置插件未安装（%s）',
	'ADDON_PRE_INSTALL_ERROR' => '插件预安装失败：%s',
	'ADDON_INSTALL_MENU_FAIL_EXISTED' => '安装菜单失败：菜单已存在（%s）',
	'ADDON_INSTALL_MENU_FAIL' => '安装菜单失败：%s',
	'ADDON_INSTALL_FAIL' => '安装插件失败：%s',
	'ADDON_ADD_FAIL' => '安装插件失败：写入插件数据失败',
	'ADDON_UPDATE_HOOK_FAIL' => '安装插件失败：更新钩子处插件失败',
	
	'ADDON_UNINSTALL_FAIL' => '执行卸载失败',
	'ADDON_UNINSTALL_MENU_FAIL' => '执行卸载失败',
	
	'USER_EXISTED' => '用户已存在',
	'USER_NOT_EXIST' => '用户不存在',
	'USER_IS_LOCKED' => '用户已被锁定',
	'SIGN_ERROR' => '签名错误',
	'PERMISSION_DENIED' => '当前用户没有权限',
	'SITE_PERMISSION_DENIED' => '站点不存在或者权限不足',
	
	'SITE_ADD_MENU_FAIL' => '站点添加失败：添加菜单时错误',
	'SITE_ADD_PRESET_ADDON_ERROR' => '站点添加失败：预置插件（%s）没有购买',
	'SITE_APP_INIT_FAIL' => '站点添加失败：应用初始化失败',
	'SITE_DELETE_FAIL_NO_SITEID' => '删除站点失败：缺少site_id',
	'SITE_DELETE_FAIL' => '删除站点失败',
	'SITE_COPY_FAIL_NO_SITEID' => '复制站点失败：缺少site_id',
	'SITE_COPY_FAIL' => '复制站点失败',
	
	'WECHAT_UNCONFIGURED' => '当前未配置微信授权',
	'WECHAT_EDIT_MENU_FAIL' => '修改菜单失败',
	'WECHAT_UPDATE_FANS_FAIL' => '当前无粉丝列表或者获取失败',
	
	'NS_USERNAME_EXISTED' => '用户名已被使用',
	'NS_MOBILE_EXISTED' => '该手机号已被使用',
	'NS_EMAIL_EXISTED' => '该邮箱已被使用',
	'NS_LEVEL_NAME_EXISTED' => '该会员等级已存在',
	'NS_LABEL_NAME_EXISTED' => '该会员标签已存在',
	'RESULT_ERROR' => '添加失败',
	
	'DELIVERY_O2O_OPEN_ADDRESS_FAIL' => '站点地址配置完毕后才能开启本地配送',
	
	'BUY_ADDON_NOT_REPEATEDLY_TRY' => '不能多次试用',
	'BUY_ADDON_OWN_OPERATION' => '不是本人操作',
	
	'ANNOUNCEMENT_NOT_EXIST' => '公告不存在',
	
	'ANNOUNCEMENT_TITLE_DUPLICATE' => '公告标题重复',
	
	'DOCUMENT_TYPE_FAIL' => '不存在的仓储类型',
	
	'UPLOAD_QINIU_CONFIG_ERROR' => '七牛云存储配置有误！',
	
	//商品sku部分
	'PRODUCT_SKU_NOT_EXIST' => '未查询到商品sku数据',
	'MORE_THAN_PRODUCT_SKU_STOCK' => '不可超出商品库存',
	'MORE_THAN_PRODUCT_SKU_MAX_BUY' => '不可超出商品最大购买量',
	'LESS_THAN_PRODUCT_SKU_MIN_BUY' => '不可少于商品最少购买量',
	'PRODUCT_SKU_STOCK_IS_NULL' => '商品sku库存为零',
	
	//商品购物车部分
	'SHOPPING_CART_NOT_EXIST' => '未查询到购物车数据',
	
	'WEB_AUTH_UPDATE_FREQUENCY_EXCEED' => '修改授权域名超过次数',
	
	'ORDER_PRODUCT_VERIFY_CURRENT_ACCOUNT_NOT_AGREEMENT' => '当前账号没有核销核销权限',
	'ORDER_PRODUCT_VERIFY_NOT_AGREEMENT' => '没有本站点虚拟商品你的核销权限',
	'ORDER_PRODUCT_VERIFY_TIME_NOT_ENOUGH' => '虚拟商品次数不足',
	
	//管理员
	'ADMINISTRATOR_EXISTED' => '管理员已存在',
	
	//产品分类
	'SUBCATEGORIES_UNDER_THE_CURRENT_CATRGORY' => '当前分类存在子分类，不能删除',
	
	// 注册登录
	'USERNAME_EXISTED' => '该用户名已被注册',
	'MOBILE_EXISTED' => '该手机号已被注册',
	'EMAIL_EXISTED' => '该邮箱已被注册',
	'NS_EXPRESS_TEMPLATE' => '物流模板没有配置',
	'NS_EXPRESS_COMPANY' => '物流公司没有配置',
	'NS_EXPRESS_NOT_SET' => '没有可用的配送方式',
	'NS_EXPRESS_MEMBER_ADDRESS' => '会员地址没有设置',
	'NS_ORDER_NO_HAVE_POINT' => '积分不足',
	'NS_EXPRESS_ADDRESS_NOTSUPPORT' => '不支持的收货地址',
	'NS_EXPRESS_O2O_NOTCONFIG' => '本地配送没有配置',
	'NS_EXPRESS_O2O_NOT_SITE_ADDRESS' => '站点位置未设置',
	'NS_EXPRESS_O2O_NOT_START_PRICE' => '订单价未达到起送价格',
	'NS_EXPRESS_O2O_OUT_OF_RANGE' => '收货地址超出配送范围',
	
	'NIUCENTER_AGENT_OTHER_ACCOUNT_NOT_EXIST' => '账号不存在',
];


// $define PARAMTER_ERROR    10000


// $define LOGIN_USER_ERROR  20000


// error(LOGIN_USER_ERROR, data)
// {

//     return [code = LOGIN_USER_ERROR,
//         msg = lang(LOGIN_USER_ERROR),
//         data
//     ]
// }


// $ERROR_ARRAY = [
//     PARAMTER_ERROR , lang(PARAMTER_ERROR)

// ]