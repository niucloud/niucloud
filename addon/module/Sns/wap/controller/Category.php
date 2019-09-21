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

use app\common\controller\BaseSite;
use addon\module\Sns\common\model\InfoCategory;
use addon\module\Sns\common\model\Info;
use app\common\model\Address;


/**
 * 分类 控制器
 * 创建时间：2019年8月28日16:00:11
 */
class Category extends BaseSite
{
    protected $replace = [];
    protected $info_model;
    protected $category_model;

    public function __construct()
    {
        parent::__construct();
        $this->replace = [
            'ADDON_WAP_SNS_CSS' => __ROOT__ . '/addon/module/Sns/wap/view/' . $this->wap_style . '/public/css',
            'ADDON_WAP_SNS_JS' => __ROOT__ . '/addon/module/Sns/wap/view/' . $this->wap_style . '/public/js',
            'ADDON_WAP_SNS_IMG' => __ROOT__ . '/addon/module/Sns/wap/view/' . $this->wap_style . '/public/img',
        ];

        $this->info_model = new Info();
        $this->category_model = new InfoCategory();
    }

    /**
     * 分类列表
     * 创建时间：2019年8月28日15:54:53
     */
    public function index()
    {
        //获取分类列表数据
        $list = $this->category_model->getInfoCategoryTree($this->siteId);

        return $this->fetch($this->wap_style . '/category/index', [
            'list' => $list['data'],
            'title' => '栏目'
        ], $this->replace);
    }

    /**
     * 信息列表
     * 创建时间：2019年8月28日16:01:51
     */
    public function lists()
    {
        $category_id = input("category_id", '');

        $this->assign("category_id", $category_id);
        $this->assign("title", "信息列表");
        return $this->fetch($this->wap_style . '/category/lists', [], $this->replace);
    }

    /**
     * 信息详情
     * 创建时间：2019年9月7日16:22:22
     */
    public function info()
    {  
        $info_id = input('info_id', 0);
        $info = $this->info_model->getInfoClientDetail($info_id);
        $member_info = $this->member_info;
        $this->assign("info_detail",$info);
        $this->assign("title", "信息详情");
        return $this->fetch($this->wap_style . '/category/info', ['member_info'=>$member_info], $this->replace);
    }


    /**
     * 获取所有省、市
     */
    public function getProvinces()
    {
        $province = input('province');

        $address = new Address();
        $provinces = $address->getProvinces($province);

        return json_encode($provinces);
    }
}