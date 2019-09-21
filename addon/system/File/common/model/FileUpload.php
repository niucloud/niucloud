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

use util\Upload;
use addon\system\File\common\model\File as FileModel;

class FileUpload
{
	public $upload_path = __UPLOAD__;//公共上传文件
	public $upload_config = []; //上传配置
	public $site_id;
	public function __construct($site_id)
	{
	    $file_model = new FileModel();
	    $this->site_id = $site_id;
	    $info = $file_model->getFileUploadConfig($site_id);
	    $this->upload_config = $info["data"]["value"];
	}
	/************************************************************上传开始*********************************************/
	/**
	 * 单图上传
	 * @param number $site_id
	 * @param string $thumb_type 生成缩略图类型
	 */
	public function image($thumb_type = '')
	{
		$upload = new Upload($_FILES["file"]["tmp_name"]);//实例化上传类
		$rule = [ "type" => "image/png,image/jpeg,image/gif,image/bmp", "ext" => "gif,jpg,jpeg,bmp,png" ];//上传文件验证规则
		$old_name = $upload->getFileName($_FILES["file"]["name"]);//文件原名
		$file_name = $this->site_id . "/images/".date("Ymd"). "/". $upload->createNewFileName();
		$extend_name = $upload->getFileExt($_FILES["file"]["name"]);
		$upload_data = $upload->setValidate($rule)->setUploadInfo($_FILES["file"])->move($this->upload_path, $file_name . "." . $extend_name);
		if ($upload_data !== false) {
			$filesize = $upload->getFileSize($upload_data);//获取文件大小
			$filetype = $upload->getFileType($upload_data);//获取文件类型
			$size_data = $upload->getImageInfo($upload_data);//获取图片信息
			$thumb_res = $this->thumbTypeCreate($this->upload_path, $file_name, $extend_name, $thumb_type);//生成缩略图
			
			//判断图片处理成功与否（设计云存储）
			if ($thumb_res["code"] !== 0) {
				return $thumb_res;
			} else {
				$thumb_data = $thumb_res["data"];
			}
			$file_res = $this->fileStore($this->upload_path, $file_name . "." . $extend_name);
			if ($file_res["code"] != 0) {
				return $file_res;
			}
			
			$data = array(
				"path" => $file_res["data"]["path"],//图片云存储
				"size" => $filesize,
				"file_name" => $old_name,
				"file_ext" => $extend_name,
				"pic_spec" => $size_data["0"] . "*" . $size_data["1"],
				"big_pic_path" => $thumb_data["big"]["thumb_name"],
				"big_pic_spec" => $thumb_data["big"]["width"] . "*" . $thumb_data["big"]["height"],
				"mid_pic_path" => $thumb_data["mid"]["thumb_name"],
				"mid_pic_spec" => $thumb_data["mid"]["width"] . "*" . $thumb_data["mid"]["height"],
				"small_pic_path" => $thumb_data["small"]["thumb_name"],
				"small_pic_spec" => $thumb_data["small"]["width"] . "*" . $thumb_data["small"]["height"],
			);
			return success($data);
		} else {
			return error('');
		}
	}
	
	/**
	 * 相册图片上传
	 * @param number $site_id
	 * @param number $category_id
	 * @param string $thumb_type
	 */
	public function imageToAlbum($category_id = 1, $thumb_type = '')
	{
		$upload = new Upload($_FILES["file"]["tmp_name"]);//实例化上传类
		$rule = [ "type" => "image/png,image/jpeg,image/gif,image/bmp", "ext" => "gif,jpg,jpeg,bmp,png" ];//上传文件验证规则
		$old_name = $upload->getFileName($_FILES["file"]["name"]);//文件原名
		$file_name = $this->site_id . "/images/".date("Ymd"). "/" . $upload->createNewFileName();
		$extend_name = $upload->getFileExt($_FILES["file"]["name"]);
		$upload_data = $upload->setValidate($rule)->setUploadInfo($_FILES["file"])->move($this->upload_path, $file_name . "." . $extend_name);
		if ($upload_data !== false) {
			$filesize = $upload->getFileSize($upload_data);//获取文件大小
			$filetype = $upload->getFileType($upload_data);//获取文件类型
			$size_data = $upload->getImageInfo($upload_data);//获取图片信息
			$thumb_res = $this->thumbTypeCreate($this->upload_path, $file_name, $extend_name, $thumb_type);//生成缩略图
			//判断图片处理成功与否（设计云存储）
			if ($thumb_res["code"] !== 0) {
				return $thumb_res;
			} else {
				$thumb_data = $thumb_res["data"];
			}
			$file_res = $this->fileStore($this->upload_path, $file_name . "." . $extend_name);
			if ($file_res["code"] != 0) {
				return $file_res;
			}
			$data = array(
				'site_id' => $this->site_id,
				'type' => 'IMAGE',
				"path" => $file_res['data']['path'],
				"size" => $filesize,
				'category_id' => $category_id,
				"file_name" => $old_name,
				"file_ext" => $extend_name,
				"pic_spec" => $size_data["0"] . "*" . $size_data["1"],
				"big_pic_path" => $thumb_data["big"]["thumb_name"],
				"big_pic_spec" => $thumb_data["big"]["width"] . "*" . $thumb_data["big"]["height"],
				"mid_pic_path" => $thumb_data["mid"]["thumb_name"],
				"mid_pic_spec" => $thumb_data["mid"]["width"] . "*" . $thumb_data["mid"]["height"],
				"small_pic_path" => $thumb_data["small"]["thumb_name"],
				"small_pic_spec" => $thumb_data["small"]["width"] . "*" . $thumb_data["small"]["height"],
				"create_time" => time()
			);
			$file_model = new File();
			$res = $file_model->addFile($data);
			if ($res['code'] == 0) {
				return success($data);
			} else {
				return error($res);
			}
		} else {
			return error($upload->getError(), 'UNKNOW_ERROR');
		}
	}
	
