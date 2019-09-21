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
use addon\module\Article\common\model\Comment as CommentModel;

class Comment extends BaseSiteHome
{
	
    public $comment_model;
	protected $replace = [];
	
	public function __construct()
	{
		parent::__construct();
		$this->replace = [
			'ARTICLE_CSS' => __ROOT__ . '/addon/module/Article/sitehome/view/public/css',
			'ARTICLE_JS' => __ROOT__ . '/addon/module/Article/sitehome/view/public/js',
			'ARTICLE_IMG' => __ROOT__ . '/addon/module/Article/sitehome/view/public/img',
		];
		$this->comment_model = new CommentModel();
	}
	
	/**
	 * 评论列表
	 * @return \think\mixed
	 */
	public function commentList()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$condition['ncc.site_id'] = $this->siteId;
			$order = 'ncc.create_time desc';
			
			$field = 'ncc.*, nca.title';
			$join = [
			    [
			        'nc_article nca',
			        'ncc.article_id = nca.article_id',
			        'left'
			    ]
			];
			
			$list = $this->comment_model->getCommentPageList($condition, $page, $limit, $order, $field, "ncc", $join);
			return $list;
		}
		return $this->fetch('comment/comment_list', [], $this->replace);
	}
	
	/**
	 * 审核评论
	 */
	public function auditComment(){
	    if (IS_AJAX) {
	        $comment_id = input('comment_id', 0);
	        $condition = array(
	            "site_id" => $this->siteId,
	            "id" => $comment_id,
	        );
	        $data = array(
	            "status" => 1,
	            "audit_time" => time(),
	        );
	        $res = $this->comment_model->editComment($data, $condition);
	        return $res;
	    }
	}
	
	
	
	/**
	 * 删除评论
	 */
	public function deleteComment()
	{
		if (IS_AJAX) {
		    $comment_id = input('comment_id', 0);
		    $condition = array(
		        "site_id" => $this->siteId,
		        "id" => $comment_id,
		    );
			$res = $this->comment_model->deleteComment($condition);
			return $res;
		}
	}
	
}