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

namespace addon\module\Sns\wap\controller;

use addon\module\Sns\common\model\Info;
use addon\module\Sns\common\model\InfoCategory;
use app\common\controller\BaseSite;
use app\common\model\Address;

/**
 * 会员 控制器
 * 创建时间：2018年8月31日16:55:48
 */
class Member extends \app\wap\controller\Member
{
    protected $replace = [];
    public $category_model;
    public $info_model;
    public function __construct()
    {
        parent::__construct();
        $this->replace = [
            'ADDON_WAP_SNS_CSS' => __ROOT__ . '/addon/module/Sns/wap/view/'.$this->wap_style.'/public/css',
            'ADDON_WAP_SNS_JS' => __ROOT__ . '/addon/module/Sns/wap/view/'.$this->wap_style.'/public/js',
            'ADDON_WAP_SNS_IMG' => __ROOT__ . '/addon/module/Sns/wap/view/'.$this->wap_style.'/public/img',
        ];

        $this->info_model = new Info();
        $this->category_model = new InfoCategory();
    }

    /**
     * @return mixed
     * 个人中心
     */
    public function index()
    {
        if (!empty($this->access_token)) {
            return $this->getDiyView([ "name" => "DIYVIEW_MEMBER" ]);
        } else {
            $this->redirect(url('wap/login/login'));
        }
    }

    /**
     * 收藏
     * 创建时间：2019年8月28日16:12:27
     */
    public function collection(){
        $this->assign("title", "我的收藏");
        return $this->fetch($this->wap_style . '/member/collection', [], $this->replace);
    }

    /**
     * 浏览
     * 创建时间：2019年8月28日16:13:24
     */
    public function history(){
        $this->assign("title", "浏览记录");
        return $this->fetch($this->wap_style . '/member/history', [], $this->replace);
    }

    /**
     * 评论
     * 创建时间：2019年8月28日16:16:17
     */
    public function comment(){

        $this->assign("title", "我的评论");
        return $this->fetch($this->wap_style . '/member/comment', [], $this->replace);
    }

    /**
     * @return mixed|string
     */
    public function publish(){

        //获取分类列表数据
        $category_list = $this->category_model->getInfoCategoryTree($this->siteId);
        return $this->fetch($this->wap_style . '/member/publish', [
            'category_list' => $category_list['data'],
            'title' => '发布信息'
        ], $this->replace);
    }

    /**
     * @return mixed|string
     * 信息属性
     */
    public function infoAttribute(){

        $category_id = input('category_id', 0);
        //分类属性
        $condition = array(
            'category_id' => $category_id,
            'site_id'     => $this->siteId
        );

        $list = $this->category_model->getInfoCategoryAttributeList($condition, '*', 'sort desc');

        foreach($list['data'] as $key=>$item){

            $arr = explode("|",$item['input_args']);
            $r = array();
            foreach ($arr as $val ){
                $t = explode(":",$val);

                $v = $t[1];
                if($t[0] == 'select_option' || $t[0] == 'radio_option' || $t[0] == 'checkbox_option'){
                    $v = explode('/', $t[1]);
                }
                $r[$t[0]]= $v;
            }
            $list['data'][$key]['args'] = $r;
        }

        //分类信息详情
        $category_info = $this->category_model->getInfoCategoryDetail($category_id, $this->siteId);

        //获取省 province
        $address = new Address();
        $province = $address->getProvinces(0);

        return $this->fetch($this->wap_style . '/member/info_attribute', [
            'category_name' => input('category_name', ''),
            'category_info' => $category_info,
            'province'  => $province,
            'list'  => $list
        ], $this->replace);
    }

    /**
     * @return mixed|string
     * 编辑时的默认显示
     */
    public function editInfoAttribute(){
        $info_id = input('info_id');
        if(isset($info_id)){
            $info_detail = $this->info_model -> getInfoDetail(['info_id' => $info_id]);
        }
        $category_id = $info_detail['category_id'];
        //分类属性
        $condition = array(
            'category_id' => $category_id,
            'site_id'     => $this->siteId
        );

        $list = $this->category_model->getInfoCategoryAttributeList($condition, '*', 'sort desc');

        foreach($list['data'] as $key=>$item){

            $arr = explode("|",$item['input_args']);
            
            $r = array();
            
            foreach ($arr as $val ){
                $t = explode(":",$val);
                $v = $this->info_model->getInfoAttributeVal(['info_id'=>$info_id,'attribute_id'=>$item['attribute_id']],'content')['content'];
                if($t[0] == 'select_option' || $t[0] == 'radio_option' || $t[0] == 'checkbox_option'){
                    $v = explode('/', $t[1]);
                }
                $r[$t[0]]= $v;
            }
            
            $list['data'][$key]['args'] = $r;
        }
        //分类信息详情
        $category_info = $this->category_model->getInfoCategoryDetail($category_id, $this->siteId);
        
        if($category_info['parent']>0){
            $category_parent = $this->category_model->getInfoCategoryDetail($category_info['parent'], $this->siteId)['name'];
            $category_info['parent'] = $category_parent;
        }
        //获取省 province
        $address = new Address();
        $province = $address->getProvinces(0);
        return $this->fetch($this->wap_style . '/member/info_attribute', [
            'category_info' => $category_info,
            'province'  => $province,
            'list'  => $list,
            'info_detail' => $info_detail
        ], $this->replace);
    }

    /**
     * 发布
     * 创建时间：2019年9月210日10:16:17
     */
    public function myPublish(){

        $this->assign("title", "我的发布");
        return $this->fetch($this->wap_style . '/member/my_publish', [], $this->replace);
    }
}