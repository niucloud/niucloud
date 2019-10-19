<?php

namespace app\api\controller;

use app\common\model\Notice as NoticeModel;
use app\common\controller\BaseApi;

/**
 * 公告api
 * @author Administrator
 *
 */
class Notice extends BaseApi
{
	private $notice_model;
	
	public function __construct($params)
	{
		parent::__construct($params);
		$this->notice_model = new NoticeModel();
	}
	
	/**
	 * 公告管理
	 * @return \think\mixed
	 */
	public function getNoticePageList($params)
	{
		
		$condition['site_id'] = $params['site_id'];
		$page = isset($params['page']) ? $params['page'] : 1;
		$limit = isset($params['limit']) ? $params['limit'] : PAGE_LIST_ROWS;
		$order = isset($params['order']) ? $params['order'] : 'set_top desc, create_time desc';
		
		$list = $this->notice_model->getSiteNoticePageList($condition, $page, $limit, $order);
		return $list;
	}
	
	public function detail($params)
	{
		
		$info = $this->notice_model->getSiteNoticeInfo([ 'id' => $params['id'] ]);
		return $info;
	}
	
}