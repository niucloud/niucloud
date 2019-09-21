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
namespace addon\system\FileAliyun;

use app\common\controller\BaseAddon;


/**
 * 阿里云上传插件
 */
class FileAliyunAddon extends BaseAddon
{
	public $replace;
	
	public $info = array(
		'name' => 'FileAliyun',
		'title' => '阿里云存储',
		'description' => '对象存储 OSS',
		'status' => 1,
		'author' => '',
		'version' => '1.0',
		'visble' => 0,
		'type' => 'ADDON_SYSTEM',
	    'category' => 'SYSTEM',
		'content' => '阿里云存储',
		'config' => '',
		//预置插件，多个用英文逗号分开
		'preset_addon' => 'File',
		'support_addon' => '',
	    'support_app_type' => 'wap,weapp'
	);
	public $config;
	
	public $upload_path = __UPLOAD__;
	
	public function __construct()
	{
		parent::__construct();
		$this->config = $this->config_info;
		$this->replace = [];
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
		$this->executeSql('uninstall');
		return success();
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
	 * @param integer $site_id
	 * @return boolean
	 */
	public function delFromSite($site_id)
	{
		return success();
	}
	
	/**
	 * 复制站点数据--复制站点时调用
	 * @param integer $site_id
	 * @param integer $new_site_id
	 * @return boolean
	 */
	public function copyToSite($site_id, $new_site_id)
	{
		return success();
	}
}