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
namespace addon\system\WeApp\sitehome\controller;

use app\common\model\Site;
use app\common\model\Addon;
use app\common\model\DiyView;
use addon\system\WeApp\common\model\WeApp;

/**
 * 微信小程序基础配置
 */
class Config extends Base
{
	
	/**
	 * 功能设置
	 */
	public function setting()
	{
		return $this->fetch('config/setting', [], $this->replace);
	}

	
	/**
	 * 小程序配置
	 * @return mixed
	 */
	public function config()
	{
		
		$weapp_model = new WeApp();
		if (IS_AJAX) {
            $weapp_name = input('weapp_name', '');
            $weapp_account = input('weapp_account', '');
            $weapp_code = input('weapp_code', '');
            $weapp_original = input('weapp_original', '');
            $appid = input('appid', '');
            $appsecret = input('appsecret', '');
            $token = input('token', 'TOKEN');
            $encodingaeskey = input('encodingaeskey', '');
            $json_data = array(
                "appid" => $appid,
                "appsecret" => $appsecret,
                "token" => $token,
                "weapp_name" => $weapp_name,
                "weapp_account" => $weapp_account,
                "weapp_code" => $weapp_code,
                "weapp_original" => $weapp_original,
                "encodingaeskey" => $encodingaeskey
            );
            $data = array(
                "site_id" => SITE_ID,
                "value" => json_encode($json_data)
            );
            $res = $weapp_model->setWeAppConfig($data);
			return $res;
		} else {
			$weapp_config_info = $weapp_model->getWeAppConfigInfo($this->siteId);
			$config_info = $weapp_config_info['data']['value'];
			$this->assign("config_info", $config_info);
            // 获取当前域名
            $url = __ROOT__;
            // 去除链接的http://头部
            $url_top = str_replace("https://", "", $url);
            $url_top = str_replace("http://", "", $url_top);
            // 去除链接的尾部/?s=
            $url_top = str_replace('/?s=', '', $url_top);
            $call_back_url = $url . '/' . SITE_ID . '/Wechat/wap/config/relateWeixin';
            $this->assign("url", $url_top);
            $this->assign("call_back_url", $call_back_url);
			return $this->fetch('config/config', [], $this->replace);
		}
	}
	
