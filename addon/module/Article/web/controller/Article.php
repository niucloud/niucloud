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

namespace addon\module\Article\web\controller;
use app\common\controller\BaseSite;

/**
 * 商品
 */
class Article extends BaseSite
{
    protected $replace = [];

    public function __construct(){
        parent::__construct();
        $this->replace = [
            'ADDON_NC_ARTICLE_CSS' => __ROOT__.'/addon/module/Article/web/view/public/css',
            'ADDON_NC_ARTICLE_JS' => __ROOT__.'/addon/module/Article/web/view/public/js',
            'ADDON_NC_ARTICLE_IMG' => __ROOT__.'/addon/module/Article/web/view/public/img',
        ];
    }

    //文章列表
    public function index()
    {
        return $this->fetch('article/index',[],$this->replace);
    }

    //文章详情
    public function detail()
    {
        return $this->fetch('article/detail',[],$this->replace);
    }
}