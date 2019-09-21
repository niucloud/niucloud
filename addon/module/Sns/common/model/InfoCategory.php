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

/**
 * 信息分类 model
 * @author Administrator
 *
 */
class InfoCategory
{
    /**
     * 添加信息分类
     * @param array $data
     */
    public function addInfoCategory($data)
    {
        $model = model('sns_info_category');
        $category_id = $model->add($data);

        $data['category_id'] = $category_id;

        if ($category_id) {
            return success($data);
        } else {
            return error($category_id);
        }
    }

    /**
     * 修改信息分类
     * @param $data
     * @return \multitype
     */
    public function editInfoCategory($data)
    {
        $condition = array(
            "site_id" => $data["site_id"],
            "category_id" => $data["category_id"],
        );
        $model = model('sns_info_category');
        $res = $model->update($data, $condition);
        if ($res) {
            return success($res);
        } else {
            return error($res);
        }
    }

    /**
     * 获取信息分类详情
     * @param int $category_id
     */
    public function getInfoCategoryDetail($category_id, $site_id){

        $condition = array(
            'category_id'   => $category_id,
            'site_id'   => $site_id
        );
        $info = model('sns_info_category')->getInfo($condition, '*');
        return $info;
    }

    /**
     * 检查分类名称是否存在
     * @param int $category_id
     */
    public function isCategoryName($site_id,$parent,$name){

        $condition = array(
            'parent'   => $parent,
            'site_id'   => $site_id,
            'name'   => $name
        );

        return model('sns_info_category')->getInfo($condition, 'category_id');
    }

    /**
     * 获取信息分类树 仅用于使用
     * @param int $site_id
     * @return multitype:string mixed
     */
    public function getInfoCategoryTree($site_id)
    {
        $list = model('sns_info_category')->getList([ 'site_id' => $site_id , 'visible' => 1]);
        $tree = list_to_tree($list, 'category_id', 'parent', 'child_list');

        foreach($tree as $key => $item){
            if(!isset($item['child_list'])){
                unset($tree[$key]);
            }
        }
        return success($tree);
    }

    /**
     * 获取所有分类
     */
    public function getCategoryData($site_id,$parent = 0)
    {
        $categoryData = model("sns_info_category")->getList(['parent' => $parent,'site_id' => $site_id,'visible'=>1]);

        if(!empty($categoryData)){
            foreach($categoryData as $key => $val){
                $categoryData[$key]['p_category'] = model("sns_info_category")->getList(['parent' => $val['category_id'],'site_id' => $site_id]);
            }
        }

        return $categoryData;
    }