	/**
	 * 打包下载
	 */
	public function packDownload()
	{
		$site_model = new Site();
		$site_info = $site_model->getSiteInfo([ 'site_id' => $this->siteId ]);
		$site_addon_modules = $site_info['data']['addon_modules'];
		$site_addon_module_array = explode(',', $site_addon_modules);
		$addon_model = new Addon();
		$addons = $addon_model->getAddons();
		
		$class_name = get_addon_class($site_info['data']['addon_app']);
		$config_file = new $class_name();
		
		if (!isset($config_file->config['default_weapp'])) {
			
			echo '默认页面配置不存在';
			exit();
		}
		
		$addon_path_array = $addons['addon_path'];
		foreach ($site_addon_module_array as $k => $v) {
			if (!empty($v)) {
				$addon_path = $addon_path_array[ $v ];
				
				if (is_dir($addon_path . 'weapp/')) {
					dir_copy($addon_path . 'weapp/', 'attachment/' . $this->siteId . '/weapp/');
				}
			}
		}
		//变量替换
		$file = file_get_contents('attachment/' . $this->siteId . '/weapp/app.js');
		$file = str_replace('{{url}}', \think\Request::instance()->root(true) . '/', $file);
		$file = str_replace('{{site_id}}', 's' . $this->siteId, $file);
		$file = str_replace('{{app_key}}', $site_info['data']['app_key'], $file);
		$file = str_replace('{{site_title}}', $site_info['data']['site_name'], $file);
		file_put_contents('attachment/' . $this->siteId . '/weapp/app.js', $file);
		
		//读取配置app.json
		$pages_arr = array( $config_file->config['default_weapp'] ); //赋值默认页面
		$dir = 'attachment/' . $this->siteId . '/weapp/pages';
		$files = dir_scan($dir);
		
		foreach ($files as $key => $item) {
			
			if (!empty($item[0])) {
				$page_url = "pages/$key/" . explode('.', $item[0])[0];
				if ($page_url != $config_file->config['default_weapp']) {
					$pages_arr[] = $page_url;
				}
				
			} else {
				foreach ($item as $children_key => $children_item) {
					$page_url = "pages/$key/$children_key/" . explode('.', $children_item[0])[0];
					if ($page_url != $config_file->config['default_weapp']) {
						$pages_arr[] = $page_url;
					}
				}
			}
			
		}
		$app_data = array(
			'pages' => $pages_arr,
			'window' => array(
				'backgroundTextStyle' => 'light',
				'navigationBarBackgroundColor' => '#fff',
				'navigationBarTitleText' => 'WeChat',
				'navigationBarTextStyle' => 'black'
			),
			'sitemapLocation' => 'sitemap.json'
		);
		
		$app_data = json_encode($app_data, JSON_UNESCAPED_SLASHES);
		file_put_contents('attachment/' . $this->siteId . '/weapp/app.json', $app_data);
		
		//读取配置diyview.json
		$dir = 'attachment/' . $this->siteId . '/weapp/component';
		$files = dir_scan($dir);
		
		$diyview_data = array();
		foreach ($files as $key => $item) {
			$component_key = "component-" . str_replace('_', '-', $key);
			$diyview_data['usingComponents'][ $component_key ] = "../../component/$key/" . explode('.', $item[0])[0];
		}
		
		$diyview_data = json_encode($diyview_data, JSON_UNESCAPED_SLASHES);
		file_put_contents('attachment/' . $this->siteId . '/weapp/pages/diyview/diyview.json', $diyview_data);
		
		//给diyview.wxml写数据
		$diy_view = new DiyView();
		$util_list = $diy_view->getDiyViewUtilList();
		
		$diy_view_xml = '';
		foreach ($util_list['data'] as $item) {
			$type = $item['name'];
			$title = $item['title'];
			$component_label = str_replace('_', '-', strtolower($type));
			$diy_view_xml .= <<<EOT
        \r<!-- $title -->
        <block wx:if="{{item.type == '$type'}}">
        <component-$component_label config="{{item}}" index="{{index}}"></component-$component_label>
        </block>\r
EOT;
		}
		$file = file_get_contents('attachment/' . $this->siteId . '/weapp/pages/diyview/diyview.wxml');
		$file = str_replace('{{diy_view_xml}}', $diy_view_xml, $file);
		file_put_contents('attachment/' . $this->siteId . '/weapp/pages/diyview/diyview.wxml', $file);
		
		//压缩zip
		$file_zip_name = "attachment/$this->siteId/niucloud_$this->siteId.zip";
		$path = "attachment/$this->siteId/weapp";
		zip_dir($path, $file_zip_name, 'weapp');
		
		//文件强制下载
		dir_readfile($file_zip_name);
		del_dir($path);
		unlink($file_zip_name);
		echo "整理完成";
	}

    /**
     * 访问统计
     * @return mixed
     */
    public function accessStatistics()
    {
        return $this->fetch('config/access_statistics', [], $this->replace);

    }

    /**
     *访问统计 (时间间隔)
     */
    public function getVisitPage(){
        $weapp_model = new WeApp();
        $daterange = input("daterange", "");//时间间隔
        $date_type = input("date_type", "today");//时间类型 // today今天

        $date_data = $this->getDaterange($date_type, $daterange);

        $data = $weapp_model->getMonthlyVisitTrend($date_data);
    }

    /**
     * 统计昨日的数据
     */
    public function getVisitCountData(){
        if(IS_AJAX){
            $date_type = input("date_type", "month");
            $data = $this->getVisitData($date_type);
            return success($data);
        }
    }
    /**
     * 查询微信小程序访问数据
     * @param $date_type
     * @param $daterange
     */
    public function getVisitData($date_type){
        $result = [];
        $weapp_model = new WeApp();
        switch ($date_type) {
            case 'yesterday':
                $begin_date = date('Ymd', strtotime('-1 days'));
                $end_date = date('Ymd', strtotime('-1 days'));
                $daterange = array(
                    "begin_date" => $begin_date,
                    "end_date" => $end_date,
                    "site_id" => $this->siteId
                );
                $result = $weapp_model->getDailyVisitTrend($daterange);
                break;
            case 'month':
                $begin_date = date('Y-m-d', strtotime(date('Y-m-01') . ' -1 month'));
                $end_date =  date('Y-m-d', strtotime(date('Y-m-01') . ' -1 day'));
                $daterange = array(
                    "begin_date" => $begin_date,
                    "end_date" => $end_date,
                    "site_id" => $this->siteId
                );
                $result = $weapp_model->getMonthlyVisitTrend($daterange);
                break;
        }
        return $result;
    }

