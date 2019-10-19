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
 * 评论管理
 * @author Administrator
 *
 */
class Comment
{
	/**
	 * 添加评论
	 * @param array $data
	 */
	public function addComment($data)
	{
		$member_model = new Member();
		$member_info_result = $member_model->getMemberInfo([ "member_id" => $data["member_id"], "site_id" => $data['site_id'] ]);//用户信息
		$member_info = $member_info_result["data"];
		//会员详情
		if (empty($member_info))
			return error();
		
		$article_model = new Article();
		$data["nick_name"] = $member_info["nick_name"];
		
		$to_member_id = 0;//被评论的用户id
		$floor = 0;
		$to_nick_name = "";
		if (!empty($data["to_comment_id"])) {
			$comment_info_result = $this->getCommentInfo([ "id" => $data["to_comment_id"], "article_id" => $data["article_id"], "site_id" => $data['site_id'] ]);//被回复的评论楼层详情
			$comment_info = $comment_info_result["data"];
			//评论详情
			
			if (empty($comment_info))
				return error();
			
			$to_member_id = $comment_info["member_id"];
			$to_member_info_result = $member_model->getMemberInfo([ "member_id" => $to_member_id, "site_id" => $data['site_id'] ]);//用户信息
			$to_member_info = $to_member_info_result["data"];
			
			//会员详情
			if (empty($to_member_info))
				return error();
			
			$to_nick_name = $to_member_info["nick_name"];
			$floor += $comment_info["floor"];
		}
		$data["to_member_id"] = $to_member_id;
		$data["floor"] = $floor;
		$data["to_nick_name"] = $to_nick_name;
		$id = model('nc_article_comment')->add($data);
		
		if ($id !== false) {
			$article_model->gainArticleCommentCount([ "article_id" => $data["article_id"] ]);//增加评论量
			$article_model->editArticle([ "last_comment_time" => time(), "article_id" => $data["article_id"] ]);//修改最后一次评论时间
			
		}

// 		hook("sendMessage",["keyword" => "COMMENT_SUCCESS", "site_id" => $data['site_id'], "id" => $id]);
		return success($id);
	}
	
	/**
	 * 修改评论
	 * @param array $data
	 */
	public function editComment($data, $condition)
	{
		$res = model('nc_article_comment')->update($data, $condition);
		if ($res === false) {
			return error();
		}
		return success($res);
	}
	
	/**
	 * 获取评论详情
	 * @param int $discount_id
	 * @return multitype:string mixed
	 */
	public function getCommentInfo($condition = [], $field = "*", $alias = 'a', $join = null, $data = null)
	{
		$res = model('nc_article_comment')->getInfo($condition, $field, $alias, $join, $data);
		return success($res);
	}
	
	/**
	 * 获取评论分类列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param number $limit
	 */
	public function getCommentList($condition = [], $field = '*', $order = '', $limit = null, $alias = 'a', $join = [], $group = '')
	{
		$model = model('nc_article_comment');
		$res = $model->getList($condition, $field, $order, $alias, $join, $group, $limit);
		return success($res);
	}
	
	/**
	 * 获取评论分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getCommentPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*', $alias = '', $join = [], $group = '')
	{
		$list = model('nc_article_comment')->pageList($condition, $field, $order, $page, $page_size, $alias, $join, $group);
		return success($list);
	}
	
	/**
	 * 删除评论
	 * @param unknown $coupon_type_id
	 */
	public function deleteComment($condition)
	{
		$res = model('nc_article_comment')->delete($condition);
		return success($res);
	}
	
	/**
	 * 评论统计
	 * @param $condition
	 * @return \multitype
	 */
	public function getCommentCount($condition)
	{
		$model = model('nc_article_comment');
		$count = $model->getCount($condition);
		return success($count);
	}
}