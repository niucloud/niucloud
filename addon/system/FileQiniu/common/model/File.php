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

namespace addon\system\FileQiniu\common\model;

require_once 'addon/system/FileQiniu/common/sdk/autoload.php';
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

/**
 * 文件管理
 * @author Administrator
 *
 */
class File
{
    /**
     * 开放空间存储
     */
    public function fileStore($upload_path, $file_name, $config)
    {
        $accessKey = $config["access_key"];
        $secretKey = $config["secret_key"];
        $bucket = $config["bucket"];//上传空间
        $domain = $config["domain"];//上传空间
        //构建鉴权对象
        $auth = new Auth($accessKey, $secretKey);
        
        $token = $auth->uploadToken($bucket);
        // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传
        
        list($ret, $err) = $uploadMgr->putFile($token, $file_name, $upload_path . "/" . $file_name);
        if ($err !== null) {
            return error('', "UPLOAD_QINIU_CONFIG_ERROR");
        } else {
            //返回图片的完整URL
            $data = array( "path" => $domain . "/" . $file_name, "domain" => $domain, "bucket" => $bucket );
            return success($data);
            
        }
    }
}
