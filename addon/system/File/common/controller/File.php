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

namespace addon\system\File\common\controller;

use app\common\controller\BaseController;
use addon\system\File\common\model\FileUpload;

class File extends BaseController
{
	public $upload_path = __UPLOAD__;//公共上传文件
	public $upload_config = []; //上传配置
	
	/**
	 * 普通单图上传
	 */
	public function image()
	{
		if (IS_AJAX) {
			$thumb_type = input('thumb_type', "");
			$site_id = request()->siteid();
			$file_upload = new FileUpload($site_id);
			$res = $file_upload->image($thumb_type);
			return $res;
		}
	}
	
	/**
	 * 相册图片上传
	 * @return
	 */
	public function imageToAlbum()
	{
		$site_id = request()->siteid();
		$category_id = input('category_id', 1);
		$thumb_type = input('thumb_type', "");
		if (request()->isAjax()) {
		    $file_upload = new FileUpload($site_id);
			$res = $file_upload->imageToAlbum($category_id, $thumb_type);
			return $res;
		}
	}
	
	/**
	 * 音频
	 */
	public function audio()
	{
		
		if (IS_AJAX) {
			$site_id = request()->siteid();
			$category_id = input('category_id', 2);
			$file_upload = new FileUpload($site_id);
			$res = $file_upload->audio($category_id);
			return $res;
		}
	}
	
	/**
	 * 视频
	 */
	public function video()
	{
		
		if (IS_AJAX) {
			$site_id = request()->siteid();
			$category_id = input('category_id', 3);
			$file_upload = new FileUpload($site_id);
			$res = $file_upload->video($category_id);
			return $res;
		}
	}
	
	/**
	 * 网络图片提取
	 */
	public function fetchPubImg()
	{
		$url = input("fetch_image_path", "");//远程图片
		$site_id = request()->siteid();
		$thumb_type = input('thumb_type', "");
		$file_upload = new FileUpload($site_id);
		$res = $file_upload->fetchPubImg($url, $thumb_type);
		return $res;
		
	}
	
	/**
	 * 网络图片提取
	 */
	public function fetchPubImgToAlbum()
	{
		$url = input("fetch_image_path", "");//远程图片
		$site_id = request()->siteid();
		$thumb_type = input('thumb_type', "");
		$category_id = input('category_id', 1);
		$file_upload = new FileUpload($site_id);
		$res = $file_upload->fetchPubImgToAlbum($url, $category_id, $thumb_type);
		return $res;
		
	}
	
	/**
	 * 图片在线裁剪 保存
	 */
	public function cropper()
	{
		$base64_image_content = input('img');
		$site_id = request()->siteid();
		$file_upload = new FileUpload($site_id);
		$res = $file_upload->cropper($base64_image_content);
		return $res;
	}
	
	/**
	 * 附件上传
	 */
	public function enclosure()
	{
		$site_id = request()->siteid();
		if (IS_AJAX) {
		    $file_upload = new FileUpload($site_id);
			$res = $file_upload->enclosure();
			return $res;
		}
	}
}