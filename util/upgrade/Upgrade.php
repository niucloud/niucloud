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
namespace util\upgrade;

/**
 * 升级
 */
class Upgrade
{
    protected $error;
    protected $recoverFiles;
    
    /**
     * 备份
     * @param string $root_path     根路径（基础路径）
     * @param string $root_dir      根文件夹
     * @param string $update_path   备份到的路径
     */
    public function backup($root_path, $root_dir, $update_path){
        $file_array = $this->getFiles($root_dir, $root_path);
        try {
            foreach ($file_array as $file_path){
                $result = $this->backupFile($root_path, $file_path, $update_path);
                if(!$result){
                    $this->error = '文件备份失败!';
                    break;
                }
            }
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        };
        return $this->error ? false : true;
    }
    
    /**
     * 备份具体的文件
     * @param string $file_path     需要备份文件的路径
     * @param string $backup_path   备份到哪里
     */
    protected function backupFile($root_path, $file_path, $backup_path){
        try {
            $file_path = str_replace('\\', '/', $file_path);
            $file_str = explode('/', $file_path);
            $from_path = $root_path . $file_path;
            $to_path = '';
            if(count($file_str) > 1){
                for ($i = 0; $i < count($file_str); $i++){
                    $middle_path = $file_str[$i];
                    if($middle_path == end($file_str)){
                        $to_path= $backup_path . $middle_path;
                    }else{
                        $backup_path = $backup_path . $middle_path . '/';
                        if (! is_dir($backup_path)) {
                            @mkdir($backup_path, 0777, true);
                        }
                    }
                }
            }else{
                $to_path = $backup_path . $file_path;
            }
            if (file_exists($from_path)){
                @copy($from_path, $to_path);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * 覆盖
     * @param string $root_path
     * @param string $from_dir
     * @param string $to_dir
     */
    public function cover($root_path, $from_dir, $to_dir){
        $file_array = $this->getFiles($to_dir, $root_path . $from_dir);
        try {
            foreach ($file_array as $file_path){
                $from_path = $root_path . $from_dir . $file_path;
                $to_path = $root_path . $file_path;
                if($file_path){
                    if (file_exists($from_path)){
                        if(!file_exists($to_path)){
                            $file_str = explode('/', $file_path);
                            if(count($file_str) > 1){
                                $backup_path = $root_path;
                                for ($i = 0; $i < count($file_str); $i++){
                                    $middle_path = $file_str[$i];
                                    if($middle_path != end($file_str)){
                                        $backup_path = $backup_path  . $middle_path . '/';
                                        if (! is_dir($backup_path)) {
                                            @mkdir($backup_path, 0777, true);
                                        }
                                    }
                                }
                            }
                        }
                        @chmod($from_path, 0777);
                        @copy($from_path, $to_path);
                        @chmod($to_path, 0777);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }
        return $this->error ? false : true;
    }
    
    /**
     * 还原文件
     * @param string $root_path
     * @param string $from_dir
     * @param string $to_dir
     * @return boolean
     */
    public function recover($root_path, $from_dir, $to_dir){
        try {
            //复制到新的地方
            $this->backup($root_path . $from_dir, $to_dir, $root_path . $to_dir.'-copy/');
            $this->deldir($root_path . $to_dir . '/');
            $this->cover($root_path, $to_dir . '-copy/', $to_dir);
            $this->deldir($root_path . $to_dir . '-copy' . '/');
            @rmdir($root_path . $to_dir . '-copy' . '/');
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }
        return $this->error ? false : true;
    }
    
    /**
     * 获取某文件夹下所有文件
     * @param string $dir 文件夹路径
     * @param string $base_path 基础路径
     * @return array
     */
    public function getFiles($dir, $base_path = '') {
        $file = [];
        if (file_exists($base_path.$dir)){
            if(substr($dir, -1) != "/"){
                $dir .= "/";
            }
            $real_dir = $base_path.$dir;
            $dh = opendir($real_dir);
            while($files = readdir($dh)){
                if (($files != ".") && ($files != "..")){
                    if (is_dir($real_dir.$files)){
                        $file = array_merge($file, $this->getFiles($dir.$files, $base_path));
                    }else{
                        $file[] = $dir.$files;
                    }
                }
            }
            closedir($dh);
        }
        $file[] = '';
        return $file;
    }
    
    protected function deldir($path){
        //如果是目录则继续
        if(is_dir($path)){
            //扫描一个文件夹内的所有文件夹和文件并返回数组
            $p = scandir($path);
            foreach($p as $val){
                //排除目录中的.和..
                if($val != "." && $val != ".."){
                    //如果是目录则递归子目录，继续操作
                    if(is_dir($path.$val)){
                        //子目录中操作删除文件夹和文件
                        $this->deldir($path.$val.'/');
                        //目录清空后删除空文件夹
                        @rmdir($path.$val.'/');
                    }else{
                        //如果是文件直接删除
                        unlink($path.$val);
                    }
                }
            }
        }
        return true;
    }
    
    
    /**
     * 获取错误信息
     * @return string
     */
    public function getError(){
        return $this->error;
    }
}