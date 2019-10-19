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

namespace addon\system\File\sitehome\controller;

use addon\system\File\common\model\File as FileModel;
use app\common\controller\BaseSiteHome;

class File extends BaseSiteHome
{
	
	protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'
	public $upload_path;//公共上传文件
	
	public function __construct()
	{
		parent::__construct();
		$this->replace = [
			'ADDON_NC_FILE_CSS' => __ROOT__ . '/addon/system/File/sitehome/view/public/css',
			'ADDON_NC_FILE_JS' => __ROOT__ . '/addon/system/File/sitehome/view/public/js',
			'ADDON_NC_FILE_IMG' => __ROOT__ . '/addon/system/File/sitehome/view/public/img',
		];
		$this->upload_path = __UPLOAD__;
	}
	
	/**
	 * 图像
	 */
	public function image()
	{
		$file_model = new FileModel();
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$type = input('type', '');
			$category_id = input('category_id', '');
			$file_name = input("file_name", "");
			$order = input("order", "create_time desc");
			$condition = array(
				'site_id' => $this->siteId,
				'type' => "IMAGE",
				'category_id' => $category_id
			);
			if (!empty($file_name)) {
				$condition['file_name'] = [ 'like', '%' . $file_name . '%' ];
			}
			
			$list = $file_model->getFilePageList($condition, $page, $limit, $order);
			return $list;
		} else {
			$category_list = $file_model->getFileCategoryList([ 'nfc.site_id' => $this->siteId, 'nfc.type' => 'IMAGE' ]);
			$this->assign("category_list", $category_list['data']);
			return $this->fetch('File/image', [], $this->replace);
		}
	}
	
	/**
	 * 获取相册分组
	 */
	function getFileCategory()
	{
		if (IS_AJAX) {
			$file_model = new FileModel();
			$type = input('type', 'IMAGE');
			$category_list = $file_model->getFileCategoryList([ 'nfc.site_id' => $this->siteId, 'nfc.type' => $type ]);
		}
		return $category_list;
	}
	
	/**
	 * 音频
	 */
	public function audio()
	{
		$file_model = new FileModel();
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$type = input('type', '');
			$category_id = input('category_id', '');
			$file_name = input("file_name", "");
			$order = input("order", "create_time desc");
			$condition = array(
				'site_id' => $this->siteId,
				'type' => "AUDIO",
				'category_id' => $category_id
			);
			if (!empty($file_name)) {
				$condition['file_name'] = [ 'like', '%' . $file_name . '%' ];
			}
			$list = $file_model->getFilePageList($condition, $page, $limit, $order);
			return $list;
		} else {
			$category_list = $file_model->getFileCategoryList([ 'nfc.site_id' => $this->siteId, 'nfc.type' => 'AUDIO' ]);
			$this->assign("category_list", $category_list['data']);
			return $this->fetch('File/audio', [], $this->replace);
		}
	}
	
	/**
	 * 视频
	 */
	public function video()
	{
		$file_model = new FileModel();
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$type = input('type', '');
			$category_id = input('category_id', '');
			$file_name = input("file_name", "");
			$order = input("order", "create_time desc");
			$condition = array(
				'site_id' => $this->siteId,
				'type' => "VIDEO",
				'category_id' => $category_id
			);
			if (!empty($file_name)) {
				$condition['file_name'] = [ 'like', '%' . $file_name . '%' ];
			}
			$list = $file_model->getFilePageList($condition, $page, $limit, $order);
			return $list;
		} else {
			$category_list = $file_model->getFileCategoryList([ 'nfc.site_id' => $this->siteId, 'nfc.type' => 'VIDEO' ]);
			$this->assign("category_list", $category_list['data']);
			return $this->fetch('File/video', [], $this->replace);
		}
	}
	
	/**
	 * 保存分组
	 */
	public function addGroup()
	{
		if (IS_AJAX) {
			$name = input('name');
			$type = input('type');
			$data = array(
				'site_id' => $this->siteId,
				'type' => $type,
				'name' => $name
			);
			$file_model = new FileModel();
			$res = $file_model->addFileCategory($data);
			return $res;
		}
	}
	
	/**
	 * 修改分组
	 */
	public function updateGroup()
	{
		if (IS_AJAX) {
			$name = input('name');
			$category_id = input('category_id');
			$data = array(
				'name' => $name
			);
			$where = array(
				'id' => $category_id
			);
			$file_model = new FileModel();
			$res = $file_model->editFileCategory($data, $where);
			return $res;
		}
	}
	
	/**
	 * 删除分组
	 */
	public function deleteGroup()
	{
		if (IS_AJAX) {
			$category_id = input('category_id');
			//把该分组下图片放到该站点下的默认分组里
			$file_model = new FileModel();
			$condition = array(
				'site_id' => $this->siteId,
				'type' => 'IMAGE',
				'is_default' => 1
			);
			$category_info = $file_model->getFileCategoryInfo($condition);
			$default_category_id = $category_info['data']['id'];
			
			$file_list = $file_model->getFileList([ 'category_id' => $category_id ]);
			foreach ($file_list['data'] as $k => $v) {
				$result = $file_model->modifyFileCategoryId($default_category_id, [ 'id' => $v['id'] ]);
			}
			
			$res = $file_model->deleteFileCategory([ 'id' => $category_id ]);
			return $res;
		}
	}
	
	/**
	 * 修改文件名
	 */
	public function updateFileName()
	{
		if (IS_AJAX) {
			$file_name = input('file_name');
			$file_id = input('file_id');
			$data = array(
				'file_name' => $file_name
			);
			$where = array(
				'id' => $file_id
			);
			$file_model = new FileModel();
			$res = $file_model->modifyFileName($data, $where);
			return $res;
		}
	}
	
	/**
	 * 修改图片分组
	 */
	public function updateImageGroup()
	{
		if (IS_AJAX) {
			$id = input('id');
			$category_id = input('category_id');
			$id = explode(',', $id);
			foreach ($id as $v) {
				$where = array(
					'id' => $v
				);
				$file_model = new FileModel();
				$res = $file_model->modifyFileCategoryId($category_id, $where);
			}
			
			return $res;
		}
	}
	
	/**
	 * 删除图片
	 */
	public function deleteFile()
	{
		if (IS_AJAX) {
			$id = input('id');
			$id = explode(',', $id);
			$file_model = new FileModel();
			
			$where = array(
				'id' => [ "in", $id ]
			);
			$res = $file_model->deleteFile($where);
			return $res;
		}
	}
	
	
	/**
	 * 相册管理界面
	 * @return mixed
	 */
	public function fileManage($file_type = 'IMAGE')
	{
		$file_model = new FileModel();
		if (IS_AJAX) {
			$page_index = input('page', 1);
			$list_rows = input('limit', PAGE_LIST_ROWS);
			$type = input('type', '');
			$category_id = input('category_id', '');
            $file_name = input("file_name", "");
            $condition = array(
				'site_id' => $this->siteId,
				'type' => $type,
				'category_id' => $category_id
			);

            if (!empty($file_name)) {
                $condition['file_name'] = [ 'like', '%' . $file_name . '%' ];
            }

			$list = $file_model->getFilePageList($condition, $page_index, $list_rows, 'create_time desc');
			return $list;
		} else {
			$category_list = $file_model->getFileCategoryList([ 'nfc.site_id' => $this->siteId, 'nfc.type' => $file_type ]);
			$param['category_list'] = $category_list['data'];
			if ($file_type == "AUDIO") {
				return array( 'sitehome/File/audio_manage', $param );
			} else {
				return array( 'sitehome/File/file_manage', $param );
			}

		}
		
	}
	
	/**
	 * 文件上传组件
	 * @param unknown $type 上传方式    'common','many',
	 * @param unknown $file_type 上传文件类型
	 */
	public function fileUploadType($type, $file_type, $size)
	{
		$template = $this->getFileUploadTemplate($type, $file_type);
		if (!empty($template)) {
			return 'common/File/' . $template['template'];
		}
	}
	
	/**
	 * 获取对应模板
	 * @param unknown $type
	 * @param unknown $file_type
	 * @return Ambigous <multitype:, multitype:string >
	 */
	private function getFileUploadTemplate($type, $file_type)
	{
		$config = array(
			//单图上传
			array(
				'type' => 'common',
				'file_type' => 'IMAGE',
				'size' => '',
				'template' => 'image_common'
			),
			//多图上传
			array(
				'type' => 'multiple',
				'file_type' => 'IMAGE',
				'size' => '',
				'template' => 'image_multiple'
			),
			//音频上传
			array(
				'type' => 'common',
				'file_type' => 'AUDIO',
				'size' => '',
				'template' => 'audio_common'
			),
			//视频上传
			array(
				'type' => 'common',
				'file_type' => 'VIDEO',
				'size' => '',
				'template' => 'video_common'
			),
			//单图上传(前台)
			array(
				'type' => 'common',
				'file_type' => 'WEB_IMAGE',
				'size' => '',
				'template' => 'web_image_common'
			),
			//缩略图上传(前台)
			array(
				'type' => 'common',
				'file_type' => 'CROPPER_IMAGE',
				'size' => '',
				'template' => 'cropper_image_common'
			),
			//附件上传
			array(
				'type' => 'common',
				'file_type' => 'ATTACHMENT',
				'size' => '',
				'template' => 'attachment'
			),
		);
		$template = array();
		foreach ($config as $k => $v) {
			if ($v['type'] == $type && $v['file_type'] == $file_type) {
				$template = $v;
				break;
			}
		}
		return $template;
		
	}

    /**
     * 媒体库
     */
	public function media(){
	    $type = input("type", "IMAGE");
        $name = input("name", "");
        $count = input("count", 0);
        $file_model = new FileModel();
        $param = [
            "type" => $type,
            "name" => $name,
            "count" => $count
        ];
        $category_list = $file_model->getFileCategoryList([ 'nfc.site_id' => $this->siteId, 'nfc.type' => $type ]);
        $param['category_list'] = $category_list['data'];
        switch ($type){
            case "IMAGE":
                $view = $this->fetch("file/file_manage", $param, $this->replace);
                break;
            case "AUDIO":
                $view = $this->fetch("file/audio_manage", $param, $this->replace);
                break;
            case "VIDEO":
                $view = $this->fetch("file/video_manage", $param, $this->replace);
                break;
        }
        return $view;
    }
	
}