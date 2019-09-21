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

use addon\module\Article\common\model\Comment as CommentModel;
use app\common\controller\BaseApi;

/**
 * 控制器
 */
class Comment extends BaseApi
{

	/**
	 * 评论
	 * @param array $params
	 */
	public function comment($params)
	{

		$member_id = $this->checkAccessToken($params['site_id'], $params['access_token']);//通过token获取用户会员id
		if (empty($member_id))
			return error([], 'NOT_LOGIN');

		$comment_model = new CommentModel();
		$comment_type = !empty($params["comment_type"]) ? $params["comment_type"] : 1;
		$comment_id = !empty($params["comment_id"]) ? $params["comment_id"] : 0;
		$data = array(
			"member_id" => $member_id,
			"to_comment_id" => $comment_id,
			"content" => $params["content"],
			"comment_type" => $comment_type,
			"article_id" => $params["article_id"],
			"create_time" => time(),
			"site_id" => $params['site_id']
		);
		$list = $comment_model->addComment($data);
		return $list;
	}

	/**
	 * 点赞
	 * @param unknown $params
	 */
	public function likeComment($params)
	{
		$member_id = $this->checkAccessToken($params['site_id'], $params['access_token']);//通过token获取用户会员id
		if (empty($member_id))
			return error([]);

	}

	/**
	 * 分类下评论列表
	 * @param array $params
	 */
	public function getCommentPageList($params)
	{
		$member_id = $this->checkAccessToken($params['site_id'], $params['access_token']);//通过token获取用户会员id
		$article = new CommentModel();
		$condition = [];
        $article_id = $params["atticle_id"];
        if (!empty($article_id)) {
            $condition["ncc.article_id"] = $article_id;
        }
		if (!empty($member_id)) {
			$condition["ncc.member_id"] = $member_id;
		}
		$condition['ncc.site_id'] = $params['site_id'];
		$page = isset($params['page']) ? $params['page'] : '1';
		$page_size = isset($params['page_size']) ? $params['page_size'] : PAGE_LIST_ROWS;
		$order =  isset($params['order']) ? $params['order'] : 'ncc.create_time desc';
		$field = 'ncc.*, nca.title';
		$join = [
			[
				'nc_article nca',
				'ncc.article_id = nca.article_id',
				'left'
			]
		];
		$list = $article->getCommentPageList($condition, $page, $page_size, $order, $field, "ncc", $join);
		return $list;
	}

}