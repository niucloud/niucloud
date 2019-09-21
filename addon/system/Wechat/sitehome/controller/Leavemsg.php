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

/**
 * 微信留言控制器
 */
class Leavemsg extends Base
{
	
	/**
	 * @return \addon\system\Wechat\common\model\multitype|mixed 消息留言
	 */
	public function index()
	{
		$is_starred_msg = input("is_starred_msg", 0);//是否收藏
		if (IS_AJAX) {
			$page = input('page', 1);
			$field = '*';
			$limit = input("limit", PAGE_LIST_ROWS);
			$condition = [ 'site_id' => $this->siteId, 'type' => 7 ];
			$date = input("date", 0);//时间筛选 1最近五天 2今天 3昨天 4前天
			$sort = input("sort", 1);//排序规则 1时间排序 2赞赏总额排序
			$search_text = input("sort", 1);//内容查询
//             if($is_starred_msg > 0){
//                 $condition["is_starred_msg"] = $is_starred_msg;
//             }
			$now = time();
			if ($date == 1) {
				$condition["create_time"] = [ "between", [ $now - 86400 * 5, $now ] ];//最近五天
			} else if ($date == 2) {
				$condition["create_time"] = [ "between", [ $now - 86400 * 1, $now ] ];//今天
			} else if ($date == 3) {
				$condition["create_time"] = [ "between", [ $now - 86400 * 2, $now ] ];//昨天
			} else if ($date == 4) {
				$condition["create_time"] = [ "between", [ $now - 86400 * 3, $now ] ];//前天
			}
			$order = input('order', '');
			$wechat_model = new Wechat();
			$leavemsg_list = $wechat_model->getMaterialPageList($condition, $page, $limit, $order, $field);
			foreach ($leavemsg_list["data"]["list"] as $k => $v) {
//                 var_dump(json_decode($v["value"], true));
				$leavemsg_list["data"]["list"][ $k ]["value"] = json_decode($v["value"]);
			}
			return $leavemsg_list;
		}
		$this->assign("is_starred_msg", $is_starred_msg);
		return $this->fetch('leavemsg/index', [], $this->replace);
	}
	
	public function receiveLeavemsg()
	{
		$postxml = file_get_contents('php://input');//读取 POST 的原始数据
		$data = simplexml_load_string($postxml, 'SimpleXMLElement', LIBXML_NOCDATA);//解析xml,解析后为对象格式l
		
	}
	
	/**
	 * 获取微信用户详情
	 */
	public function getWechatUserinfo()
	{
		$openid = input("openid", "");
		$weixin = new Weixin("public");
		$obj = $weixin->getWechatFansInfo($openid);
		return success($obj);
	}
	
	/**
	 * 回复消息
	 */
	public function sendWechatCustomservicesMessage()
	{
		if (IS_AJAX) {
			$message = input("message", "");
			$open_id = input("open_id", "");
			$weixin = new Weixin('public');
			$res = $weixin->sendWechatCustomservicesMessage($message, $open_id);
			if (isset($res["errcode"]) && $res["errcode"] < 0) {
				return error($res, 'SAVE_FAIL');
			} else {
				return success($res, "SAVE_SUCCESS");
			}
		}
	}
}