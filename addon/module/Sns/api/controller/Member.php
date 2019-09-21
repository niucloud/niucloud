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

use addon\module\Sns\common\model\Info;
use addon\module\Sns\common\model\InfoCategory as InfoCategoryModel;
use addon\module\Sns\common\model\Info as InfoModel;
use app\common\model\Member as MemberModel;
use app\common\controller\BaseApi;

/**
 * 分类 控制器
 * 创建时间：2019年8月28日16:00:11
 */
class Member extends BaseApi
{
    //获取收藏列表数据
    public function getCollectionList($params)
    {
        $InfoCategory = new InfoCategoryModel;
        $Info = new InfoModel;
        $member_id = $this->checkAccessToken($params['site_id'], $params['access_token']); //通过token获取用户会员id
        $condition = [
            'member_id' => $member_id,
            'is_collection' => 1,
            'site_id' => $params['site_id']
        ];
        $order="collection_time desc";
        $page = isset($params['page']) ? $params['page'] : 1;
        $page_size = isset($params['page_size']) ? $params['page_size'] : PAGE_LIST_ROWS;
        $collection_list = $InfoCategory->getCollectionBrowseList($condition, '*', $order, $page, $page_size);
        foreach ($collection_list['list'] as $key => $val) {
            $collection_list['list'][$key]['info_array'] = json_decode($val['info_json']);
            $collection_list['list'][$key]['collection'] = $Info->getInfoClientDetail($val['info_id'])['collection'];
        }
        return $collection_list;
    }

    //获取浏览历史列表
    public function getHistoryList($params)
    {
        $InfoCategory = new InfoCategoryModel;
        $member_id = $this->checkAccessToken($params['site_id'], $params['access_token']); //通过token获取用户会员id
        $condition = [
            'member_id' => $member_id,
            'site_id' => $params['site_id']
        ];
        $order="browse_time desc";
        $page = isset($params['page']) ? $params['page'] : 1;
        $page_size = isset($params['page_size']) ? $params['page_size'] : PAGE_LIST_ROWS;
        $history_list = $InfoCategory->getCollectionBrowseList($condition, '*', $order, $page, $page_size);
        foreach ($history_list['list'] as $key => $val) {
            $history_list['list'][$key]['info_array'] = json_decode($val['info_json']);
        }
        return $history_list;
    }

    //获取我的发布列表
    public function getPublishList($params)
    {
        $state = $params['state'];
        $Info = new InfoModel;
        $member_id = $this->checkAccessToken($params['site_id'], $params['access_token']); //通过token获取用户会员id
        $condition = [
            'member_id' => $member_id,
            'site_id' => $params['site_id']
        ];
        if($params['state'] !== "all"){
            $condition['state'] = $state;
        }
        $order="last_time desc,add_time desc";
        $page = isset($params['page']) ? $params['page'] : 1;
        $page_size = isset($params['page_size']) ? $params['page_size'] : PAGE_LIST_ROWS;
        $publish_list = $Info->getInfoPageList($condition, $page, $page_size, $order, '*');
        return $publish_list;
    }

    /**
     * @param $params
     * 发布信息
     */
    public function publish($params){

        $member_id = $this->checkAccessToken($params['site_id'], $params['access_token']);
        $info_id = $params['info_id'];
        $params['member_id'] = $member_id;
        $info_model = new Info();
        if(empty($info_id)){
            $res = $info_model->addInfo($params);
        }else{
            $res = $info_model->editInfo($params);
        }
        
        return $res;
    }

    /**
     * @description: 删除发布信息
     * @param {array} 
     * @return: 
     */    
    public function delPublishInfo($params){
        $Info = new InfoModel;
        $info_id = $params['info_id'];
        $condition = [
            'info_id' => $info_id
        ];
        $res = $Info -> deleteInfo($condition);
        return $res;
    }

    /**
     * 添加评论
     */    
    public function addComment($params){
        $Info = new InfoModel;
        $Member = new MemberModel;
        $InfoCategory = new InfoCategoryModel;
        $info_id = $params['info_id'];
        $member_id = $this->checkAccessToken($params['site_id'], $params['access_token']); //通过token获取用户会员id
        $member_info = $Member->getMemberInfo(['member_id' => $member_id]);
        $info_title = $Info->getInfoDetail(['info_id'=>$info_id],'title')['title'];
        $member_json = json_encode([
            "headimg" => $member_info["data"]["headimg"],
            "username" => $member_info["data"]["username"],
            "mobile" => $member_info["data"]["mobile"],
            "title" => $info_title
        ]);
        $comment_time = time();
        $content = $params['content'];
        $data = [
            "member_id" => $member_id,
            "member_json" => $member_json,
            "content" => $content,
            "create_time" => $comment_time,
            "info_id" => $info_id,
            "site_id" => $params['site_id']

        ];
        $res = $InfoCategory ->addComment($data);
        $Info->gainInfoComment(['info_id'=>$info_id]);
        return $res;
    }

    /**
     * 点赞 
     */    
    public function isNice($params){
        $InfoCategory = new InfoCategoryModel;
        $comment_id = $params['comment_id'];
        $isLike = $params['like'];
        if($isLike == 1){
            $like = $InfoCategory->gainLike(['comment_id'=>$comment_id]);
        }else{
            $like = $InfoCategory->reduceLike(['comment_id'=>$comment_id]);
        }
        return $like;
    }

    /**
     * @description: 刷新商品
     * @param {info_id} 
     * @return: 
     */
    public function reflashInfo($params){
        $Info = new InfoModel;
        $info_id = $params['info_id'];
        $res = $Info->reflashInfo(['info_id' => $info_id]);
        return $res;
    }

    /**
     * @description: 添加举报
     * @param {array} 
     * @return: 
     */
    public function addReport($params){
        $InfoCategory = new InfoCategoryModel;
        $Info = new InfoModel;
        $info_id = $params['info_id'];
        $info_detail = $Info->getInfoDetail(['info_id'=>$info_id]);
        $contact = $params['contact'];
        $report_explain = $params['report_explain'];
        $info_json = json_encode([
            'info_id' => $info_detail['info_id'],
            'title' => $info_detail['title'],
            'img_cover' => $info_detail['img_cover']
        ]);
        $site_id = $params['site_id'];
        $member_id = $this->checkAccessToken($params['site_id'], $params['access_token']); //通过token获取用户会员id
        $create_time = time();
        $data=[
            'info_id' => $info_id,
            'contact' => $contact,
            'report_explain' => $report_explain,
            'info_json' => $info_json,
            'site_id' => $site_id,
            'member_id' => $member_id,
            'create_time' => $create_time
        ];
        return $InfoCategory->addReport($data);

    }
}