    /**
     * 获取微信小程序  数据分析统计
     */
    public function getVisitStatistics(){

        $weapp_model = new WeApp();
        $daterange = input("daterange", "");
        if(empty($daterange))
            return success([]);

        $is_error = true;
        $site_id = $this->siteId;
        $daterange_array = explode(" - ", $daterange);
        $start_date = date_format(date_create($daterange_array[0]), "Ymd");
        $end_date = date_format(date_create($daterange_array[1]), "Ymd");
        $date_x = periodGroup(strtotime($start_date), strtotime($end_date));
        $session_cnt_data = [];//打开次数
        $visit_pv_data = [];//访问次数
        $visit_uv_data = [];//访问人数
        $visit_uv_new_data = [];//新用户数
        $stay_time_uv_data = [];//人均停留时长 (浮点型，单位：秒)
        $stay_time_session_data = [];//次均停留时长 (浮点型，单位：秒)
        $visit_depth_data = [];//平均访问深度 (浮点型)
        foreach($date_x as $k => $v){
            $session_cnt = 0;//打开次数
            $visit_pv = 0;//访问次数
            $visit_uv = 0;//访问人数
            $visit_uv_new = 0;//新用户数
            $stay_time_uv = 0;//人均停留时长 (浮点型，单位：秒)
            $stay_time_session = 0;//次均停留时长 (浮点型，单位：秒)
            $visit_depth = 0;//平均访问深度 (浮点型)
            if($is_error){
                $temp_daterange = array(
                    "begin_date" => $v,
                    "end_date" => $v,
                    "site_id" => $site_id
                );
                $result = $weapp_model->getDailyVisitTrend($temp_daterange);
                $temp_data = $result["data"];
                if(!empty($temp_data)){
                    $session_cnt = $temp_data["session_cnt"];//打开次数
                    $visit_pv = $temp_data["visit_pv"];//访问次数
                    $visit_uv = $temp_data["visit_uv"];//访问人数
                    $visit_uv_new = $temp_data["visit_uv_new"];//新用户数
                    $stay_time_uv = $temp_data["stay_time_uv"];//人均停留时长 (浮点型，单位：秒)
                    $stay_time_session = $temp_data["stay_time_session"];//次均停留时长 (浮点型，单位：秒)
                    $visit_depth = $temp_data["visit_depth"];//平均访问深度 (浮点型)
                }else{
                    $is_error = false;
                }
            }

            $session_cnt_data[] = $session_cnt;//打开次数
            $visit_pv_data[] = $visit_pv;//访问次数
            $visit_uv_data[] = $visit_uv;//访问人数
            $visit_uv_new_data[] = $visit_uv_new;//新用户数
            $stay_time_uv_data[] = $stay_time_uv;//人均停留时长 (浮点型，单位：秒)
            $stay_time_session_data[] = $stay_time_session;//次均停留时长 (浮点型，单位：秒)
            $visit_depth_data[] = $visit_depth;//平均访问深度 (浮点型)
        }

        $statistics_data = array(
            "date" => $date_x,
            "data" => array(
                "session_cnt_data" => $session_cnt_data,
                "visit_pv_data" => $visit_pv_data,
                "visit_uv_data" => $visit_uv_data,
                "visit_uv_new_data" => $visit_uv_new_data,
                "stay_time_uv_data" => $stay_time_uv_data,
                "stay_time_session_data" => $stay_time_session_data,
                "visit_depth_data" => $visit_depth_data,
            )
        );
        return success($statistics_data);
    }
    /**
     * 得到时间间隔
     * @param $date_type
     * @param string $daterange
     * @return array
     */
    public function getDaterange($date_type, $daterange = ""){
        $today_date = date('Ymd');//当前日日期
        $begin_date = "";
        $end_date = "";

        switch ($date_type) {
            case 'today':
                $begin_date = $today_date;
                $end_date = $today_date;
                break;
            case 'yesterday':
                $begin_date = date('Ymd', strtotime('-1 days'));
                $end_date = date('Ymd', strtotime('-1 days'));
                break;
            case 'week':
                $begin_date = date('Ymd', strtotime('-6 days'));
                $end_date = $today_date;
                break;
            case 'month':
                $begin_date = date('Ymd', strtotime('-29 days'));
                $end_date = $today_date;
                break;
            case 'daterange':
                if(!empty($daterange)){
                    $daterange_array = explode(" - ", $daterange);
                    $begin_date = date_format(date_create($daterange_array[0]), "Ymd");
                    $end_date = date_format(date_create($daterange_array[1]), "Ymd");
                }
                $begin_date = date('Ymd', strtotime($begin_date));//开始日期
                $end_date = date('Ymd', strtotime($end_date));//结束日期
                break;
        }

        return array("begin_date" => $begin_date, "end_date" => $end_date);
    }
}