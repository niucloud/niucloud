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

namespace addon\system\Sms\common\model;

/**
 * 短信消息发送记录
 */
class MessageRecords
{
	
	/**
	 * 添加短信消息发送记录
	 * @param $data
	 * @return array
	 */
	public function addSmsMessageReocrds($data)
	{
		$id = model("nc_site_message_sms_list")->add($data);
		if ($id === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($id);
	}
	
	/**
	 * 批量添加短信消息发送记录
	 * @param $data
	 * @return array
	 */
	public function addSmsMessageReocrdsList($data)
	{
		$id = model("nc_site_message_sms_list")->addList($data);
		if ($id === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($id);
	}
	
	/**
	 * 编辑短信消息发送记录
	 * @param $data
	 * @param $condition
	 * @return array
	 */
	public function editSmsMessageReocrds($data, $condition)
	{
		$res = model("nc_site_message_sms_list")->update($data, $condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 删除短信消息发送记录
	 * @param $condition
	 * @return array
	 */
	public function deleteSmsMessageReocrds($condition)
	{
		$res = model("nc_site_message_sms_list")->delete($condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 查询短信消息发送记录
	 * @param $condition
	 * @param string $field
	 * @return array
	 */
	public function getSmsMessageReocrdsInfo($condition, $field = "*")
	{
		$info = model("nc_site_message_sms_list")->getInfo($condition, $field);
		if (!empty($info)) {
		    if(!empty($info['var_parse'])){
		        $info['var_parse'] = json_decode($info['var_parse'], true);
		    }else{
		        $info['var_parse'] = [];
		    }
		    
		}
		
		return success($info);
	}
	
	/**
	 * 查询短信消息发送记录列表
	 * @param $condition
	 * @param string $field
	 * @return array
	 */
	public function getSmsMessageReocrdsList($condition, $field = "*")
	{
		$res = model("nc_site_message_sms_list")->getList($condition, $field);
		if (empty($res)) {
			return error('', 'UNKNOW_ERROR');
		}
		
		return success($res);
	}
	
	/**
	 * 查询短信消息发送记录分页列表
	 * @param array $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @param string $field
	 * @return array
	 */
	public function getSmsMessageReocrdsPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$res = model("nc_site_message_sms_list")->pageList($condition, $field, $order, $page, $page_size);
		if (empty($res)) {
			return error('', 'UNKNOW_ERROR');
		}

		return success($res);
	}
	
	/**
	 * 查询短信发送记录总数
	 * @param unknown $condition
	 */
	public function getSmsCount($condition)
	{
	    $count = model("nc_site_message_sms_list")->getCount($condition);
	    return success($count);
	}
}