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
namespace addon\system\WeApp;

use app\common\controller\BaseAddon;

/**
 * 微信小程序插件
 */
class WeAppAddon extends BaseAddon
{
	
	public $info = array(
		'name' => 'WeApp',
		'title' => '微信小程序',
		'description' => '微信小程序管理',
		'status' => 1,
		'author' => '',
		'version' => '1.0',
		'visble' => 0,
		'type' => 'ADDON_SYSTEM',
		'category' => 'SYSTEM',
		'content' => '微信小程序管理',
		//预置插件，多个用英文逗号分开
		'preset_addon' => '',
		'support_addon' => '',
		'support_app_type' => 'weapp'
	);
	
	public $config;
	
	public function __construct()
	{
		parent::__construct();
		$this->config = $this->config_info;
	}
	
	/**
	 * 安装
	 */
	public function install()
	{
		$this->executeSql('install');
		return success();
	}
	
	/**
	 * 卸载
	 */
	public function uninstall()
	{
/* 		$this->executeSql('uninstall');
		return success(); */
		return error('', 'System addon can not be uninstalled!');
	}
	
	/**
	 * 初始化站点数据，在添加站点的时候用
	 * @param integer $site_id
	 * @return boolean
	 */
	public function addToSite($site_id)
	{
	    return success();
	}
	
	/**
	 * 删除站点数据--删除站点时调用
	 *
	 * @param integer $site_id
	 * @return boolean
	 */
	public function delFromSite($site_id)
	{
		return success();
	}
	
	/**
	 * 复制站点数据--复制站点时调用
	 *
	 * @param integer $site_id
	 * @param integer $new_site_id
	 * @return boolean
	 */
	public function copyToSite($site_id, $new_site_id)
	{
		return success();
	}
	
}