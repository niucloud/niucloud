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

use think\Cache;
/**
 * 地区表
 */
class Address
{
    /**
     * 获取地区列表
     * @param unknown $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     * @return multitype:string mixed
     */
    public function getAreaList($condition = [], $field = '*', $order = '', $limit = null){
        
        $data = json_encode([$condition, $field, $order, $limit]);
        $cache = Cache::tag("address")->get("getAreaList_".$data);
        if(!empty($cache))
        {
            return success($cache);
        }
        $area_list = model("nc_area")->getList($condition, $field, $order, $limit);
        Cache::tag("address")->set("getAreaList_".$data, $area_list);
        return success($area_list);
    }


    /**
     * 获取地区详情
     */
    public function getArea($circle){
        return model("nc_area")->getinfo(['id'=>$circle]);
    }

    /**
     * 获取所有省、市
     */
    public function getProvinces($circle = 0){
        return model("nc_area")->getList(['pid'=>$circle]);
    }
}
