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

use think\Log;
use traits\controller\Jump;

/*
 * 站点或者插件过期异常
 */

class ExpireException extends \think\Exception
{
	use Jump;
	
	/**
	 * 获取异常
	 * @param array $data_array
	 * 插件过期：['type' => 'addon|site|auth', 'addon_name' => 'alipay']
	 */
	public function __construct($data_array)
	{
		Log::write("检测过期异常" . json_encode($data_array));
		if ($data_array['type'] == 'auth') {
			echo json_encode(error('请配置站点授权'));
		}
		if ($data_array['type'] == 'site') {
			if (request()->isPost()) {
				echo json_encode(error('', '当前站点已过期，请及时续费!'));
				exit();
			}
		}
		if ($data_array['type'] == 'addon') {
			if (request()->isPost()) {
				echo json_encode(error($data_array['addon_name'] . '已过期，请及时续费!'));
			} else {
				$this->redirect(url('sitehome/addons/buy', [ 'name' => $data_array['addon_name'] ]));
			}
		}
	}
}