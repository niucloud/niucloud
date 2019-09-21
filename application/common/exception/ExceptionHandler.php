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
namespace app\common\exception;

use think\exception\Handle;
use Exception;

/*
 * 重写Handle的render方法，实现自定义404
 */

class ExceptionHandler extends Handle
{
	public function render(Exception $e)
	{
		// 如果是服务器未处理的异常，将http状态码设置为500，并记录日志
		if (config('app_debug')) {
			// 调试状态下需要显示TP默认的异常页面，因为TP的默认页面
			// 很容易看出问题
			return parent::render($e);
		}
		return view(APP_PATH . 'common/view/public/404.html', [ 'base' => 'sitehome@style/base' ]);
	}
}