	/**
	 * 音频上传
	 * @param number $site_id
	 * @param number $category_id
	 */
	public function audio($category_id = 2)
	{
		$upload = new Upload($_FILES["file"]["tmp_name"]);//实例化上传类
		$rule = [];//上传文件验证规则
		$old_name = $upload->getFileName($_FILES["file"]["name"]);//文件原名
		$file_name = $this->site_id . "/audio/" . $upload->createNewFileName();
		$extend_name = $upload->getFileExt($_FILES["file"]["name"]);
		$upload_data = $upload->setValidate($rule)->setUploadInfo($_FILES["file"])->move($this->upload_path, $file_name . "." . $extend_name);
		if ($upload_data !== false) {
			$filesize = $upload->getFileSize($upload_data);//获取文件大小
			
			//判断图片处理成功与否（涉及云存储）
			$file_res = $this->fileStore($this->upload_path, $file_name . "." . $extend_name);
			if ($file_res["code"] != 0) {
				return $file_res;
			}
			
			$data = array(
				'site_id' => $this->site_id,
				'type' => 'AUDIO',
				'path' => $file_res["data"]["path"],
				'category_id' => $category_id,
				'file_name' => $old_name,
				'file_ext' => $extend_name,
				'size' => $filesize,
				'create_time' => time()
			);
			$file_model = new FileModel();
			$res = $file_model->addFile($data);
			return $res;
		} else {
			return error();
		}
	}
	
	/**
	 * 视频上传
	 * @param number $site_id
	 * @param number $category_id
	 * @return Ambigous <unknown, multitype:string mixed >|unknown
	 */
	public function video($category_id = 3)
	{
		
		$category_id = input('category_id', 3);
		$upload = new Upload($_FILES["file"]["tmp_name"]);//实例化上传类
		$rule = [];//上传文件验证规则
		$file_name = $this->site_id . "/video/" . $upload->createNewFileName();
		$extend_name = $upload->getFileExt($_FILES["file"]["name"]);
		$old_name = $upload->getFileName($_FILES["file"]["name"]);//文件原名
		$upload_data = $upload->setValidate($rule)->setUploadInfo($_FILES["file"])->move($this->upload_path, $file_name . "." . $extend_name);
		if ($upload_data !== false) {
			$filesize = $upload->getFileSize($upload_data);//获取文件大小
			
			//判断图片处理成功与否（设计云存储）
			$file_res = $this->fileStore($this->upload_path, $file_name . "." . $extend_name);
			if ($file_res["code"] != 0) {
				return $file_res;
			}
			$data = array(
				'site_id' => $this->site_id,
				'type' => 'VIDEO',
				'path' => $file_res["data"]["path"],
				'category_id' => $category_id,
				'file_name' => $old_name,
				'file_ext' => $extend_name,
				'size' => $filesize,
				'create_time' => time()
			);
			$file_model = new FileModel();
			$res = $file_model->addFile($data);
			return $res;
		} else {
			return error();
		}
	}
	
