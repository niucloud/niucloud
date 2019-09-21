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

namespace addon\module\Sns\api\controller;

use addon\module\Sns\common\model\Info as InfoModel;
use addon\module\Sns\common\model\InfoCategory;
use addon\module\Sns\common\model\InfoCategory as InfoCategoryModel;
use app\common\model\Member as MemberModel;
use app\common\controller\BaseApi;


/**
 * 分类 控制器
 * 创建时间：2019年8月28日16:00:11
 */
class Category extends BaseApi
{

    public function getCategoryList($params)
    {
        $category = new InfoCategory();
        $list = $category->getInfoCategoryTree($params['site_id']);
        return $list['data'];
    }

    // 获取列表数据
    public function getInfoPageList($params)
    {
        $Info = new InfoModel;
        $search_text = isset($params['search_text']) ? $params['search_text'] : '';
        $category_id = isset($params['category_id']) ? $params['category_id'] : '';
        $field = isset($params['field']) ? $params['field'] : '*';
        $order = isset($params['order']) ? $params['order'] : '';
        $page = isset($params['page']) ? $params['page'] : 1;
        $page_size = isset($params['page_size']) ? $params['page_size'] : PAGE_LIST_ROWS;

        $condition['site_id'] = $params['site_id'];
        if (!empty($category_id)) {
            $condition["category_id"] = $category_id;
        }
        if (!empty($search_text)) {
            $condition["tag|title|content"] = array(
                'like',
                '%' . $search_text . '%'
            );
        }
        $condition['state'] = 1;

        $infoPageList = $Info->getInfoPageList($condition, $page, $page_size, $order, $field);
        return $infoPageList;
    }

    /**
     * 增加浏览量
     * 创建时间：2019年9月7日16:22:22
     */
    public function addBrowse($params)
    {
        $Info = new InfoModel;
        $Member = new MemberModel;
        $InfoCategory = new InfoCategoryModel;
        $member_id = $this->checkAccessToken($params['site_id'], $params['access_token']); //通过token获取用户会员id
        $info_id = $params['info_id'];
        // 总浏览量加一
        $info_detail = $Info->gainInfoVisit(['info_id' => $info_id]);
        // 查询信息详情
        $info_detail = $Info->getInfoDetail(['info_id' => $info_id]);
        $member_info = $Member->getMemberInfo(['member_id' => $member_id]);
        $member_json = json_encode([
            "headimg" => $member_info["data"]["headimg"],
            "username" => $member_info["data"]["username"],
            "mobile" => $member_info["data"]["mobile"]
        ]);
        $info_json = json_encode([
            "img_cover" => $info_detail["img_cover"],
            "title" => $info_detail["title"],
            "tag" => $info_detail["tag"],
            "add_time" => $info_detail["add_time"],
            "collection" => $info_detail["collection"]
        ]);
        // 查询是否已有浏览记录
        $condition = [
            'info_id' => $info_id,
            'member_id' => $member_id,
        ];
        $collection_browse_detail = $InfoCategory->getCollectionBrowseDetail($condition, '*');
        $browse_time = time();
        $data = [
            "info_id" => $info_id,
            "member_id" => $member_id,
            "browse_sum" => 1,
            "browse_time" => $browse_time,
            "info_json" => $info_json,
            "member_json" => $member_json
        ];
        // 如果没有已存在的浏览记录新增数据否则更新数据
        if (!$collection_browse_detail) {
            $InfoCategory->addCollectionBrowse($data);
            return 0;
        } else {
            $data['browse_sum'] = $collection_browse_detail['browse_sum'] + 1;
            $InfoCategory->editCollectionBrowse($data);
            return $collection_browse_detail['is_collection'];
        }
    }

    /**
     * 信息收藏
     * 创建时间：2019年9月7日16:22:22
     */
    public function isCollection($params)
    {
        $InfoCategory = new InfoCategoryModel;
        $Info = new InfoModel;
        $info_id = $params['info_id'];
        $member_id = $this->checkAccessToken($params['site_id'], $params['access_token']); //通过token获取用户会员id
        $is_collection = $params['is_collection'];
        $collection_time = time();
        $data = [
            "info_id" => $info_id,
            "member_id" => $member_id,
            "is_collection" => $is_collection,
            "collection_time" => $collection_time,
        ];
        $res = $InfoCategory->editCollectionBrowse($data);
        if ($is_collection == 1) {
            // 总收藏数加一
           $Info->gainInfoCollection(['info_id' => $info_id]);
        } else {
            // 总收藏数减一
            $Info->reduceInfoCollection(['info_id' => $info_id]);
        }

        return $res;
    }

    /**
     * 评论列表
     * 创建时间：2019年9月11日16:22:22
     */
    public function getCommentList($params)
    {
        $InfoCategory = new InfoCategoryModel;
        $info_id = $params['info_id'];
        
        if(isset($params['access_token'])){
            $member_id = $this->checkAccessToken($params['site_id'], $params['access_token']); //通过token获取用户会员id
            $condition = [
                'member_id' => $member_id
            ];
        }else{
            $condition = [
                'info_id' => $info_id,
                'is_show' => 1,
                'is_delete' =>0,
                'site_id' =>$params['site_id']
            ];
        }
        $limit = $params['limit'];
        $comment_list = $InfoCategory->getCommentList($condition,'*','fabulous desc,create_time desc',$limit);
        foreach ($comment_list['comment'] as $key => $val) {
            $comment_list['comment'][$key]['member_array'] = json_decode($val['member_json']);
        }
        if($comment_list){
            return success($comment_list);
        }else{
            return error($comment_list);
        }

    }

}
