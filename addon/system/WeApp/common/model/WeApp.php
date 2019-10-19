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

namespace addon\system\WeApp\common\model;


use app\common\model\Site;
use think\Cache;
use util\weixin\Weixin;

/**
 * 微信小程序配置
 */
class WeApp
{
	
	public $site_model;
	
	public function __construct()
	{
		$this->site_model = new Site();
	}
	
	/**
	 * 设置小程序开发配置
	 */
	public function setWeAppConfig($data)
	{
		
		$data["name"] = 'NC_WEAPP_CONFIG';
		$res = $this->site_model->setSiteConfig($data);
		return $res;
	}
	
	/**
	 * 获取小程序开发配置
	 */
	public function getWeAppConfigInfo($site_id)
	{
		$config = $this->site_model->getSiteConfigInfo([ 'site_id' => $site_id, 'name' => 'NC_WEAPP_CONFIG' ]);
		$value = [];
		if (!empty($config["data"]["value"])) {
			$value = json_decode($config["data"]["value"], true);
		}
		$config["data"]["value"] = $value;
		return $config;
	}

    /******************************************************************* 小程序统计 start ************************************************************************/
    /**
     * 时间间隔间的微信小程序访问数据
     */
    public function getVisitPage($param)
    {
        $config_result = $this->getWeAppConfigInfo($param["site_id"]);
        $config_data = $config_result["data"]["value"];
        $wechat_api = new Weixin('public');
        $wechat_api->initWechatPublicAccount($config_data["appid"], $config_data["appsecret"]);
        $result = $wechat_api->getVisitPage(["begin_date" => $param["begin_date"], "end_date" => $param["end_date"]]);
        if (empty($result) || $result["errcode"] != 0 ) {
            return error([], $result["errmsg"]);
        } else {
            return success($result["list"]);
        }
    }

    /**
     * 获取用户访问小程序数据月趋势
     * @param $param
     * @return \multitype
     */
    public function getMonthlyVisitTrend($param)
    {
        $config_result = $this->getWeAppConfigInfo($param["site_id"]);
        $config_data = $config_result["data"]["value"];
        $wechat_api = new Weixin('public');
        $wechat_api->initWechatPublicAccount($config_data["appid"], $config_data["appsecret"]);
        $result = $wechat_api->getMonthlyVisitTrend(["begin_date" => $param["begin_date"], "end_date" => $param["end_date"]]);
        if (empty($result) || $result["errcode"] != 0 ) {
            return error([], $result["errmsg"]);
        } else {
            return success($result["list"]);
        }
    }

    /**
     * 时间间隔间的微信小程序访问数据
     * @param $param
     * @return \multitype
     */
    public function getWeeklyVisitTrend($param)
    {
        $config_result = $this->getWeAppConfigInfo($param["site_id"]);
        $config_data = $config_result["data"]["value"];
        $wechat_api = new Weixin('public');
        $wechat_api->initWechatPublicAccount($config_data["appid"], $config_data["appsecret"]);
        $result = $wechat_api->getWeeklyVisitTrend(["begin_date" => $param["begin_date"], "end_date" => $param["end_date"]]);
        if (empty($result) || $result["errcode"] != 0 ) {
            return error([], $result["errmsg"]);
        } else {
            return success($result["list"]);
        }
    }

    /**
     * 获取用户访问小程序数据日趋势
     * @param $param
     * @return \multitype
     */
    public function getDailyVisitTrend($param)
    {
        $info = Cache::tag("weapp_visit_info" . $param["site_id"])->get("weapp_visit_info_day_" . $param["site_id"] . "_" .$param["begin_date"]);

        if(!empty($info)){
            return success($info);
        }
        $config_result = $this->getWeAppConfigInfo($param["site_id"]);
        $config_data = $config_result["data"]["value"];

        $wechat_api = new Weixin('public');

        $wechat_api->initWechatPublicAccount($config_data["appid"], $config_data["appsecret"]);
        $result = $wechat_api->getDailyVisitTrend(["begin_date" => $param["begin_date"], "end_date" => $param["end_date"]]);

//        $data = array(
//            "session_cnt" => 0,
//            "visit_pv" => 0,
//            "visit_uv" => 0,
//            "visit_uv_new" => 0,
//            "stay_time_uv" => 0,
//            "stay_time_session" => 0,
//            "visit_depth" => 0,
//        );
//        Cache::tag("weapp_visit_info" . $param["site_id"])->set("weapp_visit_info_day_" . $param["site_id"] . "_" .$param["begin_date"], $data);
        if (empty($result) || $result["errcode"] != 0 ) {
            return error([], $result["errmsg"]);
        }
        Cache::tag("weapp_visit_info" . $param["site_id"])->set("weapp_visit_info_day_" . $param["site_id"] . "_" .$param["begin_date"], $result["list"]);
        return success($result["list"]);
    }

    /******************************************************************* 小程序统计 end ************************************************************************/
}