	/**
	 * 远程图片提取
	 * @param unknown $site_id
	 * @param unknown $url 图片路径
	 * @param unknown $thumb_type 压缩形式
	 */
	public function fetchPubImg($url, $thumb_type)
	{
		try {
			$mimes = array(
				'image/bmp' => 'bmp',
				'image/gif' => 'gif',
				'image/jpeg' => 'jpg',
				'image/png' => 'png',
				'image/x-icon' => 'ico'
			);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			$file = curl_exec($ch);
			curl_close($ch);
			$filename = pathinfo($url, PATHINFO_BASENAME);
			
			$image_info = getimagesize($url);
			$suffix = false;
			if ($mimes == $image_info['mime']) {
				$suffix = explode('/', $mimes)[1];
			}
			$extend_name = $mimes[ $suffix ];
			
			$headers = get_headers($url, 1);
			$type = $headers['Content-Type'];
			
			$extend_name = $mimes[ $type ];
			
			$upload = new Upload($url);//实例化上传类
			$old_name = $upload->getFileName($_FILES["file"]["name"]);//文件原名
			$file_name = $this->site_id . "/images/".date("Ymd"). "/" . $upload->createNewFileName();
			//        $extend_name = $upload->getFileExt($_FILES["file"]["name"]);
			
			$resource = fopen($this->upload_path . "/" . $file_name . "." . $extend_name, 'a');
			fwrite($resource, $file);
			fclose($resource);
			//        if($upload_data !== false){
			$filesize = $image_info["bits"];//获取文件大小
			$filetype = $image_info["mime"];//获取文件类型
			$size_data = $image_info;
			$thumb_res = $this->thumbTypeCreate($this->upload_path, $file_name, $extend_name, $thumb_type);//生成缩略图
			
			//判断图片处理成功与否（设计云存储）
			if ($thumb_res["code"] !== 0) {
				return $thumb_res;
			} else {
				$thumb_data = $thumb_res["data"];
			}
			$file_res = $this->fileStore($this->upload_path, $file_name . "." . $extend_name);
			if ($file_res["code"] != 0) {
				return $file_res;
			}
			
			$data = array(
				"path" => $file_res["data"]["path"],
				"size" => $filesize,
				"file_name" => $old_name,
				"file_ext" => $extend_name,
				"pic_spec" => $size_data["0"] . "*" . $size_data["1"],
				"big_pic_path" => $thumb_data["big"]["thumb_name"],
				"big_pic_spec" => $thumb_data["big"]["width"] . "*" . $thumb_data["big"]["height"],
				"mid_pic_path" => $thumb_data["mid"]["thumb_name"],
				"mid_pic_spec" => $thumb_data["mid"]["width"] . "*" . $thumb_data["mid"]["height"],
				"small_pic_path" => $thumb_data["small"]["thumb_name"],
				"small_pic_spec" => $thumb_data["small"]["width"] . "*" . $thumb_data["small"]["height"]
			);
			return success($data);
		} catch (\Exception $e) {
			return error(0, 'UNKNOW_ERROR');
		}
	}
	
