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

namespace addon\module\Sns\common\model;

use think\Db;
use think\Model;

/**
 * 分类信息
 * @author Administrator
 *
 */
class Info
{
    /**
     * 获取信息分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getInfoPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $list = model('sns_info')->pageList($condition, $field, $order, $page, $page_size);

        foreach($list['list'] as $k => $val){

            //前台发布人查询
            if($val['member_id'] > 0){

                $member = model('nc_member')->getInfo(['member_id'=>$val['member_id']]);
                $list['list'][$k]['release_name'] = $member['username'];
                $list['list'][$k]['console'] = '会员';

                //后台发布人查询
            }else if($val['uid'] > 0){
                $user = model('nc_user')->getInfo(['uid' => $val['uid']]);
                $list['list'][$k]['release_name'] = $user['username'];
                $list['list'][$k]['console'] = '平台';
            }

            if($val['state'] == 0){
                $list['list'][$k]['state_as'] = '待审核';
            }

            if($val['state'] == 1){
                $list['list'][$k]['state_as'] = '已审核';
            }

            if($val['state'] == 2){
                $list['list'][$k]['state_as'] = '已拒绝';
            }

            //获取分类
            $category_info = model('sns_info_category')->getInfo( [ 'category_id' => $val['category_id'] ]);
            $category = model('sns_info_category')->getInfo( [ 'category_id' => $category_info['parent'] ]);

            $list['list'][$k]['category_name'] = $category['name'].'/'.$category_info['name'];
            $list['list'][$k]['add_time'] = date( "Y-m-d H:i:s",$val['add_time']);
            $list['list'][$k]['reflash'] = date( "Y-m-d H:i:s",$val['reflash']);
            $list['list'][$k]['reflash_time'] = $val['reflash'];
        }

        return success($list);
    }

    /**
     * 获取信息详情
     * @param array $condition
     * @param bool $field
     * @param string $alias
     * @param null $join
     * @param null $data
     * @return \multitype
     */
    public function getInfoDetail($condition = [], $field = true, $alias = 'snsi', $join = null, $data = null)
    {
        $res = model('sns_info')->getInfo($condition, $field, $alias, $join, $data);

        $res['attribute_value'] = array();

        if(!empty($res)){

            //分类信息属性值
            $res['attribute_value'] = model('sns_info_attribute_value')->getList(['info_id' => $res['info_id']]);
        }

        return $res;
    }

    /**
     * @param int $info
     * 客户端详情
     */
    public function getInfoClientDetail($info_id){

        $info = model('sns_info')->getInfo(['info_id' => $info_id]);

        $attribute_list = model('sns_info_attribute_value')->getList(['info_id' => $info['info_id']]);
        foreach($attribute_list as $key=>$item){

            $attribute_list[$key]['attribute_name'] = model('sns_info_category_attribute')->getInfo(['attribute_id' => $item['attribute_id']], 'name')['name'];
        }

        $info['circle_name'] = model("nc_area")->getinfo(['id'=>$info['circle']], 'name')['name'];
        $info['img_arr'] = explode(',', $info['imgs']);
        $info['attribute_value'] = $attribute_list;
        return $info;
    }

        
    /**
     * 修改分类信息排序 sort
     */
    public function updateInfo($data)
    {
        $res = model('sns_info')->update($data, [ 'info_id' => $data['info_id'] ]);

        return success($res);
    }

    /**
     * 获取信息属性值
     * @param string $info_id
     */
    public function getInfoAttributeValue($info_id)
    {
        return model('sns_info_attribute_value')->getInfo(['info_id' => $info_id]);
    }

    // 根据info_id和attribute_id查询属性值
    public function getInfoAttributeVal($condition,$field="*")
    {
        return model('sns_info_attribute_value')->getInfo($condition,$field);
    }

    /**
     * 添加分类信息
     * @param array $data
     */
    public function addInfo($param)
    {
        model('sns_info')->startTrans();
        try {
            $data = array(
                'category_id' => $param['category_id'],
                'site_id' => isset($param['site_id']) ? $param['site_id'] : SITE_ID,
                'img_cover' => $param['img_cover'],
                'imgs' => $param['imgs'],
                'title' => $param['title'],
                'content' => $param['content'],
                'uid' => isset($param['uid']) ? $param['uid'] : 0,
                'add_time' => time(),
                'last_time' => time(),
                'reflash'   => time(),
                'state' => !empty($param['uid']) ? 1 : 0,
                'price' => $param['price'],
                'linkman' => $param['linkman'],
                'contact' => $param['contact'],
                'circle' => $param['city'],
                'member_id' => isset($param['member_id']) ? $param['member_id'] : 0
            );

            $info_id = model('sns_info')->add($data);

            $a_v_arr = array();

            foreach ($param as $key => $value) {

                $key_arr = explode('_', $key);

                $data_key = $key_arr[count($key_arr) - 1];

                if ($data_key > 0) {

                    $a_v_arr[] = array(
                        'attribute_id' => $data_key,
                        'content' => $value,
                        'site_id' => SITE_ID,
                        'info_id' => $info_id
                    );
                }
            }

            $res = model('sns_info_attribute_value')->addList($a_v_arr);

            model('sns_info')->commit();
            return $info_id;
        } catch (\Exception $e) {
            model('sns_info')->rollback();
            return error('', $e->getMessage());
        }
    }

