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
namespace addon\system\FileQiniu;

use addon\system\FileQiniu\common\model\File;
use app\common\controller\BaseAddon;
use app\common\model\Site;

/**
 * 七牛上传管理插件
 */
class FileQiniuAddon extends BaseAddon
{
	public $replace;
	public $info = array(
		'name' => 'FileQiniu',
		'title' => '七牛云存储',
		'description' => '七牛云存储上传',
		'status' => 1,
		'author' => '',
		'version' => '1.0',
		'visble' => 0,
		'type' => 'ADDON_SYSTEM',
	    'category' => 'SYSTEM',
		'content' => '七牛云存储上传',
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
	
	/**文件存储
	 * @param array $param
	 */
	public function fileStore($param = [])
	{
		$site_id = 0;
		$config_info = model('nc_site_config')->getInfo([ 'site_id' => $site_id, 'name' => 'NC_FILE_UPLOAD_QINIU_CONFIG' ]);
		
		if ($config_info["status"] == 1) {
			$file_name = $param["file_name"];
			$upload_path = $param["upload_path"];
			$file = new File();
			$res = $file->fileStore($upload_path, $file_name, json_decode($config_info["value"], true));
			
			return $res;
		}
	}

	
	/**
	 * 获取上传配置
	 * @param array $param
	 */
	public function getFileConfig($param = [])
	{
        $condition = array(
            "site_id" => 0,
            "name" => "NC_FILE_UPLOAD_QINIU_CONFIG"
        );
        $site_model = new Site();
        $config_info_result = $site_model->getSiteConfigInfo($condition);
        $config_info = $config_info_result["data"];
        if(empty($config_info["value"])){
            $config_info["value"] = [];
        }else{
            $config_info["value"] = json_decode($config_info["value"], true);
        }
        $this->info["url"] = addon_url('FileQiniu://admin/config/config');
		return [
			'info' => $this->info,
			'config' => $config_info
		];
	}

	
	/**
	 * 关闭上传(用于开启某项上传时，关闭其他项)
	 * @param param
	 */
	public function closeFileType($param = [])
	{
        $site_model = new Site();
        $site_id = 0;
        $data = array(
            "status" => 0
        );
        $condition = array(
            "site_id" => $site_id,
            "name" => "NC_FILE_UPLOAD_QINIU_CONFIG"
        );
        $res = $site_model->editSiteConfig($data, $condition);
        return $res;
	}

}