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

namespace addon\module\Article\sitehome\controller;

use app\common\controller\BaseSiteHome;
use addon\module\Article\common\model\Reward as RewardModel;

class Reward extends BaseSiteHome
{
	
	public $reward_model;
	protected $replace = [];
	
	public function __construct()
	{
		parent::__construct();
		$this->replace = [
			'ARTICLE_CSS' => __ROOT__ . '/addon/module/Article/sitehome/view/public/css',
			'ARTICLE_JS' => __ROOT__ . '/addon/module/Article/sitehome/view/public/js',
			'ARTICLE_IMG' => __ROOT__ . '/addon/module/Article/sitehome/view/public/img',
		];
		$this->reward_model = new RewardModel();
	}
	
	/**
	 * 评论列表
	 * @return \think\mixed
	 */
	public function rewardList()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$condition = array(
				'ncar.site_id' => $this->siteId,
				'ncar.status' => 1
			);
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
			
			$list = $this->reward_model->getRewardPageList($condition, $page, $limit, $order, $field, "ncar", $join);
			return $list;
		}
		return $this->fetch('reward/reward_list', [], $this->replace);
	}
	
}