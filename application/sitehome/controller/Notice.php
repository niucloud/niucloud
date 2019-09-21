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
namespace app\sitehome\controller;

use app\common\controller\BaseSiteHome;
use app\common\model\Notice as NoticeModel;

class Notice extends BaseSiteHome
{
	private $notice_model;
	
	public function __construct()
	{
		parent::__construct();
		$this->notice_model = new NoticeModel();
	}
	
	/**
	 * 公告管理
	 * @return \think\mixed
	 */
	public function index()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$condition['site_id'] = $this->siteId;
			$order = 'set_top desc, create_time desc';
			$list = $this->notice_model->getSiteNoticePageList($condition, $page, $limit, $order);
			return $list;
		}
		return $this->fetch('notice/index');
	}
	
	/**
	 * 公告add
	 */
	public function addNotice()
	{
		if (IS_AJAX) {
			$data = [
				'site_id' => SITE_ID,
				'title' => input('title', ''),
				'content' => input('content', ''),
				'set_top' => input('set_top', 0),
				'source' => input('source', ''),
				'source_url' => input('source_url', ''),
				'create_time' => time(),
				'sort' => input('sort', 0)
			];
			$res = $this->notice_model->addSiteNotice($data);
			return $res;
		} else {
			return $this->fetch('notice/edit_notice');
		}
	}
	
	/**
	 * 公告编辑
	 */
	public function editNotice()
	{
		$id = input('id', 0);
		$condition = [
			'id' => $id,
			'site_id' => SITE_ID
		];
		if (IS_AJAX) {
			$data = [
				'site_id' => SITE_ID,
				'title' => input('title', ''),
				'content' => input('content', ''),
				'set_top' => input('set_top', 0),
				'source' => input('source', ''),
				'source_url' => input('source_url', ''),
				'sort' => input('sort', 0)
			];
			$res = $this->notice_model->editSiteNotice($data, $condition);
			return $res;
		} else {
			$info = $this->notice_model->getSiteNoticeInfo($condition);
			$this->assign('info', $info['data']);
			echo $this->fetch('notice/edit_notice');
		}
	}
	
	/**
	 * 公告排序修改
	 */
	public function sortNotice()
	{
		$id = input('id', 0);
		$sort = input('sort', 0);
		$res = $this->notice_model->editSiteNotice([ 'sort' => $sort ], [ 'id' => $id, 'site_id' => SITE_ID ]);
		return $res;
	}
	
	/**
	 * 公告删除
	 * @return string[]|mixed[]
	 */
	public function deleteNotice()
	{
		if (IS_AJAX) {
			$id = input('id', '');
			$res = $this->notice_model->deleteSiteNotice([ 'id' => [ 'in', $id ], 'site_id' => SITE_ID ]);
			return $res;
		}
	}
	
	/**
	 * 公告置顶
	 */
	public function modifySiteNoticeTop()
	{
		$id = input('id', '');
		$res = $this->notice_model->modifySiteNoticeTop([ 'id' => $id, 'site_id' => SITE_ID ]);
		return $res;
	}
}