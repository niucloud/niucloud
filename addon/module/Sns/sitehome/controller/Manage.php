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
namespace addon\module\Sns\sitehome\controller;

use addon\module\Sns\common\model\InfoCategory;
use app\common\controller\BaseSiteHome;
use addon\module\Sns\common\model\Info as InfoModel;
use app\common\model\Address;
use app\common\model\User;
use app\common\model\Member;

/**
 * 分类信息控制器
 * @author Administrator
 *
 */
class Manage extends BaseSiteHome
{
    public $info_model;
    public $info_category_model;
    public $address;
    protected $replace = [];

    public function __construct()
    {
        parent::__construct();
        $this->replace = [
            'SNS_CSS' => __ROOT__ . '/addon/module/Sns/sitehome/view/public/css',
            'SNS_JS' => __ROOT__ . '/addon/module/Sns/sitehome/view/public/js',
            'SNS_IMG' => __ROOT__ . '/addon/module/Sns/sitehome/view/public/img',
        ];

        $this->info_model = new InfoModel();
        $this->info_category_model = new InfoCategory();
        $this->address = new Address();
    }

    /**
     * @description: 获取评论列表
     * @return: tree
     */

    public function commentList()
    {

        if (IS_AJAX) {

            $page = input('page', 1);

            $page_size = input('limit',10);

            $condition = [
                'parent' => 0,
                'is_delete' => 0,
                'site_id' => $this->siteId
            ];
            
            $comment_list = $this->info_category_model->getCommentPageList($condition, '*', '', $page, $page_size);
            
            $comment_list_all = $this->info_category_model->getCommentPageList(['is_delete'=>0,'site_id' => $this->siteId], '*', '', $page, $page_size);
            
            foreach ($comment_list_all['data']['list'] as $key => $val) {
                $comment_list_all['data']['list'][$key]['member_array'] = json_decode($val['member_json']);
            }

            $res = list_to_tree($comment_list_all['data']['list'],'comment_id','parent','comment_child');
            foreach ($comment_list['data']['list'] as $key => $val) {
                $comment_list['data']['list'] = $res;
            }
            return $comment_list;
        
        }

        return view('manage/comment_list',[],$this->replace);
        
    }

    /**
     * @description: 举报列表
     * @return: 
     */
    public function reportList(){

        if (IS_AJAX) {

            $member_model = new Member();

            $page = input('page', 1);

            $page_size = input('limit',10);

            $condition = [
                'is_delete' => 0,
                'site_id' => $this->siteId
            ];

            $report_list = $this->info_category_model->getReportPageList($condition, '*', '', $page, $page_size);
            
            foreach ($report_list['data']['list'] as $key => $val) {
                $report_list['data']['list'][$key]['info_array'] = json_decode($val['info_json']);
                $report_list['data']['list'][$key]['username'] = $member_model->getMemberInfo(['member_id'=>$val['member_id']],'username')['data']['username'];
            }

            // halt($report_list);
            return $report_list;

        }

        return view('manage/report_list',[],$this->replace);
    }


    /**
     * @description: 审核举报
     * @return: 
     */
    public function reviewReport(){

        $report_id = input('report_id');

        $state = input('state');

        $condition = [
            'report_id'=>$report_id,
        ];

        $data = [
            'state' => $state,
            'state_time' => time(),
            'uid' => UID
        ];
        if($state == -1){
            $data['refuse_reason'] =input('refuse_reason','');
        }else{
            $info_id = $this->info_category_model->getInfoId($report_id)['info_id'];
            $this->info_category_model->changeInfoState($info_id,1);
        }

        return $this->info_category_model->editReport($condition,$data);

    }

    /**
     * @description: 删除举报
     * @return: 
     */
    public function delReport(){

        $report_id = input('report_id');

        $condition = [
            'report_id'=>$report_id,
        ];

        $data = [
            'is_delete' => 1,
            'state_time' => time(),
            'uid' => UID
        ];

        return $this->info_category_model->editReport($condition,$data);

    }
    /**
     * @description: 隐藏显示评论
     * @param {} 
     * @return: 
     */    
    public function isShowComment(){

        $comment_id = input('comment_id');

        $condition = [
            'comment_id'=>$comment_id,
            // 'parent'=>$comment_id
        ];

        $is_show = input('is_show');

        $data = [
            'is_show' => $is_show,
        ];

        return $this->info_category_model->changeComment($condition,$data);

    }

    /**
     * @description: 删除评论
     * @param {} 
     * @return: 
     */  
    public function delComment(){

        $comment_id = input('comment_id');

        $condition = [
            'comment_id' => $comment_id,
        ];

        $is_delete = input('is_delete');

        $data = [
            'is_delete' => $is_delete,
        ];
        return $this->info_category_model->changeComment($condition,$data);
    }

    /**
     * @description: 后台回复评论
     * @param {type} 
     * @return: 
     */    
    public function adminReplyComment(){

        $comment_id = input('comment_id');

        $content = input('content');

        $user_model = new User;

        $nick_name = $user_model->getUserInfo(['uid'=>UID],'nick_name')['data']['nick_name'];
        
        $member_json = json_encode([

            "username" => $nick_name,
        ]);

        $data = [
            'parent' => $comment_id,
            'content' => $content,
            'uid' => UID,
            'member_json' => $member_json,
            'create_time' => time(),
            'site_id' => $this->siteId
            
        ];


        return $this->info_category_model->addComment($data);
    }
}   
