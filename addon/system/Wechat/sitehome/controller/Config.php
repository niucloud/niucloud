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
namespace addon\system\Wechat\sitehome\controller;

use addon\system\Wechat\common\model\Wechat;
use util\weixin\Weixin;
use app\common\model\Config as ConfigModel;

/**
 * 微信公众号基础配置控制器
 */
class Config extends Base
{
	
	/**
	 * 微信公众号配置
	 * @return mixed
	 */
	public function index()
	{
		$author_code = input('auth_code', '');
		$wechat_model = new Wechat();
		if (!empty($author_code)) {
            $config_model = new ConfigModel();
            $wechat_info_result = $config_model->getConfigInfo([ 'name' => 'WECHAT_PLATFORM_CONFIG' ]);
            if($wechat_info_result["data"]["status"] == 0)
                exit("缺少接入平台关键数据!");

            $wechat_info = json_decode($wechat_info_result['data']['value'], true);

			$weixin = new Weixin('platform');
			$weixin->initWechatPlatformAccount($wechat_info["app_id"], $wechat_info["app_secret"], $wechat_info["encodingaeskey"], $wechat_info["token"]);
			// 获取微信授权成功的基本信息
			$author_data = $weixin->getQueryAuth($author_code);

			$author_info = $author_data->authorization_info;
			$author_array = object_to_array($author_info);
			$info_data = $weixin->getAuthorizerInfo($author_array['authorizer_appid']);
			$info_array = object_to_array($info_data);
			$authorizer_appid = $author_array['authorizer_appid'];
			$authorizer_refresh_token = $author_array['authorizer_refresh_token'];
			$authorizer_access_token = $author_array['authorizer_access_token'];
			$func_info = json_encode($author_array['func_info']);
			$nick_name = $info_array['authorizer_info']['nick_name'];
			$head_img = $info_array['authorizer_info']['head_img'];
			$user_name = $info_array['authorizer_info']['user_name'];
			$alias = $info_array['authorizer_info']['alias'];
			$qrcode_url = $info_array['authorizer_info']['qrcode_url'];
			
			$res = $wechat_model->setWechatAuth(SITE_ID, $authorizer_appid, $authorizer_refresh_token, $authorizer_access_token, $func_info, $nick_name, $head_img, $user_name, $alias, $qrcode_url);
			if ($res) {
				echo "微信授权成功";
			}
			exit();
		}
		
		$wechat_config_info = $wechat_model->getWechatConfigInfo(SITE_ID);

		if (!empty($wechat_config_info) && !empty($wechat_config_info['data']['status'])) {
			$this->redirect(addon_url('Wechat://sitehome/config/config'));
		}
		
		//授权配置信息
		$wechat_auth_info = $wechat_model->getWechatAuthInfo(SITE_ID);
		if (!empty($wechat_auth_info) && !empty($wechat_auth_info['data']['status'])) {
			$this->redirect(addon_url('Wechat://sitehome/config/author'));
		}
		
		//查询后台是否配置开放平台信息
		$config_model = new ConfigModel();
		$admin_config_info = $config_model->getConfigInfo([ 'name' => 'WECHAT_PLATFORM_CONFIG' ]);
		$value = json_decode($admin_config_info['data']['value'], true);
		$count = !empty($value) ? count($value) : 0;
		
		$this->assign('value', $value);
		$this->assign('count', $count);
		
		return $this->fetch('config/index', [], $this->replace);
	}
	
