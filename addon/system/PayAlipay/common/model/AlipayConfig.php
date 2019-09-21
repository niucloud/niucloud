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

namespace addon\system\PayAlipay\common\model;

use app\common\model\Site;

/**
 * 支付宝配置
 * @author lzw
 */
class AlipayConfig
{
    
    public $site_model;
    public function __construct()
    {
        $this->site_model = new Site();
    }
	/**
	 * 设置支付宝配置
	 * @param number $site_id
	 * @param string $value
	 * @param number $status
	 * Returns:['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function setAlipayConfig($data)
	{
	    $data["name"] = 'NC_PAYALIPAY_CONFIG';
	    $condition = array(
	        "name" => 'NC_PAYALIPAY_CONFIG'
	    );
	    $res = $this->site_model->setSiteConfig($data, $condition);
	    return $res;
	}
	
	/**
	 * 获取支付宝配置
	 * @param number $site_id
	 * Returns:['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function getAlipayConfig($site_id)
	{
	    $config = $this->site_model->getSiteConfigInfo(["site_id" => $site_id, "name" => "NC_PAYALIPAY_CONFIG"]);
	    $value = [];
	    if(!empty($config["data"]["value"])){
	        $value = json_decode($config["data"]["value"], true);
	    }
	    $config["data"]["value"] = $value;
	    return $config;
	}
	
	/**
	 * 删除该站点的支付宝配置
	 * @param number $site_id
	 * Returns:['code' => 0|1, 'message' => '', 'data' => []]
	 */
	public function delAlipayConfig($site_id)
	{
	    $res = $this->site_model->deleteSiteConfig([ 'name' => 'NC_PAYALIPAY_CONFIG', 'site_id' => $site_id ]);
		return $res;
	}
	
}
