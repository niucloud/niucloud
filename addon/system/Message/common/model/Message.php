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

namespace addon\system\Message\common\model;

/**
 * 消息记录
 * @author lzw
 */
class Message
{
	
	/**
	 * 添加消息类型
	 * @param $data
	 * @return array
	 */
	public function addMessageType($data)
	{
		$id = model("nc_message_type")->add($data);
		if ($id === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($id);
	}
	
	/**
	 * 批量添加消息类型
	 * @param $data
	 * @return array
	 */
	public function addMessageTypeList($data)
	{
		$id = model("nc_message_type")->addList($data);
		if ($id === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($id);
	}
	
	/**
	 * 安装模板消息
	 * @param unknown $data
	 */
	public function installMessage($data){
	    
	    if(!empty($data)){
	        foreach($data as $k => $v){
	            $this->deleteMessageType(["keyword" => $v["keyword"]]);
	            $this->addMessageType($v);
	        }
	    }
	}
	/**
	 * 编辑商品类型
	 * @param $data
	 * @param $condition
	 * @return array
	 */
	public function editMessageType($data, $condition)
	{
	    
		$res = model("nc_message_type")->update($data, $condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 删除消息类型
	 * @param $condition
	 * @return array
	 */
	public function deleteMessageType($condition)
	{
		$res = model("nc_message_type")->delete($condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 查询消息类型信息
	 * @param $condition
	 * @param string $field
	 * @return array
	 */
	public function getMessageTypeInfo($condition, $field = "*")
	{
		$res = model("nc_message_type")->getInfo($condition, $field);
		
		if (empty($res)) {
			return error('', 'UNKNOW_ERROR');
		}
		
		//转义变量json
		if (!empty($res['var_json'])) {
			$res['var_json'] = json_decode($res['var_json'], true);
		} else {
			$res['var_json'] = [];
		}
        //转义变量json
        if (!empty($res['wechat_json'])) {
            $res['wechat_json'] = json_decode($res['wechat_json'], true);
        } else {
            $res['wechat_json'] = [];
        }
		return success($res);
	}
	
	/**
	 * 查询消息类型列表
	 * @param $condition
	 * @param string $field
	 * @return array
	 */
	public function getMessageTypeList($condition, $field = "*", $order = "id desc")
	{
		$res = model("nc_message_type")->getList($condition, $field, $order);
		if (empty($res)) {
			return error('', 'UNKNOW_ERROR');
		}
		
		return success($res);
	}
	
	/**
	 * 查询消息类型分页列表
	 * @param array $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @param string $field
	 * @return array
	 */
	public function getMessageTypePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$res = model("nc_message_type")->pageList($condition, $field, $order, $page, $page_size);
		if (empty($res)) {
			return error('', 'UNKNOW_ERROR');
		}
		
		return success($res);
	}
}