    /**
     * 修改分类信息
     * @param array $data
     */
    public function editInfo($param)
    {
        model('sns_info')->startTrans();
        try {
            $data = array(
                'category_id' => $param['category_id'],
                'site_id' => isset($param['site_id']) ? $param['site_id'] : SITE_ID,
                'img_cover' => $param['img_cover'],
                'imgs' => $param['imgs'],
                'title' => $param['title'],
                'content' => $param['content'],
                'last_time' => time(),
                'state' => !empty($param['uid']) ? 1 : 0,
                'price' => $param['price'],
                'linkman' => $param['linkman'],
                'contact' => $param['contact'],
                'circle' =>$param['city'],
                'member_id' => isset($param['member_id']) ? $param['member_id'] : 0,
            );
            $res = model('sns_info')->update($data, [ 'info_id' => $param['info_id'] ]);

            $a_v_arr = array();

            foreach ($param as $key => $value) {

                $key_arr = explode('_', $key);

                $data_key = $key_arr[count($key_arr) - 1];

                if ($data_key > 0) {

                    $a_v_arr[] = array(
                        'attribute_id' => $data_key,
                        'content' => $value,
                        'site_id' => SITE_ID,
                        'info_id' => $param['info_id']
                    );
                }
            }

            //删除信息属性值
            model('sns_info_attribute_value')->delete(['site_id' => SITE_ID, 'info_id' => $param['info_id']]);

            if(!empty($a_v_arr)){

                $res = model('sns_info_attribute_value')->addList($a_v_arr);

                if(!$res){
                    model('sns_info')->rollback();
                    return error();
                }
            }

            model('sns_info')->commit();
            return success($res);

        } catch (\Exception $e) {
            model('sns_info')->rollback();
            return error('', $e->getMessage());
        }
    }

    //刷新信息

    public function reflashInfo($condition){
        $time = time();
        $res = model('sns_info')->update(['reflash' => $time],$condition);
        return $res;
    }

    /**
     * 删除分类信息
     * @param unknown $coupon_type_id
     */
    public function deleteInfo($condition)
    {
        $res = model('sns_info')->delete($condition);
        return success($res);
    }


    /**
     * 增加访问量
     * @param unknown $condition
     */
    public function gainInfoVisit($condition)
    {
        $retval = model('sns_info')->setInc($condition, 'visit');
        return success($retval);
    }

    /**
     * 增加收藏量
     * @param unknown $condition
     */
    public function gainInfoCollection($condition)
    {
        $retval = model('sns_info')->setInc($condition, 'collection');
        return success($retval);
    }

    /**
     * 减少收藏量
     * @param unknown $condition
     */
    public function reduceInfoCollection($condition)
    {
        $retval = model('sns_info')->setDec($condition, 'collection');
        return success($retval);
    }

    /**
     * 增加评论数
     * @param unknown $condition
     */
    public function gainInfoComment($condition)
    {
        $retval = model('sns_info')->setInc($condition, 'comment');
        return success($retval);
    }
    /**
     * 删除站点
     * @param unknown $site_id
     */
    public function deleteSite($site_id)
    {
        model('sns_info')->delete(['site_id' => $site_id]);
        model('sns_info_category')->delete(['site_id' => $site_id]);
        model('sns_info_category_attribute')->delete(['site_id' => $site_id]);
        model('sns_info_attribute_value')->delete(['site_id' => $site_id]);
        return success();
    }

    /**
     * 获取信息分类列表
     */
    public function getCategoryParentList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {

        $list = model('sns_info_category')->pageList($condition, $field, $order, $page, $page_size);

        return success($list);
    }

    /**
     * 获取分类下拉
     */
    public function  getCategorySelect($condition){

        return success(model('sns_info_category') ->getList($condition));

    }

    /**
     * 获取所有二级分类ID
     */
    public function getCategoryId($condition)
    {
        $category_id = model('sns_info_category')->getColumn($condition,'category_id');

        return implode($category_id,',');
    }

    /**
     * 获取信息子分类列表
     */
    public function getCategorySonList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {

        $list = model('sns_info_category')->pageList($condition, $field, $order, $page, $page_size);

        if(!empty($list['list'])){
            foreach($list['list'] as $k => $val){
                $info = model('sns_info_category')->getInfo(['category_id' => $val['parent']]);
                $list['list'][$k]['pname'] = $info['name'];
            }
        }

        return success($list);
    }

    /**
     * 获取信息分类详情 ->where(['category_id','=',$category_id])->find()
     */
    public function getCategory( $category_id)
    {
        $list = model('sns_info_category')->getInfo( [ 'category_id' => $category_id ]);

        return $list;
    }

    /**
     * 分类信息修改
     */
    public function editCategory($data){

        if($data['category_id'] < 1){
            return error();
        }

        $res = model('sns_info_category')->update($data, [ 'category_id' => $data['category_id'] ]);

        return success($res);
    }

    /**
     * 删除分类
     */
    public function deleteCategory($category_id){
        model('sns_info_category')->delete(['category_id' => $category_id]);

        return success();
    }



    /**
     * 获取分类信息的总数
     */
    public function getCategoryInfoCount($condition)
    {
        return model('sns_info')->stat($condition,'count', 'info_id');
    }
}