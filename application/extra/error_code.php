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
/**
 * 系统
 */
defined('SUCCESS')              or  define('SUCCESS',                   0);
defined('REQUEST_SUCCESS')      or  define('REQUEST_SUCCESS',           0);
defined('SAVE_SUCCESS')         or  define('SAVE_SUCCESS',              0);
defined('DELETE_SUCCESS')       or  define('DELETE_SUCCESS',            0);

defined('FAIL')                 or  define('FAIL',                      400);
defined('REQUEST_FAIL')         or  define('REQUEST_FAIL',              400);
defined('SAVE_FAIL')            or  define('SAVE_FAIL',                 400);
defined('DELETE_FAIL')          or  define('DELETE_FAIL',               400);
defined('UPLOAD_FAIL')          or  define('UPLOAD_FAIL',               400);

defined('UNKNOW_ERROR')         or  define('UNKNOW_ERROR',              -1);

/**
 * 参数
 */
defined('PARAMETER_ERROR')      or  define('PARAMETER_ERROR',           10000);

/**
 * 插件  以 2 开头
 */
defined('ADDON_NOT_EXIST')      or  define('ADDON_NOT_EXIST',           20000);
defined('ADDON_IS_EXIST')       or  define('ADDON_IS_EXIST',            20001);
defined('ADDON_ADD_CATEGORY_FAIL')       or  define('ADDON_ADD_CATEGORY_FAIL',            20002);
defined('ADDON_UPDATE_HOOK_FAIL')       or  define('ADDON_UPDATE_HOOK_FAIL',            20003);

/**
 * 站点
 */
defined('SITE_ADD_MENU_FAIL')   or  define('SITE_ADD_MENU_FAIL',        30001);
defined('SITE_APP_INIT_FAIL')   or  define('SITE_INIT_APP_FAIL',        30002);
defined('SITE_MODULE_INIT_FAIL')or  define('SITE_MODULE_INIT_FAIL',     30003);

/**
 * 平台用户
 */
defined('USER_EXISTED')         or  define('USER_EXISTED',              40001);
defined('USER_NOT_EXIST')       or  define('USER_NOT_EXIST',            40002);
defined('PASSWORD_ERROR')       or  define('PASSWORD_ERROR',            40003);
defined('USER_IS_LOCKED')       or  define('USER_IS_LOCKED',            40004);

/**
 * 微信
 */
defined('WECHAT_UNCONFIGURED')  or  define('WECHAT_UNCONFIGURED',       90001);
defined('WECHAT_EDIT_MENU_FAIL')or  define('WECHAT_EDIT_MENU_FAIL',     90002);
defined('WECHAT_UPDATE_FANS_FAIL')or  define('WECHAT_UPDATE_FANS_FAIL', 90003);

/**
 * 配送管理
 */
defined('DELIVERY_O2O_OPEN_ADDRESS_FAIL')  or  define('DELIVERY_O2O_OPEN_ADDRESS_FAIL',       100001);


/**
 * 仓储
 */

defined('DOCUMENT_TYPE_FAIL')  or  define('DOCUMENT_TYPE_FAIL',       110001);

/**
 * 授权码
 */
defined('AUZ_NOT_EXIST')  or  define('AUZ_NOT_EXIST',       120001);
defined('AUZ_NOT_UID')  or  define('AUZ_NOT_UID',       120002);
defined('AUZ_REQUEST_APP_KEY')  or  define('AUZ_REQUEST_APP_KEY',       120003);
defined('AUZ_REQUEST_APP_SECRET')  or  define('AUZ_REQUEST_APP_SECRET',       120004);
defined('AUZ_SECRET_WRONG')  or  define('AUZ_SECRET_WRONG',       120005);
defined('AUZ_APP_KEY_HAS_USED')  or  define('AUZ_APP_KEY_HAS_USED',       120006);

/**
 * 七牛
 */
defined('UPLOAD_QINIU_CONFIG_ERROR')  or  define('UPLOAD_QINIU_CONFIG_ERROR',       130001);

/**
 *订单核销
 */
defined('ORDER_PRODUCT_VERIFY_NOT_AGREEMENT')   or  define('ORDER_PRODUCT_VERIFY_NOT_AGREEMENT',       140001);
defined('ORDER_PRODUCT_VERIFY_TIME_NOT_ENOUGH')   or  define('ORDER_PRODUCT_VERIFY_TIME_NOT_ENOUGH',       140002);
defined('ORDER_PRODUCT_VERIFY_CURRENT_ACCOUNT_NOT_AGREEMENT')   or  define('ORDER_PRODUCT_VERIFY_CURRENT_ACCOUNT_NOT_AGREEMENT',       140003);


/**
 *物流
 */
defined('NS_EXPRESS_TEMPLATE')  or  define('NS_EXPRESS_TEMPLATE',       150001);
defined('NS_EXPRESS_COMPANY')  or  define('NS_EXPRESS_COMPANY',       150002);
defined('NS_EXPRESS_NOT_SET')  or  define('NS_EXPRESS_NOT_SET',       150003);
defined('NS_EXPRESS_MEMBER_ADDRESS')  or  define('NS_EXPRESS_MEMBER_ADDRESS',       150004);
defined('NS_EXPRESS_ADDRESS_NOTSUPPORT')  or  define('NS_EXPRESS_ADDRESS_NOTSUPPORT',       150005);
defined('NS_EXPRESS_O2O_NOTCONFIG')  or  define('NS_EXPRESS_O2O_NOTCONFIG',       150006);
defined('NS_EXPRESS_O2O_NOT_SITE_ADDRESS')  or  define('NS_EXPRESS_O2O_NOT_SITE_ADDRESS',       150007);
defined('NS_EXPRESS_O2O_NOT_START_PRICE')  or  define('NS_EXPRESS_O2O_NOT_START_PRICE',       150008);
defined('NS_EXPRESS_O2O_OUT_OF_RANGE')  or  define('NS_EXPRESS_O2O_OUT_OF_RANGE',       150009);




/**
 * 订单
 */
defined('NS_ORDER_NO_HAVE_POINT')  or  define('NS_ORDER_NO_HAVE_POINT',       170001);




