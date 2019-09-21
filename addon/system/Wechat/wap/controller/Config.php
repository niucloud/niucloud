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

namespace addon\system\Wechat\wap\controller;

use util\weixin\Weixin;
use app\common\controller\BaseSite;
use addon\system\Wechat\common\model\Wechat;
use think\Log;

/**
 * 控制器
 */
class Config extends BaseSite
{
	public $wechat;
	
	public $author_appid;
	
	public function __construct()
	{
		parent::__construct();
		$this->wechat = new Wechat();
		$action = request()->action();
		if (strtolower($action) == 'relateweixin') {
			$this->getMessage();
		}
		
	}
	
	/**
	 * ************************************************************************微信公众号消息相关方法 开始******************************************************
	 */

	
	/**
	 * ************************************************************************微信公众号消息相关方法 结束******************************************************
	 */

}