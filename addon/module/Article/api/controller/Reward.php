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
namespace addon\module\Article\api\controller;

use addon\module\Article\common\model\Reward as RewardModel;
use app\common\controller\BaseApi;

/**
 * 赞赏 控制器
 */
class Reward extends BaseApi
{
	
	/**
	 * 赞赏
	 * @param array $params
	 */
	public function reward($params)
	{
		$member_id = $this->checkAccessToken($params['site_id'], $params['access_token']);//通过token获取用户会员id
		if (empty($member_id))
			return error([], 'NOT_LOGIN');
		
		$out_trade_no = hook("getOutTradeNo", [], [], true);
		$data = array(
			"article_id" => $params["article_id"],
			"member_id" => $member_id,
			"money" => $params["money"],
			"create_time" => time(),
			"out_trade_no" => $out_trade_no,
			"site_id" => $params['site_id'],
//			"pay_type" => $params["pay_type"]
		);
		$reward_model = new RewardModel();
		$result = $reward_model->addReward($data);
		return $result;
		
	}
	
	/**
	 * 赞赏详情
	 * @param array $params
	 */
	public function getRewardInfo($params)
	{
		$reward_model = new RewardModel();
		$condition = [];
		$condition['out_trade_no'] = $params['out_trade_no'];
		$article_detail = $reward_model->getRewardInfo($condition);
		return $article_detail;
	}
	
	/**
	 * 赞赏列表
	 * @param array $params
	 */
	public function getRewardPageList($params)
	{
		$member_id = $this->checkAccessToken($params['site_id'], $params['access_token']);//通过token获取用户会员id
		if (empty($member_id))
			return error([], 'NOT_LOGIN');
		
		$reward = new RewardModel();
		
		$condition = json_decode($params['condition'], true);
		if (!empty($member_id)) {
			$condition["ncar.member_id"] = $member_id;
		}
		$condition['ncar.site_id'] = $params['site_id'];
		$page = isset($params['page']) ? $params['page'] : '1';
		$page_size = isset($params['page_size']) ? $params['page_size'] : PAGE_LIST_ROWS;
		$order = isset($params['order']) ? $params['order'] : 'create_time desc';
		
		$order = 'ncar.pay_time desc';
		
		$field = 'ncar.*, nca.title, nm.nick_name';
		$join = [
			[
				'nc_article nca',
				'ncar.article_id = nca.article_id',
				'left'
			],
			[
				'nc_member nm',
				'ncar.member_id = nm.member_id',
				'left'
			]
		];
		$list = $reward->getRewardPageList($condition, $page, $page_size, $order, $field, "ncar", $join);
		return $list;
	}
	
	/**
	 * 账户抵扣赞赏金额
	 * @param $param
	 */
	public function rewardPay($param)
	{
		$member_id = $this->checkAccessToken($param['site_id'], $param['access_token']);//通过token获取用户会员id
		if (empty($member_id))
			return error([], 'NOT_LOGIN');
		
		$reward_model = new RewardModel();
		$param["member_id"] = $member_id;
		$result = $reward_model->rewardPay($param);
		return $result;
	}
	
}