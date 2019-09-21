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

namespace addon\system\OAuthLoginWechat\common\model;

use app\common\model\Site;

/**
 * 微信登录配置
 * @author lzw
 */
class WechatLoginConfig
{
	
    public $site_model;
    public function __construct()
    {
        $this->site_model = new Site();
    }
	/**
	 * 设置微信登录配置
	 * @param number $site_id
	 * @param string $value
	 * @param number $status
	 */
	public function setWechatLoginConfig($data)
	{
	    $data["name"] = 'NC_OAUTHlOGIN_WECHAT';
	    $res = $this->site_model->setSiteConfig($data);
	    return $res;
	}
	
	/**
	 * 查询数据
	 */
	public function getWechatLoginConfig($site_id)
	{	
	    $config = $this->site_model->getSiteConfigInfo([ 'name' => 'NC_OAUTHlOGIN_WECHAT', 'site_id' => $site_id ]);
	    $value = [];
	    if(!empty($config["data"]["value"])){
	        $value = json_decode($config["data"]["value"], true);
	    }
	    $config["data"]["value"] = $value;
	    return $config;

	}
	
	/**
	 * 删除数据
	 * @param number $site_id
	 */
	public function deleteWechatLoginConfig($site_id)
	{
	    $res = $this->site_model->deleteSiteConfig([ 'name' => 'NC_OAUTHlOGIN_WECHAT', 'site_id' => $site_id ]);
		return $res;
	}
	
}