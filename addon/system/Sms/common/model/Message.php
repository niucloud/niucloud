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
 * 短信消息模板
 */
class Message
{
	
	/**
	 * 添加短信消息模板
	 * @param $data
	 * @return array
	 */
	public function addSmsMessage($data)
	{
		$id = model("nc_site_message_sms")->add($data);
		if ($id === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($id);
	}
	
	/**
	 * 批量添加短信消息模板
	 * @param $data
	 * @return array
	 */
	public function addSmsMessageList($data)
	{
		$id = model("nc_site_message_sms")->addList($data);
		if ($id === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($id);
	}
	
	/**
	 * 编辑商品类型
	 * @param $data
	 * @param $condition
	 * @return array
	 */
	public function editSmsMessage($data, $condition)
	{
		$res = model("nc_site_message_sms")->update($data, $condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	public function modifySmsMessage($condition)
	{
	
	}
	
	/**
	 * 删除短信消息模板
	 * @param $condition
	 * @return array
	 */
	public function deleteSmsMessage($condition)
	{
		$res = model("nc_site_message_sms")->delete($condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 查询短信消息模板信息
	 * @param $condition
	 * @param string $field
	 * @return array
	 */
	public function getSmsMessageInfo($condition, $field = "*")
	{
		$info = model("nc_site_message_sms")->getInfo($condition, $field);
		if(empty($info)){
            $data = array(
                "sms_addon" => $condition["sms_addon"],
                "keyword" => $condition["keyword"],
                "site_id" => $condition["site_id"]
            );
            $this->addSmsMessage($data);
            $info = model("nc_site_message_sms")->getInfo($condition, $field);
        }
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
	 * 查询短信消息模板列表
	 * @param $condition
	 * @param string $field
	 * @return array
	 */
	public function getSmsMessageList($condition, $field = "*")
	{
		$res = model("nc_site_message_sms")->getList($condition, $field);
		if (empty($res)) {
			return error('', 'UNKNOW_ERROR');
		}
		
		return success($res);
	}
	
	/**
	 * 查询短信消息模板分页列表
	 * @param array $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @param string $field
	 * @return array
	 */
	public function getSmsMessagePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*', $alias = '', $join = [], $group = '')
	{
		$res = model("nc_site_message_sms")->pageList($condition, $field, $order, $page, $page_size, $alias, $join, $group);
		if (empty($res)) {
			return error('', 'UNKNOW_ERROR');
		}
		
		return success($res);
	}
	
	public function deleteSite($site_id)
	{
	    model("nc_site_message_sms")->delete(['site_id' => $site_id]);
	    model("nc_site_message_sms_list")->delete(['site_id' => $site_id]);
	    return success();
	}

    /**
     * 短信发送方式
     * @return \multitype
     */
	public function getSmsTypeList(){
	    $type_list = [];
        $sms_type_list = hook("getSmsConfig", ["site_id" => 0]);
        foreach($sms_type_list as $k => $v){
            $type_list[$v["info"]["name"]] = $v["info"]["title"];
        }
        return success($type_list);
    }
	
}