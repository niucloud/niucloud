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

namespace addon\system\Email\common\model;


/**
 * 邮箱消息发送记录
 */
class MessageRecords
{
	
	/**
	 * 新建邮箱发送记录
	 * @param unknown $data
	 */
	public function addEmailMessageRecords($data)
	{
		$id = model("nc_site_message_email_list")->add($data);
		if ($id === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($id);
	}
	
	/**
	 * 批量添加邮箱发送记录
	 * @param $data
	 * @return array
	 */
	public function addEmailMessageRecordsList($data)
	{
		$id = model("nc_site_message_email_list")->addList($data);
		if ($id === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($id);
	}
	
	/**
	 * 编辑邮箱发送记录
	 * @param $data
	 * @param $condition
	 * @return array
	 */
	public function editEmailMessageRecords($data, $condition)
	{
		$res = model("nc_site_message_email_list")->update($data, $condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 删除邮箱发送记录
	 * @param $condition
	 * @return array
	 */
	public function deleteEmailMessageRecords($condition)
	{
		$res = model("nc_site_message_email_list")->delete($condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	
	/**
	 * 查询邮箱发送记录
	 * @param $condition
	 * @param string $field
	 * @return array
	 */
	public function getEmailMessageRecordsInfo($condition, $field = "*")
	{
		$res = model("nc_site_message_email_list")->getInfo($condition, $field);
		if (empty($res)) {
			return error('', 'UNKNOW_ERROR');
		}
		
		return success($res);
	}
	
	/**
	 * 查询邮箱发送记录
	 * @param $condition
	 * @param string $field
	 * @return array
	 */
	public function getEmailMessageRecordsList($condition, $field = "*")
	{
		$res = model("nc_site_message_email_list")->getList($condition, $field);
		if (empty($res)) {
			return error('', 'UNKNOW_ERROR');
		}
		
		return success($res);
	}
	
	/**
	 * 查询邮箱发送记录分页列表
	 * @param array $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @param string $field
	 * @return array
	 */
	public function getEmailMessageRecordsPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$res = model("nc_site_message_email_list")->pageList($condition, $field, $order, $page, $page_size);
		if (empty($res)) {
			return error('', 'UNKNOW_ERROR');
		}
		
		return success($res);
	}
	
}