	public function config()
	{
	    
		$wechat_model = new Wechat();
		if (IS_AJAX) {
			$wechat_name = input('wechat_name', '');
			$wechat_account = input('wechat_account', '');
			$wechat_code = input('wechat_code', '');
			$wechat_type = input('wechat_type', '');
			$wechat_original = input('wechat_original', '');
			$appid = input('appid', '');
			$appsecret = input('appsecret', '');
			$token = input('token', 'TOKEN');
            $encodingaeskey = input('encodingaeskey', '');

            $json_data = array(
                "appid" => $appid,
                "appsecret" => $appsecret,
                "token" => $token,
                "encodingaeskey" => $encodingaeskey,
            );
            $data = array(
                "site_id" => $this->siteId,
                "value" => json_encode($json_data),
                "create_time" => time()
            );
			$res = $wechat_model->setWechatConfig($data);
			$json_data = array(
				"wechat_name" => $wechat_name,
				"wechat_account" => $wechat_account,
				"wechat_code" => $wechat_code,
				"wechat_type" => $wechat_type,
				"wechat_original" => $wechat_original
			);
			$data = array(
				"site_id" => SITE_ID,
				"value" => json_encode($json_data)
			);
			$res = $wechat_model->setWechatInfoConfig($data);
//			//检测微信是否获取到token
			$get_token = $wechat_model->getAccessToken($appid, $appsecret);
			if ($get_token['code'] != 0) {
				return $get_token;
			}
			return $res;
		} else {
			$wechat_config_info = $wechat_model->getWechatConfigInfo(SITE_ID);
			$wechat_info_config_info = $wechat_model->getWechatInfoConfig(SITE_ID);
			$wechat_info = $wechat_info_config_info['data']["value"];
			$config_info = $wechat_config_info['data']['value'];
			$this->assign("config_info", $config_info);
			$this->assign("wechat_info", $wechat_info);
			// 获取当前域名
			$url = __ROOT__;
			// 去除链接的http://头部
			$url_top = str_replace("https://", "", $url);
			$url_top = str_replace("http://", "", $url_top);
			// 去除链接的尾部/?s=
			$url_top = str_replace('/?s=', '', $url_top);
			$call_back_url = $url . '/s' . SITE_ID . '/Wechat/common/config/relateWeixin';
			$this->assign("url", $url_top);
			$this->assign("call_back_url", $call_back_url);
			
			return $this->fetch('config/config', [], $this->replace);
		}
	}
	
	/**
	 * 微信授权
	 */
	public function author()
	{
		
		$wechat_model = new Wechat();
		//授权配置信息
		$wechat_auth_info = $wechat_model->getWechatAuthInfo(SITE_ID);
		$auth_info = json_decode($wechat_auth_info['data']['value'], true);
		dump($auth_info);
		$this->assign('auth_info', $auth_info);
		return $this->fetch('config/author', [], $this->replace);
	}
	
	/**
	 * 重新绑定
	 * @return multitype:string mixed
	 */
	public function unbind()
	{
		if (IS_AJAX) {
			$unbind_type = input('unbind_type');
			$wechat_model = new Wechat();
			$res = $wechat_model->unbindStatus(SITE_ID, $unbind_type);
			return $res;
		}
	}
	
	/**
	 * 功能设置
	 */
	public function setting()
	{
		
		$wechat_model = new Wechat();
		//授权配置信息
		$wechat_auth_info = $wechat_model->getWechatAuthInfo(SITE_ID);
		$auth_info = json_decode($wechat_auth_info['data']['value'], true);
		
		$this->assign('auth_info', $auth_info);
		return $this->fetch('config/setting', [], $this->replace);
	}
	
	/**
	 * 访问统计
	 * @return mixed
	 */
	public function accessStatistics()
	{
	    $wechat_model = new Wechat();

	    $yesterday = date('Y-m-d', strtotime('-1 day'));
        //昨天的用户分析数据
	    $wechat_fans_result = $wechat_model->getFansStatistics(['site_id' => request()->siteid(),'begin_date' => $yesterday,'end_date' => $yesterday]);
	    $this->assign('yesterday_user_data', $wechat_fans_result['data'][0]);
        //昨天的接口分析数据
        $wechat_interface_result = $wechat_model->getInterfaceSummary(['site_id' => request()->siteid(),'begin_date' => $yesterday,'end_date' => $yesterday]);
        
        $this->assign('yesterday_interface_data', $wechat_interface_result['data'][0]);
		return $this->fetch('config/access_statistics', [], $this->replace);
	}
	
