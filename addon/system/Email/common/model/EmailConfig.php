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

namespace addon\system\Email\common\model;

use app\common\model\Site;

/**
 * 菜单
 * @author lzw
 */
class EmailConfig extends Site
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
	public function setEmailConfig($data)
	{
        $data["name"] = 'EMAIL_CONFIG';
        $res = $this->site_model->setSiteConfig($data);
        return $res;
	}
	
	/**
	 * 查询数据
	 * @param unknown $where
	 * @param unknown $field
	 * @param unknown $value
	 */
	public function getEmailConfig($site_id)
	{
        $config = $this->site_model->getSiteConfigInfo([ 'name' => 'EMAIL_CONFIG', 'site_id' => $site_id ]);
        $value = [];
        if(!empty($config["data"]["value"])){
            $value = json_decode($config["data"]["value"], true);
        }
        $config["data"]["value"] = $value;
        return $config;
	}
	
}