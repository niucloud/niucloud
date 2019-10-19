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
namespace addon\system\SmsAliyun;

use app\common\controller\BaseSmsAddon;
use addon\system\SmsAliyun\common\model\SmsAliyunConfig;
use addon\system\SmsAliyun\common\model\SmsAliyun;


/**
 * 短信系统插件
 */
class SmsAliyunAddon extends BaseSmsAddon
{
	public $info = array(
		'name' => 'SmsAliyun',
		'title' => '阿里云短信',
		'description' => '阿里云短信',
		'status' => 1,
		'author' => '',
		'version' => '1.0',
		'visble' => 0,
		'type' => 'ADDON_SYSTEM',
	    'category' => 'SYSTEM',
		'content' => '阿里云短信',
		//预置插件，多个用英文逗号分开
		'preset_addon' => 'Sms',
		'support_addon' => '',
	    'support_app_type' => 'wap,weapp'
	);
	
	public $config;
	
	public function __construct()
	{
		parent::__construct();
		$this->config = $this->config_info;
	}
	
	/**
	 * 安装
	 * @return ['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function install()
	{
		return success();
	}
	
	
	/**
	 * 卸载
	 * @return ['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function uninstall()
	{
		return success();
	}
	
	/**
	 * 初始化站点数据，在添加站点的时候用
	 * @param integer $site_id
	 * Returns:['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function addToSite($site_id)
	{
		return success();
	}
	
	/**
	 * 删除站点数据--删除站点时调用
	 * @param integer $site_id
	 * Returns:['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function delFromSite($site_id)
	{
		
	    return success();
	}
	
	/**
	 * 复制站点数据--复制站点时调用
	 * @param integer $site_id
	 * @param integer $new_site_id
	 * @return boolean
	 */
	public function copyToSite($site_id, $new_site_id)
	{
	    return success();
	}
	
	/**
	 * 发送短信
	 * @param array $param
	 */
	public function sendSmsMessage($param = []){
	    if ($param['addon_name'] != $this->info['name']) return;

	    $sms_aliyun = new SmsAliyun();
	    $res = $sms_aliyun->aliyunSmsSend($param);
	    return $res;
	}
	/**
	 * 获取短信配置
	 * @param integer $site_id
	 * Returns:['info' => [], 'site_config' => []]
	 */
	public function getSmsConfig($param = [])
	{
	    $aliyun_config = new SmsAliyunConfig();
	    $config_info = $aliyun_config->getSmsAliyunConfig($param['site_id']);
	    $this->info["url"] = addon_url('SmsAliyun://admin/Index/config');
	    $this->info['icon'] = __ROOT__.'/addon/system/' . $this->info['name'] . '/icon.png';
	    return [
	        'info' => $this->info,
	        'config' => $config_info["data"]
	    ];
	}
	
	
	/**
	 * 关闭短信
	 * @param array $param
	 */
	public function closeSms($param = [])
	{
        $aliyun_config_model = new SmsAliyunConfig();
        $data = array(
            "status" => 0,
            "site_id" => $param['site_id'],
            "update_time" => time()
        );
        $res = $aliyun_config_model->setSmsAliyunConfig($data);
        return $res;
	}
	
	/**
	 * 编辑短信模板
	 * @param array $param
	 */
	public function doEditMessage($param = [])
	{
	    if($param["name"] == "Sms"){
	        $aliyun_config = new SmsAliyunConfig();
	        $config_info = $aliyun_config->getSmsAliyunConfig($param["site_id"]);
	        if ($config_info['data']["status"] == 1) {
	            if($param["site_id"] > 0){
                    $this->redirect(addon_url('SmsAliyun://sitehome/index/edit', [ 'keyword' => $param['keyword'] ]));
                }else{
                    $this->redirect(addon_url('SmsAliyun://admin/index/template', [ 'keyword' => $param['keyword'] ]));
                }

	        }
	    }
	}
	
	
}