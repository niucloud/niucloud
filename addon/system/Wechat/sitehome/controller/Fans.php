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
use think\Session;

/**
 * 微信粉丝控制器
 */
class Fans extends Base
{
	
	public function index()
	{
		$wechat_model = new Wechat();
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$is_subscribe = input('is_subscribe', '');//关注
			$nickname = input('nickname', '');//粉丝名称
			$start_time = input('start_time', '');
			$end_time = input('end_time', '');
			
			$condition['site_id'] = SITE_ID;
			if ($is_subscribe !== '') {
				$condition['is_subscribe'] = $is_subscribe;
			}
			if ($nickname != '') {
				$condition['nickname_decode'] = [ 'like', '%' . $nickname . '%' ];
			}
			if (!empty($start_time) && empty($end_time)) {
				$condition["subscribe_time"] = [ "egt", date_to_time($start_time) ];
			} elseif (empty($start_time) && !empty($end_time)) {
				$condition["subscribe_time"] = [ "elt", date_to_time($end_time) ];
			} elseif (!empty($start_time) && !empty($end_time)) {
				$condition["subscribe_time"] = [ "between", [ date_to_time($start_time), date_to_time($end_time) ] ];
			}
			$fans_list = $wechat_model->getWechatFansList($condition, $page, $limit);
			return $fans_list;
		}
		
		$site_id = request()->siteid();
		$tag_list = $wechat_model->getWechatFansTagPageList([ 'site_id' => $site_id ]);
		$this->assign('tag_list', $tag_list['data']['list']);
		
		return $this->fetch('Fans/index', [], $this->replace);
	}
	
	/**
	 * 更新粉丝信息
	 */
	public function updateWchatFansList()
	{
		$page_index = input('page', 0);
		$page_size = input('limit', PAGE_LIST_ROWS);
		
		$wechat_model = new Wechat();
		if ($page_index == 0) {
			//建立连接，同时获取所有用户openid
			$openid_list = $wechat_model->getWechatOpenidList(SITE_ID);
			
			if ($openid_list['code'] == 0 && !empty($openid_list['data']['total'])) {
				Session::set('site_' . SITE_ID . '_wchat_openid_list', $openid_list['data']['openid_list']);
				
				if ($openid_list['data']['total'] % $page_size == 0) {
					$page_count = $openid_list['data']['total'] / $page_size;
				} else {
					$page_count = (int) ($openid_list['data']['total'] / $page_size) + 1;
				}
				$data = array(
					'total' => $openid_list['data']['total'],
					'page_count' => $page_count,
				);
				return success($data);
			}
			
			return $openid_list;
		} else {
			//对应页数更新用户粉丝信息
			$get_fans_openid_list = Session::get('site_' . SITE_ID . '_wchat_openid_list');
			
			if (empty($get_fans_openid_list)) {
				return error('');
			}
			
			$start = ($page_index - 1) * $page_size;
			$page_fans_openid_list = array_slice($get_fans_openid_list, $start, $page_size);
			
			if (empty($page_fans_openid_list)) {
				return error('');
			}
			
			$str = '{ "user_list" : [';
			foreach ($page_fans_openid_list as $key => $value) {
				$str .= ' {"openid" : "' . $value . '"},';
			}
			
			$openidlist = substr($str, 0, strlen($str) - 1);
			$openidlist .= ']}';
			$result = $wechat_model->updateWechatFansList(SITE_ID, $openidlist);
			return $result;
		}
	}
	
	/**
	 * 微信粉丝标签
	 */
	public function fansTag()
	{
		if (IS_AJAX) {
			$wechat_model = new Wechat();
			$site_id = request()->siteid();
			$list = $wechat_model->getWechatFansTagPageList([ 'site_id' => $site_id ]);
			return $list;
		}
		return $this->fetch('Fans/fans_tag', [], $this->replace);
	}
	
	/**
	 * 为微信粉丝批量打标签
	 */
	public function fansBatchTagging()
	{
		if (IS_AJAX) {
			$wechat_model = new Wechat();
			$site_id = request()->siteid();
			$tagids = input('tag_id', '');
			$openids = input('openid', '');
			if (!empty($openids)) {
				$tag_id_list = explode(',', $tagids);
				$openid_list = explode(',', $openids);
				$data = [
					'site_id' => $site_id,
					'tag_id_list' => $tag_id_list,
					'openid_list' => $openid_list
				];
				$res = $wechat_model->fansBatchTagging($data);
				return $res;
			}
		}
	}
	
	/**
	 * 为微信粉丝打标签
	 */
	public function fansTagging()
	{
		if (IS_AJAX) {
			$wechat_model = new Wechat();
			$site_id = request()->siteid();
			$openid = input('openid', '');
			$tagid_list = input('tagid_list', '');
			$cancel_tagid_list = input('cancel_tagid_list', '');
			if (!empty($openid)) {
				$tagid_list_arr = !empty($tagid_list) ? explode(',', $tagid_list) : [];
				$cancel_tagid_list_arr = !empty($cancel_tagid_list) ? explode(',', $cancel_tagid_list) : [];
				$data = [
					'site_id' => $site_id,
					'tag_id_list' => $tagid_list_arr,
					'openid_list' => [ $openid ]
				];
				$res = $wechat_model->fansBatchTagging($data);
				$data['tag_id_list'] = $cancel_tagid_list_arr;
				$wechat_model->fansBatchUnTagging($data);
				return $res;
			}
		}
	}
	
	/**
	 * 添加标签
	 * @return multitype:string
	 */
	public function addFansTag()
	{
		if (IS_AJAX) {
			$wechat_model = new Wechat();
			$tag_name = input('tag_name', '');
			if (!empty($tag_name)) {
				$data = [
					'tag_name' => $tag_name,
					'site_id' => request()->siteid()
				];
				$res = $wechat_model->addFansTag($data);
				return $res;
			}
		}
	}
	
	/**
	 * 编辑标签
	 */
	public function editFansTag()
	{
		if (IS_AJAX) {
			$wechat_model = new Wechat();
			$id = input('id', '');
			$tag_name = input('tag_name', '');
			$tag_id = input('tag_id', '');
			$site_id = request()->siteid();
			if (!empty($tag_name)) {
				$data = [
					'tag_name' => $tag_name,
					'tag_id' => $tag_id,
					'site_id' => request()->siteid()
				];
				$res = $wechat_model->editFansTag($data, [ 'id' => $id, 'site_id' => $site_id ]);
				return $res;
			}
		}
	}
	
	/**
	 * 删除标签
	 */
	public function deleteFansTag()
	{
		if (IS_AJAX) {
			$wechat_model = new Wechat();
			$id = input('id', '');
			$tag_id = input('tag_id', '');
			$site_id = request()->siteid();
			if ($id) {
				$data = [
					'id' => $id,
					'tag_id' => $tag_id,
					'site_id' => $site_id
				];
				$res = $wechat_model->deleteFansTag($data);
				return $res;
			}
		}
	}
	
	/**
	 * 同步粉丝标签
	 */
	public function syncFansTag()
	{
		if (IS_AJAX) {
			$wechat_model = new Wechat();
			$site_id = request()->siteid();
			$res = $wechat_model->syncFansTag([ 'site_id' => $site_id ]);
			return $res;
		}
		
	}
}