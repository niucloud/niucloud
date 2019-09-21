<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/15
 * Time: 16:06
 */

namespace addon\system\File\api\controller;

use addon\system\File\common\model\FileUpload;
use app\common\controller\BaseApi;

class File extends BaseApi
{
	
	/**
	 * 普通单图上传
	 * @param array $param
	 * @return array
	 */
	public function imageUpload($param = [])
	{
        $thumb_type = $param['thumb_type'];
        $site_id = $param["site_id"];
        $file_upload = new FileUpload($site_id);
        $res = $file_upload->image($thumb_type);
        return $res;
	}
    /**
     * 网络图片提取
     */
    public function fetchPubImg($param = [])
    {
        $url = $param["fetch_image_path"];//远程图片
        $site_id = $param["site_id"];
        $thumb_type = $param["thumb_type"];
        $file_upload = new FileUpload($site_id);
        $res = $file_upload->fetchPubImg($url, $thumb_type);
        return $res;

    }


    /**
     * 图片在线裁剪 保存
     */
    public function cropper($param = [])
    {
        $base64_image_content = $param["img"];
        $site_id = $param["site_id"];
        $file_upload = new FileUpload($site_id);
        $res = $file_upload->cropper($base64_image_content);
        return $res;
    }


    /**
     * 附件上传
     */
    public function enclosure($param = [])
    {
        $site_id = $param["siteid"];
        $file_upload = new FileUpload($site_id);
        $res = $file_upload->enclosure();
        return $res;

    }
	


}