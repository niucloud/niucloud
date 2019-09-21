<?php
namespace util\addon;
use util\api\WebClient;
use util\api\HttpClient;
use think\Exception;
use ZipArchive;
use app\common\model\Addon as AddonModel;

class AddonService
{
    protected $http;
    protected $tmpFile;
    protected $type;
    protected $data = [];
    public function __construct($app_key, $app_secret){
        $this->http = new WebClient($app_key, $app_secret);
    }
    
    /**
     * 远程下载插件
     */
    public function download($name, $extend = []){
        $addonTmpDir = RUNTIME_PATH . 'addons' . DS;
        if (!is_dir($addonTmpDir))
        {
            @mkdir($addonTmpDir, 0755, true);
        }
        $ret = $this->http->post('Addon.download', array_merge(['name' => $name], $extend));
        if ($ret['code'] == 0) {
            $list = $ret['data'];
            foreach ($list as $k => $v){
                if($v['url']){
                    $res = HttpClient::post($v['url'], []);
                    if(!$res){
                        //下载返回错误，抛出异常
                        throw new AddonException($res['message'], $res['code'], $res['data']);
                    }
                    $tmpFile = $addonTmpDir.$v['zip_name'];
                    if ($write = fopen($tmpFile, 'w'))
                    {
                        fwrite($write, $res);
                        fclose($write);
                    }else{
                        throw new Exception("没有权限写入临时文件");
                    }
                }
            }
            return $list;
        }else{
            throw new Exception("无法下载远程文件");
        }
    }
    
    public function unzip($zip_name, $type){
        $file = RUNTIME_PATH . 'addons' . DS . $zip_name;
        if($type == 'ADDON_APP'){
            $dir = ADDON_APP_PATH;
        }else{
            $dir = ADDON_MODULE_PATH;
        }
        if (class_exists('ZipArchive'))
        {
            $zip = new ZipArchive;
            if ($zip->open($file) !== TRUE)
            {
                throw new Exception('Unable to open the zip file');
            }
            if (!$zip->extractTo($dir))
            {
                $zip->close();
                throw new Exception('Unable to extract the file');
            }
            $zip->close();
            return $file;
        }
        throw new Exception("无法执行解压操作，请确保ZipArchive安装正确");
    }
    
    public function install($name, $extend = []){
        if (!$name || (is_dir(ADDON_PATH . $name)))
        {
            throw new Exception('Addon already exists');
        }
        //下载插件
        $list = $this->download($name, $extend);
        //解压
        foreach ($list as $k => $v){
            $file = $this->unzip($v['zip_name'], $v['type']);
            //删除下载的压缩包
            @unlink($file);
        }
    }
    
    public function uninstall($name){
        $model = new AddonModel();
        $res = $model->uninstall($name);
        deldir(ADDON_APP_PATH . $name . '/');
        return $res;
    }
    
    public function upgrade($name, $extend = []){
        //下载插件
        $list = $this->download($name, $extend);
        //解压
        foreach ($list as $k => $v){
            $file = $this->unzipUpdate($v['zip_name'], $v['type'], $name, $extend['addon_version']);
            //删除下载的压缩包
            @unlink($file);
        }
    }
    
    public function unzipUpdate($zip_name, $type, $addon_name, $version){
        $file = RUNTIME_PATH . 'addons' . DS . $zip_name;
        if($type == 'ADDON_APP'){
            $dir = ADDON_APP_PATH;
        }else{
            $dir = ADDON_MODULE_PATH;
        }
        $dir = $dir . $addon_name . '/update/' . $version . '/';
        if (!is_dir($dir))
        {
            @mkdir($dir, 0755, true);
        }
        if (class_exists('ZipArchive'))
        {
            $zip = new ZipArchive;
            if ($zip->open($file) !== TRUE)
            {
                throw new Exception('Unable to open the zip file');
            }
            if (!$zip->extractTo($dir))
            {
                $zip->close();
                throw new Exception('Unable to extract the file');
            }
            $zip->close();
            return $file;
        }
        throw new Exception("无法执行解压操作，请确保ZipArchive安装正确");
    }
    
}

?>