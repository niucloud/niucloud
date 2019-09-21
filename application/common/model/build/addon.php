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
namespace addon\module\{{addon_name}};

use app\common\controller\BaseAddon;

/**
 * {{addon_title}}
 */
class {{addon_name}}Addon extends BaseAddon
{
	
	public $info = array(
		'name' => '{{addon_name}}',
		'title' => '{{addon_title}}',
		'description' => '{{addon_description}}',
		'status' => 1,
		'author' => '',
		'version' => '{{addon_version}}',
		'visble' => 1,
		'type' => '{{addon_type}}',
		'category' => '{{addon_category}}',
		'content' => '{{addon_content}}',
		//预置插件，多个用英文逗号分开
		'preset_addon' => '{{addon_preset_addon}}',
		'support_addon' => '{{addon_support_addon}}',
	    'support_app_type' => '{{addon_support_app_type}}'
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
		return success();
	}
	
	/**
	 * 卸载
	 */
	public function uninstall()
	{
		return success();
	}
	
	/**
	 * 初始化站点数据，在添加站点的时候用
	 *
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
	{{addon_function}}
}