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

/**
 * 首页 控制器
 * 创建时间：2018年8月31日16:55:48
 */
class Index extends BaseSite
{
    protected $replace = [];

    public function __construct()
    {
        parent::__construct();
        $this->replace = [
            'ADDON_WAP_SNS_CSS' => __ROOT__ . '/addon/module/Sns/wap/view/'.$this->wap_style.'/public/css',
            'ADDON_WAP_SNS_JS' => __ROOT__ . '/addon/module/Sns/wap/view/'.$this->wap_style.'/public/js',
            'ADDON_WAP_SNS_IMG' => __ROOT__ . '/addon/module/Sns/wap/view/'.$this->wap_style.'/public/img',
        ];
    }

    /**
     * 首页
     * 创建时间：2019年8月23日15:55:33
     */
    public function index()
    {
        hook("index");
        return $this->getDiyView([ "name" => "DIYVIEW_SITE" ]);

    }

    /**
     * 分类列表
     * 创建时间：2019年8月28日15:54:53
     */
    public function category(){

        $this->assign("title", "分类列表");
        return $this->fetch($this->wap_style . '/category/category', [], $this->replace);
    }
}