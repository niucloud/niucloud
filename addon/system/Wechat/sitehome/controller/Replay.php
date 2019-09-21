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
use app\common\model\DiyView;

/**
 * 微信回复控制器
 */
class Replay extends Base
{
	/**
	 * 回复设置--关键字自动回复
	 */
	public function index()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$rule_type = input('rule_type', '');
			$search_text = input('search_text', '');
			
			$condition = array(
				'site_id' => SITE_ID,
				'rule_type' => $rule_type
			);
			
			$condition['rule_name'] = [
				'like',
				'%' . $search_text . '%'
			];
			
			$order = 'create_time desc';
			$wechat_model = new Wechat();
			$list = $wechat_model->getReplayPageList($condition, $page, $limit, $order);
			foreach ($list['data']['list'] as $k => $v) {
				$list['data']['list'][ $k ]['key_list'] = $v['keywords_json'] != false ? json_decode($v['keywords_json']) : [];
				$list['data']['list'][ $k ]['replay_list'] = $v['replay_json'] != false ? json_decode($v['replay_json']) : [];
			}
			return $list;
		} else {
			$div_model = new DiyView();
			$list = $div_model->getDiyLinkList();
			$this->assign('list', $list['data']);
			return $this->fetch('Replay/index', [], $this->replace);
		}
	}
	
	/**
	 * 添加或修改关键字回复
	 */
	public function addOrEditRule()
	{
		$wechat_model = new Wechat();
		if (IS_AJAX) {
			$rule_id = input('rule_id', '');
			$rule_name = input('rule_name', '');
			if ($rule_id > 0) {
				$data = [
					'rule_name' => $rule_name,
					'modify_time' => time()
				];
				$res = $wechat_model->editRule($data, [ 'rule_id' => $rule_id ]);
			} else {
				$data = [
					'site_id' => SITE_ID,
					'rule_name' => $rule_name,
					'rule_type' => 'KEYWORDS',
					'keywords_json' => '',
					'replay_json' => '',
					'create_time' => time()
				];
				$res = $wechat_model->addRule($data);
			}
			return $res;
		}
	}
	
	/**
	 * 删除关键字回复
	 */
	public function delRule()
	{
		if (IS_AJAX) {
			$rule_id = input('rule_id', 0);
			$wechat_model = new Wechat();
			$res = $wechat_model->deleteRule([ 'rule_id' => $rule_id ]);
			return $res;
		}
	}
	
	/**
	 * 添加或者修改关键词数据
	 */
	public function addOrEditKeywords()
	{
		$wechat_model = new Wechat();
		if (IS_AJAX) {
			$rule_id = input('rule_id', '');
			$info = $result = $wechat_model->getRuleInfo([ 'rule_id' => $rule_id ]);
			$keywords_name = input('keywords_name', '');
			$keywords_type = input('keywords_type', 0);
			$key_id = input('key_id', -1);
			if ($info['data']['keywords_json']) {
				$data = json_decode($info['data']['keywords_json']);
				if ($key_id > -1) {
					$data[ $key_id ] = [
						'keywords_name' => $keywords_name,
						'keywords_type' => $keywords_type
					];
				} else {
					array_push($data, [
						'keywords_name' => $keywords_name,
						'keywords_type' => $keywords_type
					]);
				}
				$data = json_encode($data);
			} else {
				$data = [
					[
						'keywords_name' => $keywords_name,
						'keywords_type' => $keywords_type
					]
				];
				$data = json_encode($data);
			}
			$data = [
				'keywords_json' => $data,
				'modify_time' => time()
			];
			$res = $wechat_model->editRule($data, [ 'rule_id' => $rule_id ]);
			return $res;
		}
	}
	
	/**
	 * 删除关键词数据
	 */
	public function delKeywords()
	{
		$wechat_model = new Wechat();
		if (IS_AJAX) {
			$rule_id = input('rule_id', '');
			$key_id = input('key_id', '');
			$info = $result = $wechat_model->getRuleInfo([ 'rule_id' => $rule_id ]);
			if ($info['data']['keywords_json']) {
				$data = json_decode($info['data']['keywords_json']);
				array_splice($data, $key_id, 1);
				$data = json_encode($data);
			}
			$data = [
				'keywords_json' => $data,
			];
			$res = $wechat_model->editRule($data, [ 'rule_id' => $rule_id ]);
			return $res;
		}
	}
	
	/**
	 * 添加或修改回复数据
	 */
	public function addOrEditReplays()
	{
		$wechat_model = new Wechat();
		if (IS_AJAX) {
			$rule_id = input('rule_id', '');
			$reply_content = input('reply_content', '');
			$media_id = input('media_id', '');
			$key_id = input('key_id', -1);
			$type = input('type', '');
			$info = $result = $wechat_model->getRuleInfo([ 'rule_id' => $rule_id ]);
			if ($info['data']['replay_json']) {
				$data = json_decode($info['data']['replay_json']);
				
				if ($key_id > -1) {
					$data[ $key_id ] = [
						'reply_content' => $reply_content,
						'type' => $type,
					];
					if (!empty($media_id)) {
						$data[ $key_id ]['media_id'] = $media_id;
					}
				} else {
					if (!empty($media_id)) {
						array_push($data, [
							'reply_content' => $reply_content,
							'type' => $type,
							'media_id' => $media_id
						]);
					} else {
						array_push($data, [
							'reply_content' => $reply_content,
							'type' => $type,
						]);
					}
				}
			} else {
				$data = [
					[
						'reply_content' => $reply_content,
						'type' => $type,
					]
				];
				if (!empty($media_id)) {
					$data[0]['media_id'] = $media_id;
				}
			}
			$data = json_encode($data);
			$data = [
				'replay_json' => $data,
				'modify_time' => time()
			];
			$res = $wechat_model->editRule($data, [ 'rule_id' => $rule_id ]);
			return $res;
		}
	}
	
	/**
	 * 删除回复数据
	 */
	public function delReply()
	{
		$wechat_model = new Wechat();
		if (IS_AJAX) {
			$rule_id = input('rule_id', '');
			$key_id = input('key_id', '');
			$info = $result = $wechat_model->getRuleInfo([ 'rule_id' => $rule_id ]);
			if ($info['data']['replay_json']) {
				$data = json_decode($info['data']['replay_json']);
				array_splice($data, $key_id, 1);
				$data = json_encode($data);
			}
			$data = [
				'replay_json' => $data,
			];
			$res = $wechat_model->editRule($data, [ 'rule_id' => $rule_id ]);
			return $res;
		}
	}
	
	/**
	 * 关注后回复设置
	 */
	public function afterAttention()
	{
		return $this->fetch('Replay/aterAttention', [], $this->replace);
	}
	
	/**
	 * 回复设置--关注后自动回复
	 */
	public function follow()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$rule_type = input('rule_type', '');
			$condition = array(
				'site_id' => SITE_ID,
				'rule_type' => $rule_type
			);
			$order = 'create_time desc';
			$wechat_model = new Wechat();
			$list = $wechat_model->getReplayPageList($condition, $page, $limit, $order);
			foreach ($list['data']['list'] as $k => $v) {
				$list['data']['list'][ $k ]['key_list'] = $v['keywords_json'] != false ? json_decode($v['keywords_json']) : [];
				$list['data']['list'][ $k ]['replay_list'] = $v['replay_json'] != false ? json_decode($v['replay_json']) : [];
			}
			return $list;
		} else {
			$div_model = new DiyView();
			$list = $div_model->getDiyLinkList();
			$this->assign('list', $list['data']);
			return $this->fetch('Replay/follow_list', [], $this->replace);
		}
	}
}