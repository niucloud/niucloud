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
 * @author Administrator
 *
 */
class Index
{
    
    /**
     * 插件详情
     */
    public function getInfo($condition)
    {
        $info = model('nc_site_menu')->getInfo($condition);
        return success($info);
    }
    
    /**
     * 获取子目录
     */
    public function getChildList($condition)
    {
        $list = model('nc_site_menu')->getList($condition);
        return success($list);        
    }
    
    function getTree($data, $menu_pid)
    {	
        $tree = [];
        foreach($data as $k => $v){
            if($v['menu_pid'] == $menu_pid){
                $v['child_list'] = $this->getTree($data, $v['name']);
                $tree[] = $v;
                unset($data[$k]);
            }
        }
        return $tree;
    }
    
}
