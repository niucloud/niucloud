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

namespace addon\system\File\common\model;

use app\common\model\Site;

/**
 * 文件管理
 * @author Administrator
 *
 */
class File
{
	public $site_model;
	
	public function __construct()
	{
		$this->site_model = new Site();
	}
	
	/***********************************************************************************文件处理******************************************************/
	/**
	 * 添加文件
	 * @param array $data
	 */
	public function addFile($data = [])
	{
		$res = model('nc_file')->add($data);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		//同步本分组下的图片数量
		$this->syFileCategoryNum([ "site_id" => $data["site_id"], "category_id" => $data["category_id"] ]);
		return success($res);
	}
	
	/**
	 * 添加文件（多条）
	 * @param array $data
	 */
	public function addFileList($data = [])
	{
		$res = model('nc_file')->addList($data);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		//同步本分组下的图片数量
		$this->syFileCategoryNum([ "site_id" => $data["site_id"] ]);
		return success($res);
	}
	
	/**
	 * 删除文件
	 * @param array $condition
	 */
	public function deleteFile($condition = [])
	{
		$res = model('nc_file')->delete($condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		//同步本分组下的图片数量
		$this->syFileCategoryNum($condition);
		return success($res);
	}
	
	/**
	 * 获取文件信息
	 * @param array $condition
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getFileInfo($condition = [], $field = '*')
	{
		$res = model('nc_file')->getInfo($condition, $field);
		return success($res);
	}
	
	/**
	 *获取文件列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getFileList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$res = model('nc_file')->getList($condition, $field, $order, '', '', '', $limit);
		return success($res);
	}
	
	/**
	 * 查询文件数量
	 * @param array $condition
	 */
	public function getFileCount($condition = [])
	{
		$res = model('nc_file')->stat($condition);
		return success($res);
	}
	
	/**
	 * 获取文件分页列表
	 * @param array $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getFilePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$res = model('nc_file')->pageList($condition, $field, $order, $page, $page_size);
		return success($res);
	}
	
	/**
	 * 修改文件的分组id
	 * @param array $condition
	 * @param int $category_id
	 */
	public function modifyFileCategoryId($category_id, $condition)
	{
		$res = model('nc_file')->update([ 'category_id' => $category_id ], $condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 修改文件名称
	 * @param array $condition
	 * @param string $file_name
	 */
	public function modifyFileName($data, $condition)
	{
		$res = model('nc_file')->update($data, $condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/***********************************************************************************文件分组处理***************************************************/
	/**
	 * 添加文件分组
	 * @param array $data
	 */
	public function addFileCategory($data)
	{
		$res = model('nc_file_category')->add($data);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 添加文件分组(多条)
	 * @param array $data
	 */
	public function addFileCategoryList($data)
	{
		$res = model('nc_file_category')->addList($data);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 修改文件分组
	 * @param array $data
	 * @param array $condition
	 */
	public function editFileCategory($data, $condition)
	{
		$res = model('nc_file_category')->update($data, $condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 删除文件分组
	 * @param unknown $condition
	 */
	public function deleteFileCategory($condition)
	{
		$res = model('nc_file_category')->delete($condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 获取文件分组信息
	 * @param array $condition
	 */
	public function getFileCategoryInfo($condition)
	{
		$res = model('nc_file_category')->getInfo($condition);
		return success($res);
	}
	
	/**
	 * 获取文件分组分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getFileCategoryPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$res = model('nc_file_category')->pageList($condition, $field, $order, $page, $page_size);
		return success($res);
	}
	
	/**
	 *获取文件列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getFileCategoryList($condition = [], $field = 'nfc.id,nfc.site_id,nfc.name,nfc.type,nfc.is_default,nfc.sort,nfc.cover,count(ncf.category_id) as num', $order = '', $limit = null)
	{
		$join = [
			[ 'nc_file ncf', 'ncf.category_id = nfc.id', 'left' ],
		];
		$group = 'nfc.id';
		$res = model('nc_file_category')->getList($condition, $field, $order, 'nfc', $join, $group, $limit);
		return success($res);
	}
	
	/***********************************************************************************上传设置***************************************************/
	
	/**
	 * 获取上传配置
	 * @param int $site_id
	 */
	public function getFileUploadConfig($site_id)
	{
		$site_config = model('nc_site_config');
		$config = $site_config->getInfo([ 'name' => 'FILE_UPLOAD_CONFIG', 'site_id' => $site_id ], "*");
		if (empty($config)) {
			$data["name"] = 'FILE_UPLOAD_CONFIG';
			$data["site_id"] = $site_id;
			$data["remark"] = "上传配置";
			$data["type"] = 1;
			$data["title"] = "上传配置";
			
			$json_data = array();
			$thumb_spec = array(
				"big_width" => 800,
				"big_height" => 800,
				"mid_width" => 400,
				"mid_height" => 400,
				"small_width" => 200,
				"small_height" => 200
			);
			$json_data["thumb"] = [ "thumb_type" => 3, "thumb_spec" => $thumb_spec ];
			$json_data["watermark"] = [ "is_open_watermark" => 'false', "watermark_image" => '', "transparence" => 0, "watermark_position" => 1, 'watermark_type' => 1, 'watermark_offset' => '', 'watermark_angle' => '', 'watermark_color' => '' ];
			$json_data = json_encode($json_data);
			
			$data["value"] = $json_data;
			//上传配置
			$res = $site_config->add($data);
			$site_config = model('nc_site_config');
			$config = $site_config->getInfo([ 'name' => 'FILE_UPLOAD_CONFIG', 'site_id' => $site_id ], "*");
		}
		$value = [];
		if (!empty($config["value"])) {
			$value = json_decode($config["value"], true);
		}
		$config["value"] = $value;
		return success($config);
	}
	
	/**
	 * 设置上传配置
	 * @param int $data 上传设置
	 * @param int $site_id
	 */
	public function setFileUploadConfig($data)
	{
		$data["name"] = 'FILE_UPLOAD_CONFIG';
		$result = $this->site_model->setSiteConfig($data);
		return $result;
	}
	
	/**
	 * 修改相册下的图片数量
	 * @param unknown $condition
	 */
	public function syFileCategoryNum($condition)
	{
		$file_category_model = model('nc_file_category');
		$file_category_model->startTrans();
		try {
			$file_model = model('nc_file');
			
			$category_id = $condition["category_id"];
			$site_id = $condition["site_id"];
			if (empty($category_id)) {
				$file_category_list = $file_category_model->getList([ "site_id" => $site_id ]);
				foreach ($file_category_list as $k => $v) {
					$category_id = $v["id"];
					$count = $file_model->getCount([ "category_id" => $category_id ]);//获取本商品分组下的图片数量
					$data = array(
						"num" => $count
					);
					$res = $file_category_model->update($data, [ 'id' => $category_id, 'site_id' => $site_id ]);
				}
			} else {
				$count = $file_model->getCount([ "category_id" => $category_id ]);//获取本商品分组下的图片数量
				
				$data = array(
					"num" => $count
				);
				$res = $file_category_model->update($data, [ 'id' => $category_id, 'site_id' => $site_id ]);
			}
			$file_category_model->commit();
			return success($res);
		} catch (\Exception $e) {
			$file_category_model->rollback();
			return error(0, 'UNKNOW_ERROR');
		}
		
	}
	
	public function deleteSite($site_id)
	{
		model('nc_file_category')->delete([ 'site_id' => $site_id ]);
		model('nc_file')->delete([ 'site_id' => $site_id ]);
		return success();
	}
	
}