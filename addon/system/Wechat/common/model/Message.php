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

namespace addon\system\Wechat\common\model;

use app\common\model\Site;

/**
 * 微信消息模板
 */
class Message
{
	
	/**
	 * 添加站点微信消息模板
	 * @param $data
	 * @return array
	 */
	public function addSiteWechatMessage($data)
	{
		$id = model("nc_site_message_wechat")->add($data);
		if ($id === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($id);
	}
	
	/**
	 * 批量添加站点微信消息模板
	 * @param $data
	 * @return array
	 */
	public function addSiteWechatMessageList($data)
	{
		$id = model("nc_site_message_wechat")->addList($data);
		if ($id === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($id);
	}
	
	/**
	 * 编辑站点微信模板消息
	 * @param $data
	 * @param $condition
	 * @return array
	 */
	public function editSiteWechatMessage($data, $condition)
	{
		$res = model("nc_site_message_wechat")->update($data, $condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	
	/**
	 * 删除站点微信消息模板
	 * @param $condition
	 * @return array
	 */
	public function deleteSiteWechatMessage($condition)
	{
		$res = model("nc_site_message_wechat")->delete($condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 查询站点微信消息模板信息
	 * @param $condition
	 * @param string $field
	 * @return array
	 */
	public function getSiteWechatMessageInfo($condition, $field = "*")
	{
		$res = model("nc_site_message_wechat")->getInfo($condition, $field);
		if (empty($res)) {
			$data = array(
				'site_id' => $condition["site_id"],
				'keyword' => $condition['keyword']
			);
			$this->addSiteWechatMessage($data);
			$res = model("nc_site_message_wechat")->getInfo($condition, $field);
		}
		return success($res);
	}
	
	/**
	 * 查询站点微信消息模板列表
	 * @param $condition
	 * @param string $field
	 * @return array
	 */
	public function getSiteWechatMessageList($condition, $field = "*")
	{
        $join = [
            [
                'nc_message_type nmt',
                'nsmw.keyword = nmt.keyword',
                'left'
            ]
        ];
		$res = model("nc_site_message_wechat")->getList($condition, $field, '', $alias = 'nsmw', $join);
		return success($res);
	}
	
	/**
	 * 查询站点微信消息模板分页列表
	 * @param array $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @param string $field
	 * @return array
	 */
	public function getSiteWechatMessagePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$res = model("nc_site_message_wechat")->pageList($condition, $field, $order, $page, $page_size);
		if (empty($res)) {
			return error('', 'UNKNOW_ERROR');
		}
		
		return success($res);
	}
	
	
	/********************************************************************** 模板消息配置 ************************************************************************/
	/**
	 * 添加修改
	 * @param unknown $site_id
	 * @param unknown $name
	 * @param unknown $value
	 */
	public function setWechatMessageConfig($data)
	{
		$site_model = new Site();
		$data["name"] = 'WECHAT_MESSAGE_CONFIG';
		$res = $site_model->setSiteConfig($data);
		return $res;
	}
	
	/**
	 * 查询数据
	 * @param unknown $where
	 * @param unknown $field
	 * @param unknown $value
	 */
	public function getWechatMessageConfig($site_id)
	{
		$site_model = new Site();
		$config = $site_model->getSiteConfigInfo([ 'name' => 'WECHAT_MESSAGE_CONFIG', 'site_id' => $site_id ]);
		return $config;
	}
	
}