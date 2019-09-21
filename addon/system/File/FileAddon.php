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
namespace addon\system\File;

use addon\system\File\common\model\File as FileModel;
use addon\system\File\sitehome\controller\File;
use app\common\controller\BaseAddon;

/**
 * 文件管理插件
 */
class FileAddon extends BaseAddon
{
	public $replace;
	public $info = array(
		'name' => 'File',
		'title' => '文件管理',
		'description' => '文件管理',
		'status' => 1,
		'author' => '',
		'version' => '1.0',
		'visble' => 0,
		'type' => 'ADDON_SYSTEM',
		'category' => 'SYSTEM',
		'content' => '主要处理文件的上传，存储，查询等操作',
		//预置插件，多个用英文逗号分开
		'preset_addon' => '',
		'support_addon' => '',
		'support_app_type' => 'wap,weapp'
	);
	public $config;
	
	public $upload_path = __UPLOAD__;
	
	public function __construct()
	{
		parent::__construct();
		$this->config = $this->config_info;
		$this->replace = [
			'FILE_CSS' => __ROOT__ . '/addon/system/File/common/view/public/css',
			'FILE_JS' => __ROOT__ . '/addon/system/File/common/view/public/js',
			'FILE_IMG' => __ROOT__ . '/addon/system/File/common/view/public/img'
		];
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
		//       $this->executeSql('uninstall');
//		return success();
		return error('', 'System addon can not be uninstalled!');
	}
	
	/**
	 * 初始化站点数据，在添加站点的时候用
	 * @param integer $site_id
	 * @return boolean
	 */
	public function addToSite($site_id)
	{
		//默认分组
		$data = [
			[
				'name' => '默认分组',
				'type' => 'IMAGE',
				'site_id' => $site_id,
				'is_default' => 1
			],
			[
				'name' => '默认分组',
				'type' => 'AUDIO',
				'site_id' => $site_id,
				'is_default' => 1
			],
			[
				'name' => '默认分组',
				'type' => 'VIDEO',
				'site_id' => $site_id,
				'is_default' => 1
			],
		];
		$file_model = new FileModel();
		$res = $file_model->addFileCategoryList($data);
		return $res;
	}
	
	/**
	 * 删除站点数据--删除站点时调用
	 * @param integer $site_id
	 * @return boolean
	 */
	public function delFromSite($site_id)
	{
		$file_model = new FileModel();
		$file_model->deleteSite($site_id);
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
		$file_model = new FileModel();
		$file_category_list = $file_model->getFileCategoryList([ 'nfc.site_id' => $site_id ]);
		foreach ($file_category_list['data'] as $k => $v) {
			$file_list = $file_model->getFileList([ 'site_id' => $site_id, 'category_id' => $v['id'] ]);
			$v['site_id'] = $new_site_id;
			unset($v['id']);
			$category_id = $file_model->addFileCategory($v);
			foreach ($file_list['data'] as $k_file => $v_file) {
				$file_list[ $k_file ]['site_id'] = $new_site_id;
				unset($file_list['data'][ $k_file ]['id']);
			}
			$res = $file_model->addFileList($file_list['data']);
		}
		return $res;
	}
	
	
	/**
	 * 文件管理
	 * @param unknown $param
	 */
	public function fileManage($param = [])
	{
		$file_type = $param['file_type'];
		$file = new File();
		$result = $file->fileManage($file_type);
		$return_array = array_merge($result[1], $param);
		return $this->fetch($result[0], $return_array);
	}
	
	/**
	 * 文件上传，传入类型
	 * @param unknown $param
	 */
	public function fileUpload($param = [])
	{
		$file = new File();
		$param['thumb_type'] = !empty($param['thumb_type']) ? $param['thumb_type'] : "";
		$param['size'] = !empty($param['size']) ? $param['size'] : 2000;
		$result = $file->fileUploadType($param['type'], $param['file_type'], $param['size']);
		return $this->fetch($result, [ 'type' => $param['file_type'], "name" => $param['name'], "param" => $param, "thumb_type" => $param['thumb_type'] ], $this->replace);
	}
	
}