	/**
	 * 获取微信接口调用数据统计
	 */
	public function getInterfaceSummary(){
	    if (IS_AJAX) {
	        $date_type = input("date_type", "week");
            $date_data = $this->getDaterange($date_type);
	        $wechat_model = new Wechat();
            $is_error = true;
            $callback_count_data = [];
            $fail_count_data = [];
            $average_time_cost_data = [];
            $max_time_cost_data = [];
            foreach ($date_data["date_list"] as $k => $v) {
                $callback_count = 0;
                $fail_count = 0;
                $average_time_cost = 0;
                $max_time_cost = 0;
                if($is_error) {
                    $temp_data = $wechat_model->getInterfaceSummary(["begin_date" => $v, "end_date" => $v, "site_id" => $this->siteId]);
                    if (!empty($temp_data["data"])) {
                        $temp_date_item = $temp_data["data"];
                        $callback_count = $temp_date_item[0]["callback_count"];
                        $fail_count = $temp_date_item[0]["fail_count"];
                        $average_time_cost = $temp_date_item[0]["total_time_cost"] / $temp_date_item[0]["callback_count"];
                        $max_time_cost = $temp_date_item[0]["max_time_cost"];
                    }else{
                        $is_error = false;
                    }
                }
                $callback_count_data[] = $callback_count;
                $fail_count_data[] = $fail_count;
                $average_time_cost_data[] = $average_time_cost;
                $max_time_cost_data[] = $max_time_cost;
            }
            $return_data = array(
                "date" => $date_data["date_list"],
                "data" => array(
                    "callback_count_data" => $callback_count_data,
                    "fail_count_data" => $fail_count_data,
                    "average_time_cost_data" => $average_time_cost_data,
                    "max_time_cost_data" => $max_time_cost_data
                )
            );
	        return success($return_data);
	    }
	}

    /**
     * 获取微信用户分析调用数据统计
     */
    public function getUserSummary(){
        if (IS_AJAX) {
            $date_type = input("date_type", "week");
            $is_error = true;
            $date_data = $this->getDaterange($date_type);
            $wechat_model = new Wechat();
            $new_user_data = [];
            $cancel_user_data = [];
            $net_growth_user_data = [];
            $cumulate_user_data = [];
            foreach ($date_data["date_list"] as $k => $v) {
                $new_user = 0;
                $cancel_user = 0;
                $net_growth_user = 0;
                $cumulate_user = 0;
                if($is_error){
                    $temp_data = $wechat_model->getFansStatistics(["begin_date" => $v, "end_date" => $v, "site_id" =>  $this->siteId]);
                    if(!empty($temp_data["data"])){
                        $temp_date_item = $temp_data["data"];
                        $new_user = $temp_date_item[0]["new_user"];
                        $cancel_user = $temp_date_item[0]["cancel_user"];
                        $net_growth_user = $temp_date_item[0]["net_growth_user"];
                        $cumulate_user = $temp_date_item[0]["cumulate_user"];
                    }else{
                        $is_error = false;
                    }
                }

                $new_user_data[] = $new_user;
                $cancel_user_data[] = $cancel_user;
                $net_growth_user_data[] = $net_growth_user;
                $cumulate_user_data[] = $cumulate_user;
            }
            $return_data = array(
                "date" => $date_data["date_list"],
                "data" => array(
                    "new_user_data" => $new_user_data,
                    "cancel_user_data" => $cancel_user_data,
                    "net_growth_user_data" => $net_growth_user_data,
                    "cumulate_user_data" => $cumulate_user_data
                )
            );
            return success($return_data);
        }
    }
    /**
     * 获取时间间隔
     * @param $date_type
     * @return array
     */
	public function getDaterange($date_type){
        $yesterday = strtotime('-1 days');
        switch ($date_type) {
            case 'yesterday':
                $yesterday = strtotime('-1 days');
                $begin_time = $yesterday;
                $end_time = $yesterday;
                break;
            case 'week':
                $week = strtotime('-7 days');
                $begin_time = $week;
                $end_time = $yesterday;
                break;
            case 'month':
                $month = strtotime('-30 days');
                $begin_time = $month;
                $end_time = $yesterday;
                break;
        }
        $date_x = periodGroup($begin_time, $end_time, "Y-m-d");
        $begin_date = date("Ymd", $begin_time);
        $end_date = date("Ymd", $end_time);
        $data = array(
            "begin_date" => $begin_date,
            "end_date" => $end_date,
            "date_list" => $date_x,
        );
        return $data;
    }
}