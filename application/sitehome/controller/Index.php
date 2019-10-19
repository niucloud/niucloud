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
namespace app\sitehome\controller;

use app\common\controller\BaseSiteHome;
use app\common\model\Addon;
use app\common\model\Config;
use app\common\model\Member;
use app\common\model\Notice as NoticeModel;
use app\common\model\Visit;

class Index extends BaseSiteHome
{
	public $app_key = '';
	public $app_secret = '';
	public $domain = '';
	
	public function __construct()
	{
		parent::__construct();
		$config_model = new Config();
		$auth_info = $config_model->getConfigInfo([ 'name' => 'SYSTEM_AUTH_CONFIG' ]);
		$app_config = json_decode($auth_info['data']['value'], true);
		$this->app_key = $app_config['app_key'];
		$this->app_secret = $app_config['app_secret'];
		$this->domain = $app_config['domain'];
	}
	
	public function index()
	{
		$app = $this->siteInfo['addon_app'];
		$addon_model = new Addon();
		$addon_result = $addon_model->getAddonInfo([ "name" => $app ]);
		$addon = $addon_result["data"];
		
		$addon["support_app_type"] = getSupportPort($addon["support_app_type"]);
		$this->assign("addon_info", $addon);
		$this->assign("title", "概况");
		$res = hook('appHomeIndex', [ 'addon_app' => $app ]);
		if (empty($res)) {
			$this->loadEmptyPage();
		} else {
			return $res[0];
		}
		
	}
	
	/**
	 * 默认页面
	 */
	public function loadEmptyPage()
	{
		$site_id = $this->siteId;
		$this->assign('site_id', $site_id);
		$this->assign('site_info', $this->siteInfo);
		//当日
		
		// 今天起始时间戳
		$today_start = mktime(0, 0, 0, date("m", time()), date("d", time()), date("Y", time()));
		$today_end = mktime(23, 59, 59, date("m", time()), date("d", time()), date("Y", time()));
		// 本月起始时间戳
		$month_start = mktime(0, 0, 0, date('m'), 1, date('Y'));
		$month_end = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
		// 上周起始时间戳
		$lastweek_start = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1 - 7, date('Y'));
		$lastweek_end = mktime(23, 59, 59, date('m'), date('d') - date('w') + 7 - 7, date('Y'));
		// 本周起始时间戳
		$week_start = mktime(0, 0, 0, date('m'), date('d') - date('w') + 1, date('Y'));
		$week_end = mktime(23, 59, 59, date('m'), date('d') - date('w') + 7, date('Y'));
		$member_model = new Member();
		$member_count['total'] = $member_model->getMemberCount([ 'site_id' => $site_id ])['data'];
		$member_count['today_count'] = $member_model->getMemberCount([ 'site_id' => $site_id, 'register_time' => [ 'between', [ $today_start, $today_end ] ] ])['data'];
		$member_count['month_count'] = $member_model->getMemberCount([ 'site_id' => $site_id, 'register_time' => [ 'between', [ $month_start, $month_end ] ] ])['data'];
		$member_count['week_count'] = $member_model->getMemberCount([ 'site_id' => $site_id, 'register_time' => [ 'between', [ $week_start, $week_end ] ] ])['data'];
		$member_count['lastweek_count'] = $member_model->getMemberCount([ 'site_id' => $site_id, 'register_time' => [ 'between', [ $lastweek_start, $lastweek_end ] ] ])['data'];
		
		$this->assign('member_count', $member_count);
		
		$visit_model = new Visit();
		$visit_count = [];
		
		$visit_count["total"] = $visit_model->getVisitCountData([ 'site_id' => $site_id, "date_type" => "" ])["data"];
		$visit_count["today_count"] = $visit_model->getVisitCountData([ 'site_id' => $site_id, "start_data" => $today_start, "end_date" => $today_end, "date_type" => "daterange" ])['data'];
		$visit_count["month_count"] = $visit_model->getVisitCountData([ 'site_id' => $site_id, "start_data" => $month_start, "end_date" => $month_end, "date_type" => "daterange" ])['data'];
		$visit_count["week_count"] = $visit_model->getVisitCountData([ 'site_id' => $site_id, "start_data" => $week_start, "end_date" => $week_end, "date_type" => "daterange" ])['data'];
		$visit_count["lastweek_count"] = $visit_model->getVisitCountData([ 'site_id' => $site_id, "start_data" => $lastweek_start, "end_date" => $lastweek_end, "date_type" => "daterange" ])['data'];
		$this->assign("visit_count", $visit_count);
		
		// 获取插件列表
		$addon_model = new Addon();
		$addon_list = $addon_model->getUserAddonList(UID);
		$this->assign('addon_list', $addon_list);
		
		//系统公告
		$notice_model = new NoticeModel();
		$notice_list = $notice_model->getNoticePageList([ 'is_display' => 1 ], 1, PAGE_LIST_ROWS, 'create_time desc', 'notice_id,title,create_time');
		$this->assign("notice_list", $notice_list['data']['list']);
		
		echo $this->fetch('index/index');
	}
	
	/**
	 * 访问统计图表数据
	 */
	public function getVisitStatisticsData()
	{
		$type = input("type", "ALL");
		$date_type = input("date_type", "daterange");//日期选择类型
		$daterange = input("daterange", "");
		$visit_model = new Visit();
		if (!empty($daterange)) {
			$daterange_array = explode(" - ", $daterange);
			$start_date = date_format(date_create($daterange_array[0]), "Ymd");
			$end_date = date_format(date_create($daterange_array[1]), "Ymd");
		}
		$condition = array(
			"type" => $type,
			"date_type" => $date_type,
			"date_range" => [ "start_date" => $start_date, "end_date" => $end_date ],
			"site_id" => $this->siteId
		);
		$data = $visit_model->getVisitStatisticsData($condition);
		return $data;
		
	}
	
	/**
	 * 获取当日/昨日 /本周/本月的访问数据
	 */
	public function getVisitCountData()
	{
		$visit_model = new Visit();
		$condition = array(
			"site_id" => $this->siteId,
			"type" => "ALL"
		);
		//本日数据
		$condition["date_type"] = "today";
		$today_data_result = $visit_model->getVisitCountData($condition);
		$today_count = $today_data_result["data"]["visit_count"];//访问量
		$today_ip_count = $today_data_result["data"]["visit_ip_count"];//访问人数
		$condition["date_type"] = "yesterday";
		$yesterday_data_result = $visit_model->getVisitCountData($condition);
		$yesterday_count = $yesterday_data_result["data"]["visit_count"];//访问量
		$yesterday_ip_count = $yesterday_data_result["data"]["visit_ip_count"];//访问人数
		
		$condition["date_type"] = "week";
		$week_data_result = $visit_model->getVisitCountData($condition);
		$week_count = $week_data_result["data"]["visit_count"];//访问量
		$week_ip_count = $week_data_result["data"]["visit_ip_count"];//访问人数
		
		$condition["date_type"] = "month";
		$month_data_result = $visit_model->getVisitCountData($condition);
		$month_count = $month_data_result["data"]["visit_count"];//访问量
		$month_ip_count = $month_data_result["data"]["visit_ip_count"];//访问人数
		
		$data = array(
			"today_count" => $today_count,
			"today_ip_count" => $today_ip_count,
			"yesterday_count" => $yesterday_count,
			"yesterday_ip_count" => $yesterday_ip_count,
			"week_count" => $week_count,
			"week_ip_count" => $week_ip_count,
			"month_count" => $month_count,
			"month_ip_count" => $month_ip_count,
		);
		return success($data);
		
	}
}