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

class Config extends BaseSiteHome
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
	 *上传设置
	 */
	public function config()
	{
		$file_model = new FileModel();
		if (IS_AJAX) {
			$thumb_type = input('thumb_type', 3);//缩略图裁切类型
			$thumb_spec = input('thumb_spec', "{}");//缩略图规格
			$is_open_watermark = input('is_open_watermark', 0);//是否开启水印
			$watermark_image = input('watermark_image', '');//水印嵌入图
			$watermark_position = input('watermark_position', '');//水印图位置
			$watermark_type = input('watermark_type', '');//水印图位置
			
			$watermark_color = input('watermark_color', '');//水印文字颜色
			$watermark_fontsize = input('watermark_fontsize', '');//水印文字大小
			$watermark_text = input('watermark_text', '');//水印文字内容
			$watermark_offset = input('watermark_offset', '');//水印文字迁移量
			$watermark_angle = input('watermark_angle', '');//水印文字倾斜角度
			
			$transparence = input('transparence', 0);//水印透明度
			
			$big_width = input('big_width', 0);//图宽
			$big_height = input('big_height', 0);//图高
			$mid_width = input('mid_width', 0);//图宽
			$mid_height = input('mid_height', 0);//图高
			$small_width = input('small_width', 0);//图宽
			$small_height = input('small_height', 0);//图高
			$thumb_spec = array(
				"big_width" => $big_width,
				"big_height" => $big_height,
				"mid_width" => $mid_width,
				"mid_height" => $mid_height,
				"small_width" => $small_width,
				"small_height" => $small_height,
			);
			$json_array = array();
			$json_array["thumb"] = [ "thumb_type" => $thumb_type, "thumb_spec" => $thumb_spec ];
			$json_array["watermark"] = [ "watermark_offset" => $watermark_offset, "watermark_angle" => $watermark_angle, "watermark_color" => $watermark_color, "watermark_fontsize" => $watermark_fontsize, "watermark_text" => $watermark_text, "is_open_watermark" => $is_open_watermark, "watermark_image" => $watermark_image, "transparence" => $transparence, "watermark_position" => $watermark_position, "watermark_type" => $watermark_type ];
			$json_value = json_encode($json_array);
			$data = array(
				"site_id" => $this->siteId,
				"value" => $json_value
			);
			$res = $file_model->setFileUploadConfig($data, $this->siteId);
			return $res;
		} else {
			$info = $file_model->getFileUploadConfig($this->siteId);
			$this->assign("info", $info["data"]["value"]);
			return $this->fetch('config/config');
			
		}
		
	}
	
}