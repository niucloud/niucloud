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

use app\sitehome\controller\Addons;
use app\common\model\Addon;
use app\common\model\Site;

/**
 * 站点消息
 */
class SiteMessage
{
	
	/**
	 * 添加消息类型
	 * @param $data
	 * @return array
	 */
	public function addSiteMessageType($data)
	{
		$id = model("nc_site_message_type")->add($data);
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
	public function addSiteMessageTypeList($data)
	{
		$id = model("nc_site_message_type")->addList($data);
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
	public function editSiteMessageType($data, $condition)
	{
		$res = model("nc_site_message_type")->update($data, $condition);
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
	public function deleteSiteMessageType($condition)
	{
		$res = model("nc_site_message_type")->delete($condition);
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
	public function getSiteMessageTypeInfo($condition, $field = "*")
	{
		$res = model("nc_site_message_type")->getInfo($condition, $field);
		
		if (empty($res)) {
			return error('', 'UNKNOW_ERROR');
		}
		
		//转义变量json
		if (!empty($res['var_json'])) {
			$res['var_json'] = json_decode($res['var_json'], true);
		} else {
			$res['var_json'] = [];
		}
		return success($res);
	}
	
	/**
	 * 查询消息类型列表
	 * @param $condition
	 * @param string $field
	 * @return array
	 */
	public function getSiteMessageTypeList($condition, $field = "*", $order = "id desc")
	{
		$res = model("nc_site_message_type")->getList($condition, $field, $order);
		if (empty($res)) {
			return error('', 'UNKNOW_ERROR');
		}
		
		return success($res);
	}
	/**
	 * 站点消息类型  view
	 * @param unknown $condition
	 * @param string $field
	 * @return string[]|mixed[]
	 */
	public function getSiteMessageTypeViewInfo($condition, $field = "*"){
	    
	    $alias = 'nmt';
	    $join = [
	        [
	            'nc_site_message_type nsmt',
	            'nsmt.site_keyword = nmt.keyword',
	            'left'
	        ]
	    ];
	    
	    $res = model("nc_message_type")->getInfo($condition, $field, $alias, $join);
	    //如果没有站点配置就创建
	    if(empty($res)){
	        $data = array(
	            "site_keyword" => $condition["keyword"],
	            "site_id" => $condition["site_id"],
	            "create_time" => time()
	        );
	        $this->addSiteMessageType($data);
	        $res = model("nc_message_type")->getInfo($condition, $field, $alias, $join);
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
	 * 站点消息类型  view
	 * @param unknown $condition
	 * @param string $field
	 * @param string $order
	 * @return string[]|mixed[]
	 */
	public function getSiteMessageTypeViewList($site_id, $condition = [], $field = "*", $order = ""){
	    
	    $alias = 'nmt';
	    $join = [
	        [
	            'nc_site_message_type nsmt',
	            'nsmt.site_keyword = nmt.keyword and nsmt.site_id = '.$site_id,
	            'left'
	        ]
	    ];
	    $res = model("nc_message_type")->getList($condition, $field, $order, $alias, $join);
	    
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
	public function getSiteMessageTypePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$res = model("nc_site_message_type")->pageList($condition, $field, $order, $page, $page_size);
		if (empty($res)) {
			return error('', 'UNKNOW_ERROR');
		}
		
		return success($res);
	}
}