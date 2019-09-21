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

namespace app\common\model;


/**
 * 公告管理
 * @author Administrator
 *
 */
class Notice
{
	/**
	 * 添加公告
	 * @param array $data
	 */
	public function addSiteNotice($data)
	{
		if ($data['set_top']) {
			model('nc_web_config_notice')->update([ 'set_top' => 0 ], '1=1');
		}
		$res = model('nc_web_config_notice')->add($data);
		return success($res);
	}
	
	/**
	 * 修改公告
	 * @param array $data
	 */
	public function editSiteNotice($data, $condition)
	{
		if ($data['set_top']) {
			model('nc_web_config_notice')->update([ 'set_top' => 0 ], '1=1');
		}
		$res = model('nc_web_config_notice')->update($data, $condition);
		return success($res);
	}
	
	/**
	 * 获取公告详情
	 */
	public function getSiteNoticeInfo($conditon, $field = "*")
	{
		$res = model('nc_web_config_notice')->getInfo($conditon, $field);
		return success($res);
	}
	
	/**
	 * 获取分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getSiteNoticePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$list = model('nc_web_config_notice')->pageList($condition, $field, $order, $page, $page_size);
		return success($list);
	}
	
	/**
	 * 删除公告
	 * @param array $condition
	 */
	public function deleteSiteNotice($condition)
	{
		$res = model('nc_web_config_notice')->delete($condition);
		return success($res);
	}
	
	/**
	 * 公告置顶
	 * @param array $condition
	 */
	public function modifySiteNoticeTop($condition)
	{
		model('nc_web_config_notice')->update([ 'set_top' => 0 ], '1=1');
		$res = model('nc_web_config_notice')->update([ 'set_top' => 1 ], $condition);
		return success($res);
	}
	
	/*********************************************系统公告模块***********************************************************/
	
	/**
	 * 添加系统公告
	 * @param array $data
	 * @return multitype:string mixed
	 */
	public function addNotice($data)
	{
		
		$notice_info = model('nc_notice')->getInfo([
			'title' => $data['title']
		]);
		if (!empty($notice_info)) {
			return error('', 'ANNOUNCEMENT_TITLE_DUPLICATE');
		}
		$res = model('nc_notice')->add($data);
		if ($res === false) {
			return error($res, 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 编辑系统公告
	 * @param array $data
	 * @return multitype:string mixed
	 */
	public function editNotice($data, $condition)
	{
		$notice_info = model('nc_notice')->getInfo([
			'title' => $data['title'], 'notice_id' => [ 'neq', $condition['notice_id'] ]
		]);
		if (!empty($notice_info)) {
			return error('', 'ANNOUNCEMENT_TITLE_DUPLICATE');
		}
		$res = model('nc_notice')->update($data, $condition);
		if ($res === false) {
			return error($res, 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 获取公告分页列表
	 *
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getNoticePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$list = model('nc_notice')->pageList($condition, $field, $order, $page, $page_size);
		if (!empty($list['list'])) {
			foreach ($list['list'] as $key => $item) {
				if (!empty($item['notice_category_id'])) {
					$list['list'][ $key ]['notice_category_title'] = model('nc_notice_category')->getInfo([ 'notice_category_id' => $item['notice_category_id'] ], "title")['title'];
				}
			}
		}
		return success($list);
	}
	
	/**
	 * 获取公告类型列表
	 *
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function getNoticeCategorPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$order = "sort desc";
		$list = model('nc_notice_category')->pageList($condition, $field, $order, $page, $page_size);
		return success($list);
	}
	
	/**
	 * 设置公告显示状态
	 */
	public function modifyNoticeIsDisplay($notice_id, $status)
	{
		$res = model('nc_notice')->update([ 'is_display' => $status ], [ 'notice_id' => $notice_id ]);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	
	/**
	 * 设置公告推荐
	 */
	public function modifyNoticeIsRecommend($notice_id, $status)
	{
		$res = model('nc_notice')->update([ 'is_recommend' => $status ], [ 'notice_id' => $notice_id ]);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 公告分类
	 */
	public function getNoticeCategoryList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$list = model('nc_notice_category')->getList($condition, $field, $order, '', '', '', $limit);
		return success($list);
	}
	
	/**
	 * 获取公告详情
	 */
	public function getNoticeInfo($conditon, $field = "*")
	{
		$res = model('nc_notice')->getInfo($conditon, $field);
		return success($res);
	}
	
	/**
	 * 删除系统公告 $notice_id
	 */
	public function deleteNotice($notice_id)
	{
		$notice_id = isset($notice_id) ? $notice_id : '';
		if ($notice_id === '') {
			return error('', '缺少必须参数notice_id');
		}
		$res = model('nc_notice')->delete([ 'notice_id' => $notice_id ]);
		if ($res === false) {
			return error('', 'DELETE_FAIL');
		}
		return success($res);
	}
	
	/**
	 * 删除系统公告分类 $notice_id
	 */
	public function deleteNoticeCategory($notice_category_id)
	{
		$notice_category_id = isset($notice_category_id) ? $notice_category_id : '';
		if ($notice_category_id === '') {
			return error('', '缺少必须参数notice_id');
		}
		$res = model('nc_notice_category')->delete([ 'notice_category_id' => $notice_category_id ]);
		if ($res === false) {
			return error('', 'DELETE_FAIL');
		}
		return success($res);
	}
	
	
	/**
	 * 添加系统公告
	 * @param array $data
	 * @return multitype:string mixed
	 */
	public function addNoticeCategory($data)
	{
		$res = model('nc_notice_category')->add($data);
		return success($res);
	}
	
	/**
	 * 编辑系统公告分类
	 * @param array $data
	 * @return multitype:string mixed
	 */
	public function editCategoryTitle($data, $condition)
	{
		$res = model('nc_notice_category')->update($data, $condition);
		if ($res === false) {
			return error($res, 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/*********************************************系统公告模块***********************************************************/
}