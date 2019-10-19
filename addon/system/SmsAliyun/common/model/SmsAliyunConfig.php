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

namespace addon\system\SmsAliyun\common\model;


use app\common\model\Site;

/**
 * 阿里云短信
 * @author lzw
 */
class SmsAliyunConfig
{
    public $site_model;
    public function __construct()
    {
        $this->site_model = new Site();
    }
    /**
     * 设置阿里云短信配置
     * @param number $site_id
     * @param string $value
     * @param number $status
     */
    public function setSmsAliyunConfig($data){
        //如果开启当前短信,则关闭其他短信
        if($data["status"] == 1){
            hook("closeSms", ["site_id" => $data['site_id']]);
        }
        $data["name"] = 'NC_SMS_ALIYUN_CONFIG';
        $condition = array(
            "name" => 'NC_SMS_ALIYUN_CONFIG'
        );
        $res = $this->site_model->setSiteConfig($data, $condition);
        return $res;
     }
     
    /**
     * 获取该站点阿里云短信配置
     * @param number $site_id
     */
    public function getSmsAliyunConfig($site_id){
        $config = $this->site_model->getSiteConfigInfo(["site_id" => $site_id, "name" => "NC_SMS_ALIYUN_CONFIG"]);
        $value = [];
        if(!empty($config["data"]["value"])){
            $value = json_decode($config["data"]["value"], true);
        }
        $config["data"]["value"] = $value;
        return $config;
    }
    
    /**
     * 删除该站点阿里云短信配置
     * @param number $site_id
     * Returns:['code' => 0|1, 'message' => '', 'data' => []]
     */
    public function delSmsAliyunConfig($site_id){
        $res = $this->site_model->deleteSiteConfig(['name' => 'NC_SMS_ALIYUN_CONFIG', 'site_id' => $site_id]);
        return $res;
    }
    
}
