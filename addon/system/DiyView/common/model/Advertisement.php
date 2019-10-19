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
 * 广告位管理
 * @author Administrator
 *
 */
class Advertisement
{
    /**
     * 添加广告位
     * @param array $data
     */
    public function addAdv($data){
        $id = model('nc_web_config_adv')->add($data);
        return success($id);
    }

    /**
     * 修改广告位
     * @param array $data
     */
    public function editAdv($data,$id){
        $res = model('nc_web_config_adv')->update($data, ['adv_id' => $id]);
        return success($res);
    }

    /**
     * 获取广告详情
     */
    public function getAdvInfo($conditon, $field = "*")
    {
        $res = model('nc_web_config_adv')->getInfo($conditon, $field);
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
    public function getAdvPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $list = model('nc_web_config_adv')->pageList($condition, $field, $order, $page, $page_size);
        return success($list);
    }

    /**
     * 删除广告
     * @param unknown $condition
     */
    public function deleteAdv($condition)
    {
        $res = model('nc_web_config_adv')->delete($condition);
        return success($res);
    }

}
