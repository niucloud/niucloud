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
namespace addon\app\Applet2\sitehome\controller;

use addon\system\Wechat\common\model\Wechat as WechatModel;

use app\common\model\Notice as NoticeModel;

/**
 * 首页 控制器
 */
class Index extends Base
{
	
	/**
	 * 首页
	 */
	public function index()
	{
		//系统公告
		$notice_model = new NoticeModel();
		$notice_list = $notice_model->getNoticePageList([ 'is_display' => 1 ], 1, PAGE_LIST_ROWS, 'create_time desc', 'notice_id,title,create_time');
		$this->assign("notice_list", $notice_list['data']['list']);
		$this->assign("title", "首页");

//		//累计关注人数（当前关注的粉丝总数）
//		$wechat_model = new WechatModel();
//
//		$total_condition = [
//			'site_id' => $this->siteId,
//		];
//		$total = $wechat_model->getWechatFansCount($total_condition);
//		$this->assign("total", $total);
//
//		//昨日
//		$time = date("Y-m-d", strtotime("-1 day"));
//		$yesterday = $this->getFansStatistical($time);
//		$this->assign("yesterday", $yesterday[0]);
//
//		//今日
//		$time = date("Y-m-d");
//		$today = $this->getFansStatistical($time);
//		$this->assign("today", $today[0]);
		
		return $this->fetch(ADDON_APP_PATH . 'Applet2/sitehome/view/index/index.html', [], $this->replace);
	}
	
	/**
	 * 微信粉丝统计
	 */
	public function getFansStatistical($time)
	{
		$data = [];
		$wechat_model = new WechatModel();
		if (empty($time)) {
			$time = input("time", "");
		}
		if (!empty($time)) {
			$time = explode(",", $time);
			foreach ($time as $k => $v) {
				
				$start_time = strtotime($v);
				$end_time = strtotime($v . '23:59:59');
				
				//新关注人数（一段时间内新关注的人数）
				$subscribe_condition = [
					'site_id' => $this->siteId,
					'subscribe_time' => [ 'between', [ $start_time, $end_time ] ]
				];
				$subscribe = $wechat_model->getWechatFansCount($subscribe_condition);
				$data[ $k ]['subscribe'] = $subscribe;
				
				//取消关注人数（一段时间内取消关注的人数）
				$unsubscribe_condition = [
					'site_id' => $this->siteId,
					'unsubscribe_time' => [ 'between', [ $start_time, $end_time ] ]
				];
				$unsubscribe = $wechat_model->getWechatFansCount($unsubscribe_condition);
				$data[ $k ]['unsubscribe'] = $unsubscribe;
				
				//净增关注人数（一段时间内新增关注人数-取消关注人数）
				$data[ $k ]['net_gain'] = $subscribe - $unsubscribe;
				
				$total_condition = [
					'site_id' => $this->siteId,
					'subscribe_time' => [ 'between', [ $start_time, $end_time ] ]
				];
				$total = $wechat_model->getWechatFansCount($total_condition);
				$data[ $k ]['total'] = $total;
				
			}
		}
		
		return $data;
	}
	
}