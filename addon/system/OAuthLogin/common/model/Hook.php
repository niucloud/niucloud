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

namespace addon\system\OAuthLogin\common\model;


/**
 * 功能说明：插件钩子
 */
class Hook
{
	/**
	 * 获取插件钩子详情
	 * @param unknown $where
	 */
	public function getHookInfo($where)
	{
		$info = model('nc_hook')->getInfo($where);
		
		return success($info);
	}
	
}