    /**
     * 获取信息分类分页列表
     * @param array $condition
     * @param number $page
     * @param number $page_size
     * @param string $order
     * @param string $field
     */
    public function getInfoCategoryPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $model = model('sns_info_category');
        $res = $model->pageList($condition, $field, $order, $page, $page_size);
        return success($res);
    }

    /**
     * 获取信息分类列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param number $limit
     */
    public function getInfoCategoryList($condition = [], $field = '*', $order = '', $limit = null)
    {
        $model = model('sns_info_category');
        $res = $model->getList($condition, $field, $order, $alias = 'i', $join = [], $group = '', $limit);
        return success($res);
    }

    /**
     * 删除信息分类
     * @param array $condition
     */
    public function deleteInfoCategory($condition)
    {
        $model = model('sns_info_category');
        $res = $model->delete($condition);
        if ($res) {
            return success($res);
        } else {
            return error($res);
        }
    }

    /**
     * 分类属性列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @return multitype
     */
    public function getInfoCategoryAttributeList($condition = [], $field = '*', $order = ''){

        $model = model('sns_info_category_attribute');
        $list = $model->getList($condition, $field, $order);

        //获取分类
        if(!empty($list)){
			foreach($list as $k => $val){
				$info = model('sns_info_category')->getInfo(['category_id' => $val['category_id']]);
				$list[$k]['category_name'] = $info['name'];
			}
		}

        return success($list);
    }

    /**
     * 属性详情
     */
    public function getCategoryAttribute($attributeId)
    {
        $attribute_find = model('sns_info_category_attribute')->getInfo(['attribute_id' => $attributeId]);

        //input_args字段处理
        $args = array();
        $input_args = explode('|',$attribute_find ['input_args']);



        foreach($input_args as $k => $val){
            $args_v = substr($val,strripos($val,":")+1);
            $args_k = substr($val,0,strrpos($val,":"));;

            $args[$args_k] = $args_v;
        }

        $attribute_find['input_args'] = !empty($args) ? $args : array();

        return $attribute_find;
    }

    /**
     * 添加分类属性
     * @param $data
     */
    public function addInfoCategoryAttribute($data){

        $model = model('sns_info_category_attribute');
        $res = $model->add($data);
        return success($res);
    }

    /**
     * 修改分类属性
     * @param $data
     */
    public function editInfoCategoryAttribute($data){

        $model = model('sns_info_category_attribute');
        $res = $model->update($data, [ 'attribute_id' => $data['attribute_id'] ]);
        
        return success($res);
    }

    /**
     * 删除属性
     */
    public function deleteAttribute($attributeId)
    {
        $res = model('sns_info_category_attribute')->delete(['attribute_id' => $attributeId]);

        if ($res) {
            return success($res);
        } else {
            return error($res);
        }
    }

    /**
     * 属性类型array
     * @param string $type_name
     */
    public function getInputType($type_name = 'all'){

        $arr = array(
            array(
                'name' => '单行文本',
                'type'  => 'text'
            ),
            array(
                'name' => '多行文本',
                'type'  => 'textarea'
            ),
            array(
                'name' => '编辑器',
                'type'  => 'editor'
            ),
            array(
                'name' => '下拉框',
                'type' => 'select'
            ),
            array(
                'name' => '单选按钮',
                'type' => 'radio'
            ),
            array(
                'name' => '复选按钮',
                'type' => 'checkbox'
            ),
            array(
                'name' => '图片',
                'type' => 'img'
            ),
            array(
                'name' => '多图片',
                'type' => 'imgs'
            ),
            array(
                'name' => '文件上传',
                'type' => 'file'
            ),
            array(
                'name' => '多文件上传',
                'type' => 'files'
            ),
            array(
                'name' => '数字',
                'type' => 'number'
            ),
            array(
                'name' => '时间',
                'type' => 'time'
            ),
            array(
                 'name' => '地图坐标',
                 'type' => 'map'
            ),
            array(
                'name' => '地区选择框',
                'type' => 'area'
            ),

        );

        $list = $type_name == 'all' ? $arr : [];
        foreach($arr as $item){

            if($item['type'] == $type_name){

                $list = $item;
                break;
            }
        }

        return $list;
    }

    /**
     * 输入验证正则
     * @param int $regex
     */
    public function getInputRegex($regex_id = 0){

        $arr = array(
            0 => array(
                'regex_id'  => 1,
                'regex_name' => '数字',
                'regex_value' => '/^[0-9.-]+$/'
            ),
            1 => array(
                'regex_id'  => 2,
                'regex_name' => '整数',
                'regex_value' => '/^[0-9-]+$/'
            ),
            2 => array(
                'regex_id'  => 3,
                'regex_name' => '字母',
                'regex_value' => '/^[a-z]+$/i'
            ),
            3 => array(
                'regex_id'  => 4,
                'regex_name' => '字母+数字',
                'regex_value' => '/^[0-9a-z]+$/i'
            ),
            4 => array(
                'regex_id'  => 5,
                'regex_name' => 'E-mail',
                'regex_value' => '/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/'
            ),
            5 => array(
                'regex_id'  => 6,
                'regex_name' => 'QQ',
                'regex_value' => '/^[0-9]{5,20}$/'
            ),
            6 => array(
                'regex_id'  => 7,
                'regex_name' => '网址链接',
                'regex_value' => '/^http:\/\//'
            ),
            7 => array(
                'regex_id'  => 8,
                'regex_name' => '中国手机号码',
                'regex_value' => '/^(1)[0-9]{10}$/'
            ),
            8 => array(
                'regex_id'  => 9,
                'regex_name' => '中国电话号码',
                'regex_value' => '/^[0-9-]{6,13}$/'
            ),
            9 => array(
                'regex_id'  => 10,
                'regex_name' => '中国邮政编码',
                'regex_value' => '/^[0-9]{6}$/'
            ),
        );

        $list = empty($regex_id) ? $arr : [];
        foreach($arr as $item){
            if($item['regex_id'] == $regex_id){

                $list = $item;
                break;
            }
        }

        return $list;
    }

     /**
     * 添加收藏浏览
     * @param array $data
     */
    public function addCollectionBrowse($data)
    {
        $model = model('sns_info_collection_browse');
        $record_id = $model->add($data);
        $data['record_id'] = $record_id;
        if ($record_id) {
            return success($data);
        } else {
            return error($record_id);
        }
    }
    /**
     * 修改收藏浏览
     * @param $data
     * @return \multitype
     */
    public function editCollectionBrowse($data)
    {
        $condition = array(
            "info_id" => $data["info_id"],
            "member_id" => $data["member_id"],
        );
        $model = model('sns_info_collection_browse');
        $res = $model->update($data, $condition);
        if ($res) {
            return success($res);
        } else {
            return error($res);
        }
    }

    /**
     * 获取收藏浏览详情
     * @param array $condition
     */
    public function getCollectionBrowseDetail($condition,$filed="*")
    {
        $res = model('sns_info_collection_browse')->getInfo($condition, $filed);
        return $res;
    }
    
    /**
     * 获取收藏浏览分页数据
     * @param array $condition
     */
    public function getCollectionBrowseList($condition=[],$field="*", $order = '',$page = 1, $list_rows = PAGE_LIST_ROWS)
    {
        $res = model('sns_info_collection_browse')->pageList($condition,$field,$order,$page,$list_rows);
        return $res;
    }

    /**
     * 添加评论
     * @param array $data
     */
    public function addComment($data)
    {
        $model = model('sns_info_comment');
        $record_id = $model->add($data);
        $data['record_id'] = $record_id;
        if ($record_id) {
            return success($data);
        } else {
            return error($record_id);
        }
    }

    /**
     * @description: 修改评论
     * @param {$condition,$data} 
     * @return: 
     */    
    public function changeComment($condition,$data){

        $model = model('sns_info_comment');
        $record_id  = $model->update($data,$condition);

        return $record_id;
    }

    /**
     * 获取带总数的评论列表
     * @param array $condition
     */
    public function getCommentList($condition=[],$field='*',$order='',$limit)
    {
        $model = model('sns_info_comment');
        $comment = $model->getList($condition, $field, $order, 'i', [], '', $limit);
        $total = $model->getCount($condition);
        $comment_list = [
            "comment"=>$comment,
            "total"=>$total
        ];
        return $comment_list;
    }

    /**
     * 获取评论分页列表
     * @param array $condition
     */
    public function getCommentPageList($condition=[],$field='*',$order='',$page=1,$page_size = PAGE_LIST_ROWS)
    {
        $model = model('sns_info_comment');
        $comment_list = $model->pageList($condition, $field, $order, $page, $page_size);
        return success($comment_list);
    }
    
    // 获取已通过评论总数
    public function getCommentTotal($condition){
        $model = model('sns_info_comment');
        $total = $model->getCount($condition);
        return  $total;
    }

    /**
     * 增加点赞数
     * @param unknown $condition
     */
    public function gainLike($condition)
    {
        $retval = model('sns_info_comment')->setInc($condition, 'fabulous');
        return success($retval);
    }

    /**
     * 减少点赞数
     * @param unknown $condition
     */
    public function reduceLike($condition)
    {
        $retval = model('sns_info_comment')->setDec($condition, 'fabulous');
        return success($retval);
    }

    /**
     * 获取评论分页列表
     * @param array $condition
     */
    public function getReportPageList($condition=[],$field='*',$order='',$page=1,$page_size = PAGE_LIST_ROWS)
    {
        $model = model('sns_info_report');
        $report_list = $model->pageList($condition, $field, $order, $page, $page_size);
        return success($report_list);
    }

    /**
     * 添加举报
     * @param unknown $data
     */
    public function addReport($data){
        $model = model('sns_info_report');
        $record_id = $model->add($data);
        $data['record_id'] = $record_id;
        if ($record_id) {
            return success($data);
        } else {
            return error($record_id);
        }
    }

    /**
     * 审核举报
     * @param unknown $data
     */
    public function editReport($condition,$data){
        $model = model('sns_info_report');
        $record_id = $model->update($data,$condition);
        $data['record_id'] = $record_id;
        if ($record_id) {
            return success($data);
        } else {
            return error($record_id);
        }
    }

    /**
     * 通过举报id查找info_id
     * @param unknown report_id
     */
    public function getInfoId($report_id)
    {
        $res = model('sns_info_report')->getInfo(['report_id'=>$report_id], 'info_id');
        return $res;
    }

    /**
     * 改变信息状态值
     * @param unknown info_id
     */
    public function changeInfoState($info_id,$is_delete){
        $res = model('sns_info')->update(['is_delete'=>$is_delete], ['info_id'=>$info_id]);
        return $res;
    }
}