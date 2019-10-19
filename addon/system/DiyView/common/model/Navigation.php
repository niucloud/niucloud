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

namespace addon\system\DiyView\common\model;


/**
 * 导航管理
 * @author Administrator
 *
 */
class Navigation
{
	/**
	 * 添加
	 * @param array $data
	 */
	public function add($data)
	{
		$id = model('nc_web_config_nav')->add($data);
		return success($id);
	}
	
	/**
	 * 修改
	 * @param array $data
	 */
	public function edit($data, $id)
	{
		$res = model('nc_web_config_nav')->update($data, [ 'id' => $id ]);
		return success($res);
	}
	
	/**
	 * 获取详情
	 * @param int $discount_id
	 * @return multitype:string mixed
	 */
	public function getInfo($id)
	{
		$res = model('nc_web_config_nav')->getInfo([ 'id' => $id ]);
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
	public function getPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$list = model('nc_web_config_nav')->pageList($condition, $field, $order, $page, $page_size);
		return success($list);
	}
	
	/**
	 * 删除
	 * @param unknown $coupon_type_id
	 */
	public function delete($condition)
	{
		$res = model('nc_web_config_nav')->delete($condition);
		return success($res);
	}
	
	/**
	 * 通过条件获取详情
	 * @param unknown $coupon_type_id
	 */
	public function getInfoByCondition($condition)
	{
		$res = model('nc_web_config_nav')->getInfo($condition);
		return success($res);
	}
}