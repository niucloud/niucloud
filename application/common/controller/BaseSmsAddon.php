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
namespace app\common\controller;

/**
 * 短信系统插件
 */
abstract class BaseSmsAddon extends BaseAddon
{
    
    public function __construct(){
        parent::__construct();
    }
    
    protected function sms($param = []){
        //判断当前使用的是哪个短信
//         dump('this is a base!!!');
        if($param['name'] == $this->info['name']){
            return true;
        }
        return false;
    }
    
    /**
     * 获取配置信息
     * @param array $param
     * Returns:['code' => 0|1, 'message' => '', 'data' => []]  
     */
    protected function getSmsConfig($param = []){
        return [
            'info' => $this->info,
            'site_config' => ''
        ];
    }
    
    /**
     * 安装
     * Returns:['code' => 0|1, 'message' => '', 'data' => []]  
     */
    public function install(){
        //设置默认值
        return true;
    }
    
   /**
     * 卸载
     * Returns:['code' => 0|1, 'message' => '', 'data' => []]  
     */
    public function uninstall(){
        //删除掉，设置别的为默认
        return true;
    }
}
