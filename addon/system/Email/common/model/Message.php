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
 * 邮箱消息模板
 */
class Message
{
	
	/**
	 * 添加邮箱消息模板
	 * @param $data
	 * @return array
	 */
	public function addEmailMessage($data)
	{
		$id = model("nc_site_message_email")->add($data);
		if ($id === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($id);
	}
	
	/**
	 * 批量添加邮箱消息模板
	 * @param $data
	 * @return array
	 */
	public function addEmailMessageList($data)
	{
		$id = model("nc_site_message_email")->addList($data);
		if ($id === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($id);
	}
	
	/**
	 * 编辑邮箱消息模板
	 * @param $data
	 * @param $condition
	 * @return array
	 */
	public function editEmailMessage($data, $condition)
	{
		$res = model("nc_site_message_email")->update($data, $condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	public function modifyEmailMessage($condition)
	{
	
	}
	
	/**
	 * 删除邮箱消息模板
	 * @param $condition
	 * @return array
	 */
	public function deleteEmailMessage($condition)
	{
		$res = model("nc_site_message_email")->delete($condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 查询邮箱消息模板信息
	 * @param $condition
	 * @param string $field
	 * @return array
	 */
	public function getEmailMessageInfo($condition, $field = "*")
	{
		$res = model("nc_site_message_email")->getInfo($condition, $field);
		if (empty($res)) {
            $data = array(
                'site_id' => $condition["site_id"],
                'keyword' => $condition['keyword']
            );
            $this->addEmailMessage($data);
            $res = model("nc_site_message_email")->getInfo($condition, $field);
		}
		return success($res);
	}
	
	/**
	 * 查询邮箱消息模板列表
	 * @param $condition
	 * @param string $field
	 * @return array
	 */
	public function getEmailMessageList($condition, $field = "*")
	{
		$res = model("nc_site_message_email")->getList($condition, $field);
		if (empty($res)) {
			return error([], 'UNKNOW_ERROR');
		}
		
		return success($res);
	}
	
	/**
	 * 查询邮箱消息模板分页列表
	 * @param array $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @param string $field
	 * @return array
	 */
	public function getEmailMessagePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$res = model("nc_site_message_email")->pageList($condition, $field, $order, $page, $page_size);
		if (empty($res)) {
			return error([], 'UNKNOW_ERROR');
		}
		
		return success($res);
	}
	
	public function deleteSite($site_id)
	{
	    model("nc_site_message_email")->delete(['site_id' => $site_id]);
	    model("nc_site_message_email_list")->delete(['site_id' => $site_id]);
	    return success();
	}
	
}