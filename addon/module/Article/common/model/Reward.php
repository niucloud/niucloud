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

namespace addon\module\Article\common\model;

use app\common\model\Member;

/**
 * 赞赏管理
 * @author Administrator
 *
 */
class Reward
{
	/**
	 * 添加赞赏
	 * @param array $data
	 */
	public function addReward($data)
	{
		$res = model('nc_article_reward_list')->add($data);
		return success($data["out_trade_no"]);
	}
	
	/**
	 * 修改赞赏
	 * @param array $data
	 */
	public function editReward($data, $condition)
	{
		$res = model('nc_article_reward_list')->update($data, $condition);
		if ($res === false) {
			return error();
		}
		return success($res);
	}
	
	/**
	 * 获取赞赏详情
	 * @param int $discount_id
	 * @return multitype:string mixed
	 */
	public function getRewardInfo($condition = [], $field = "*")
	{
		$res = model('nc_article_reward_list')->getInfo($condition, $field);
		return success($res);
	}
	
	/**
	 * 获取赞赏分类列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param number $limit
	 */
	public function getRewardList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$model = model('nc_article_reward_list');
		$res = $model->getList($condition, $field, $order, $alias = 'a', $join = [], $group = '', $limit);
		return success($res);
	}
	
	/**
	 * 获取赞赏分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getRewardPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*', $alias = '', $join = [], $group = '')
	{
		$list = model('nc_article_reward_list')->pageList($condition, $field, $order, $page, $page_size, $alias, $join, $group);
		return success($list);
	}
	
	/**
	 * 删除赞赏
	 * @param unknown $coupon_type_id
	 */
	public function deleteReward($condition)
	{
		$res = model('nc_article_reward_list')->delete($condition);
		return success($res);
	}
	
	/**
	 * 赞赏账户抵扣支付
	 * @param $data
	 * @return \multitype
	 */
	public function rewardPay($data)
	{
		model('nc_article_reward_list')->startTrans();
		try {
			//如果是非在线支付,则用账户金额抵扣
			$reward_info_result = $this->getRewardInfo([ "site_id" => $data["site_id"], "out_trade_no" => $data["out_trade_no"] ]);
			$reward_info = $reward_info_result["data"];
			//判断是否已支付
			
			if ($reward_info["status"] == 1)
				return error();
			
			
			$article_model = new Article();
			$article_info_result = $article_model->getArticleInfo([ "article_id" => $reward_info["article_id"], "site_id" => $data["site_id"] ]);
			$article_info = $article_info_result["data"];
			
			$member_model = new Member();
			$param = array(
				"member_id" => $data["member_id"],
				"money" => $reward_info["money"],
				"site_id" => $data["site_id"],
				"remark" => "文章" . $article_info["title"] . "的赞赏"
			);
			$result = $member_model->addPayAccount($param);
			
			if ($result["code"] != 0) {
				model('nc_article_reward_list')->rollback();
				return $result;
			}
			$res = $this->editReward([ "status" => 1 ], [ "site_id" => $data["site_id"], "out_trade_no" => $data["out_trade_no"] ]);
			model('nc_article_reward_list')->commit();
			return $res;
		} catch (\Exception $e) {
			model('nc_article_reward_list')->rollback();
			return error('', $e->getMessage());
		}
	}
}