	/**
	 * 远程相册图片提取（存储相册）
	 * @param unknown $site_id
	 * @param unknown $url
	 * @param unknown $category_id
	 * @param unknown $thumb_type
	 * @return \addon\system\File\common\model\Ambigous|Ambigous <unknown, multitype:string mixed >|multitype:string mixed
	 */
	public function fetchPubImgToAlbum($url, $category_id, $thumb_type)
	{
		try {
			$mimes = array(
				'image/bmp' => 'bmp',
				'image/gif' => 'gif',
				'image/jpeg' => 'jpg',
				'image/png' => 'png',
				'image/x-icon' => 'ico'
			);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			$file = curl_exec($ch);
			curl_close($ch);
			$filename = pathinfo($url, PATHINFO_BASENAME);
			
			$image_info = getimagesize($url);
			$suffix = false;
			if ($mimes == $image_info['mime']) {
				$suffix = explode('/', $mimes)[1];
			}
			$extend_name = $mimes[ $suffix ];
			
			$headers = get_headers($url, 1);
			$type = $headers['Content-Type'];
			
			$extend_name = $mimes[ $type ];
			
			$upload = new Upload($url);//实例化上传类
			$old_name = $upload->getFileName($_FILES["file"]["name"]);//文件原名
			$file_name = $this->site_id . "/images/".date("Ymd"). "/" . $upload->createNewFileName();
			//        $extend_name = $upload->getFileExt($_FILES["file"]["name"]);
			
			$resource = fopen($this->upload_path . "/" . $file_name . "." . $extend_name, 'a');
			fwrite($resource, $file);
			fclose($resource);
			//        if($upload_data !== false){
			$filesize = $image_info["bits"];//获取文件大小
			$filetype = $image_info["mime"];//获取文件类型
			$size_data = $image_info;
			$thumb_res = $this->thumbTypeCreate($this->upload_path, $file_name, $extend_name, $thumb_type);//生成缩略图
			
			//判断图片处理成功与否（设计云存储）
			if ($thumb_res["code"] !== 0) {
				return $thumb_res;
			} else {
				$thumb_data = $thumb_res["data"];
			}
			$file_res = $this->fileStore($this->upload_path, $file_name . "." . $extend_name);
			if ($file_res["code"] != 0) {
				return $file_res;
			}
			$data = array(
				'site_id' => $this->site_id,
				'type' => 'IMAGE',
				"path" => $file_res["data"]["path"],
				"size" => $filesize,
				'category_id' => $category_id,
				"file_name" => $old_name,
				"file_ext" => $extend_name,
				"pic_spec" => $size_data["0"] . "*" . $size_data["1"],
				"big_pic_path" => $thumb_data["big"]["thumb_name"],
				"big_pic_spec" => $thumb_data["big"]["width"] . "*" . $thumb_data["big"]["height"],
				"mid_pic_path" => $thumb_data["mid"]["thumb_name"],
				"mid_pic_spec" => $thumb_data["mid"]["width"] . "*" . $thumb_data["mid"]["height"],
				"small_pic_path" => $thumb_data["small"]["thumb_name"],
				"small_pic_spec" => $thumb_data["small"]["width"] . "*" . $thumb_data["small"]["height"],
				"create_time" => time()
			);
			$file_model = new File();
			$res = $file_model->addFile($data);
			if ($res['code'] == 0) {
				return success($data);
			} else {
				return error($res);
			}
		} catch (\Exception $e) {
			return error(0, 'UNKNOW_ERROR');
		}
	}
	
