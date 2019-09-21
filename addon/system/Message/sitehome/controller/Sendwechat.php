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
namespace addon\system\Message\sitehome\controller;

use addon\system\Member\common\model\Member;
use addon\system\Wechat\common\model\Wechat;
use util\weixin\Weixin;

class Sendwechat extends Base
{
	public function send()
	{
		if (IS_AJAX) {
			$label_id = input('type', '');
			$content = input('content', '');
			$member = new Member();
			$condition = [];
			if ($label_id) {
				$condition[] = [
					'exp',
					"CONCAT(',', member_label,',') like '%," . $label_id . ",%'"
				];
			}
			$condition['site_id'] = SITE_ID;
			$member_list = $member->getMemberList($condition);
			$wx_openid = [];
			foreach ($member_list['data'] as $v) {
				$wx_openid[] = $v['wx_openid'];
			}
			$wx_openid = json_encode($wx_openid);
			$wx_arr = "{
                 'touser' : $wx_openid,
                 'msgtype' : 'text',
                 'text': {'content' : $content}
            }";
			$wx = new Weixin('public');
			$res = $wx->messageGroupSend($wx_arr);
			//追加记录
			$status = ($res['errcode'] > 0) ? 1 : 0;
			$this->addSendRecording(SITE_ID, $status, $label_id, 0, json_encode($res), $content);
			return $res;
		}
		$member = new Member();
		$list = $member->getMemberLabelList([ 'site_id' => $this->siteId ]);
		$this->assign('member_lable_list', $list);
		return $this->fetch('sendwechat/send', [], $this->replace);
	}
	
	public function getLabelNumber()
	{
		if (IS_AJAX) {
			$id = input('id', '');
			$member = new Member();
			if ($id) {
				$condition[] = [
					'exp',
					"CONCAT(',', member_label,',') like '%," . $id . ",%'"
				];
			}
			$condition['site_id'] = $this->siteId;
			$count = $member->getMemberCount($condition);
			return $count;
		}
	}
	
	/**
	 * 添加群发记录
	 *
	 */
	public function addSendRecording($site_id, $status, $label_id, $media_id, $res, $content)
	{
		$conidtion = [
			'site_id' => $site_id,
			'status' => $status,
			'create_time' => time(),
			'member_label' => $label_id,
			'media_id' => $media_id,
			'err' => $res,
			'content' => $content,
		];
		$res = model('nc_wechat_information_list')->add($conidtion, true);
		return $res;
	}
	
	public function getMediaInfo()
	{
		$media_id = input('id', 0);
		$wechat_model = new Wechat();
		$media_info = $wechat_model->getMaterialInfo([ 'id' => $media_id ]);
		$media_info['data']['value'] = json_decode($media_info['data']['value'], true);
		return $media_info['data']['value'];
	}
	
	public function wechatRecordingList()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$status = input('wechat_status', 0);
			$limit = input('limit', PAGE_LIST_ROWS);
			$condition = [];
			if ($status) {
				$condition['nwir.status'] = $status;
			}
			$join = [
				[
					'nc_member_label nml',
					'nwir.member_label = nml.label_id',
					'left'
				]
			];
			$condition['nwir.site_id'] = SITE_ID;
			$field = 'nwir.*,nml.label_name';
			$list = $this->getWechatRecordingList($condition, $field, 'nwir.id desc', $page, $limit, 'nwir', $join);
			if (!empty($list['data']['list']) && is_array($list['data']['list'])) {
				foreach ($list['data']['list'] as $k => $v) {
					if (!empty($v['value']) && json_decode($v['value'])) {
						$list['data']['list'][ $k ]['value'] = json_decode($v['value'], true);
					}
				}
			}
			return $list;
		}
	}
	
	/**
	 * 获取微信群发记录
	 */
	public function getWechatRecordingList($where = [], $field = true, $order = '', $page = 1, $list_rows = PAGE_LIST_ROWS, $alias = 'a', $join = [], $group = null, $limit = null)
	{
		$list = model('nc_wechat_information_list')->pageList($where, $field, $order, $page, $list_rows, $alias, $join, $group, $limit);
		return success($list);
	}
}