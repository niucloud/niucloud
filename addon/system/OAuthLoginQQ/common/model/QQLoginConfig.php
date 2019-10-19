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

namespace addon\system\OAuthLoginQQ\common\model;

use app\common\model\Site;

/**
 * 支付配置
 * @author lzw
 */
class QQLoginConfig
{
	
    public $site_model;
    public function __construct()
    {
        $this->site_model = new Site();
    }
    
	/**
	 * 添加修改
	 * @param unknown $site_id
	 * @param unknown $name
	 * @param unknown $value
	 */
    public function setQQLoginConfig($data)
	{
	    $data["name"] = 'NC_OAUTHlOGIN_QQ';
	    $res = $this->site_model->setSiteConfig($data);
	    return $res;
	}
	
	/**
	 * 查询数据
	 * @param unknown $where
	 * @param unknown $field
	 * @param unknown $value
	 */
	public function getQQLoginConfig($site_id)
	{
	    $config = $this->site_model->getSiteConfigInfo([ 'name' => 'NC_OAUTHlOGIN_QQ', 'site_id' => $site_id ]);
	    $value = [];
	    if(!empty($config["data"]["value"])){
	        $value = json_decode($config["data"]["value"], true);
	    }
	    $config["data"]["value"] = $value;
	    return $config;
	}
	
	/**
	 * 删除配置信息
	 * @param int $site_id
	 * @return multitype:string mixed
	 */
	public function deleteQQLoginConfig($site_id)
	{
	    $res = $this->site_model->deleteSiteConfig([ 'name' => 'NC_OAUTHlOGIN_QQ', 'site_id' => $site_id ]);
	    return $res;
	}
}