	/**
	 * 图片在线裁剪 保存
	 */
	public function cropper($base64_image_content)
	{
		header('Content-type:text/html;charset=utf-8');
		$upload = new Upload('');//实例化上传类
		$old_name = '';//文件原名
		//将base64编码转换为图片保存
		if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
			$type = $result[2];
			$file_name = $upload->createNewFileName();
			//        $extend_name = $upload->getFileExt($_FILES["file"]["name"]);
			$file_path = $this->upload_path . "/" . $this->site_id . "/images/".date("Ymd"). "/";//上传路径
			
			if (!file_exists($file_path)) {
				//检查是否有该文件夹，如果没有就创建，并给予最高权限
				mkdir($file_path, 0700, true);
			}
			$img = $file_name . "." . $type;
			$new_file = $file_path . $img;
			//将图片保存到指定的位置
			if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))) {
				
				//判断图片处理成功与否（设计云存储）
			    $file_res = $this->fileStore($this->upload_path, $this->site_id . "/images/".date("Ymd"). "/" . $img);
				if ($file_res["code"] != 0) {
					return $file_res;
				}
				return success($file_res['data']['path']);
			} else {
				return error(0, 'UNKNOW_ERROR');
			}
		} else {
			return error(0, 'UNKNOW_ERROR');
		}
	}
	
	/**
	 * 附件上传
	 */
	public function enclosure()
	{
		$upload = new Upload($_FILES["file"]["tmp_name"]);//实例化上传类
		$old_name = $upload->getFileName($_FILES["file"]["name"]);//文件原名
		$file_name = $this->site_id . "/enclosure/" . $upload->createNewFileName();
		$extend_name = $upload->getFileExt($_FILES["file"]["name"]);
		$upload_path = __UPLOAD__;//上传路径
		$upload_data = $upload->setUploadInfo($_FILES["file"])->move($upload_path, $file_name . "." . $extend_name);
		
		if ($upload_data !== false) {
			$filesize = $upload->getFileSize($upload_data);//获取文件大小
			
			//判断图片处理成功与否（设计云存储）
			$file_res = $this->fileStore(__UPLOAD__, $file_name . "." . $extend_name);
			if ($file_res["code"] != 0) {
				return $file_res;
			}
			
			$data = array(
				"path" => $file_res['data']['path'],
				"size" => $filesize,
				"file_name" => $old_name,
				"file_ext" => $extend_name,
			);
			return success($data);
		} else {
			return error('');
		}
	}
	/************************************************************上传结束*********************************************/
	/************************************************************上传功能组件******************************************/

	
	/**
	 * 缩略图生成
	 * @param unknown $file_name
	 * @param unknown $extend_name
	 * @param unknown $thumb_type
	 * @return Ambigous <string, multitype:multitype:string  >
	 */
	private function thumbTypeCreate($file_path, $file_name, $extend_name, $thumb_type = "")
	{
		$thumb_type_array = array(
			"big" => array(
				"size" => "BIG",
				"width" => $this->upload_config["thumb"]["thumb_spec"]["big_width"],
				"height" => $this->upload_config["thumb"]["thumb_spec"]["big_width"],
				"thumb_name" => ""
			),
			"mid" => array(
				"size" => "MID",
				"width" => $this->upload_config["thumb"]["thumb_spec"]["mid_width"],
				"height" => $this->upload_config["thumb"]["thumb_spec"]["mid_width"],
				"thumb_name" => ""
			),
			"small" => array(
				"size" => "SMALL",
				"width" => $this->upload_config["thumb"]["thumb_spec"]["small_width"],
				"height" => $this->upload_config["thumb"]["thumb_spec"]["small_width"],
				"thumb_name" => ""
			)
		);
		foreach ($thumb_type_array as $k => $v) {
			if (!empty($thumb_type) && strpos($thumb_type, $k) !== false) {
				$result = $this->thumbCreate($file_path, $file_name . "." . $extend_name, $file_name . "_" . $v["size"] . "." . $extend_name, $v["width"], $v["height"]);
				//返回生成的缩略图路径
				if ($result["code"] == 0) {
					$thumb_type_array[ $k ]["thumb_name"] = $result["data"]["path"];
				} else {
					return $result;
				}
			}
		}
		return success($thumb_type_array);
	}
	
	/**
	 * 缩略图
	 * @param unknown $file_name
	 * @param unknown $new_path
	 * @param unknown $width
	 * @param unknown $height
	 * @return multitype:boolean unknown |multitype:boolean
	 */
	public function thumbCreate($file_path, $file_name, $thumb_name, $width, $height)
	{
		try {
			$image = \think\Image::open($file_path . "/" . $file_name);
			$image->thumb($width, $height, $this->upload_config["thumb"]["thumb_type"]);
			
			$image->save($file_path . "/" . $thumb_name, null, 80);
			unset($image);
			//是否添加水印
			if ($this->upload_config["watermark"]["is_open_watermark"] == 1) {
				$this->imageWater($file_path . "/" . $thumb_name, $file_path . "/" . $thumb_name);
			}
			$res = $this->fileStore($file_path, $thumb_name);
			return $res;
		} catch (\Exception $e) {
			return error(0, 'UNKNOW_ERROR');
		}
	}
	
	/**
	 * 添加水印
	 */
	public function imageWater($file_name, $warer_name)
	{
		try {
			$image = \think\Image::open($file_name);
			$locate = $this->upload_config["watermark"]["watermark_position"];//水印位置
			$alpha = $this->upload_config["watermark"]["transparence"];//水印透明度
			if ($this->upload_config["watermark"]["watermark_type"] == 1) {
				$watermark_image = __UPLOAD__ . "/" . $this->upload_config["watermark"]["watermark_image"];
				$image->water($watermark_image, $locate, $alpha);
			} else {
				$text = $this->upload_config["watermark"]["watermark_text"];   //添加的文字
				$font = realpath("public/static/font/Microsoft.ttf");   //字体路径
				$size = $this->upload_config["watermark"]["watermark_fontsize"];   //字号
				$color = $this->upload_config["watermark"]["watermark_color"];  //文字颜色
				$offset = $this->upload_config["watermark"]["watermark_offset"]; //文字相对当前位置的偏移量
				$angle = $this->upload_config["watermark"]["watermark_angle"];;  //文字倾斜角度
				$image->text($text, $font, $size, $color, $locate, $offset, $angle);
			}
			$image->save($warer_name, null, 80);
			unset($image);
			return success($warer_name);
		} catch (\Exception $e) {
			return error(0, 'UNKNOW_ERROR');
		}
	}
	
	
	/**删除文件
	 * @param $file_name
	 */
	private function deleteFile($file_name)
	{
		$res = @unlink($file_name);
		if ($res) {
			return success();
		} else {
			return error();
		}
		
	}
	
	/**
	 * 云存储调用
	 */
	public function fileStore($file_path, $file_name)
	{
	    //给原图添加水印
        $this->imageWater($file_path . "/" . $file_name, $file_path . "/" . $file_name);
		$res = hook("fileStore", [ "file_name" => $file_name, "upload_path" => $file_path ]);
		if ($res[0] != null) {
			$this->deleteFile($file_path . "/" . $file_name);
			return $res[0];
		}
		return success([ 'path' => $file_name ]);
	}
	/************************************************************上传功能组件******************************************/
}