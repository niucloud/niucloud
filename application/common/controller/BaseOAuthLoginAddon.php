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
namespace app\common\controller;

/**
 * 登录系统插件
 */
abstract class BaseOAuthLoginAddon extends BaseAddon
{
	
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 获取第三方登录配置信息
	 * @param unknown $param
	 */
	abstract function getOAuthLoginConfig($param = []);
	
	/**
	 * 获取第三方登录配置url
	 * @param unknown $param
	 */
	protected function oauthLoginConfig($param = [])
	{
		if ($param['name'] == $this->info['name']) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 第三方登录
	 * @param array $param
	 */
	protected function oAuthLogin($param = [])
	{
		if ($param['type'] == $this->info['name']) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 安装
	 */
	public function install()
	{
		//设置默认值
		return true;
	}
	
	/**
	 * 卸载
	 */
	public function uninstall()
	{
		//删除掉，设置别的为默认
		return true;
	}
}