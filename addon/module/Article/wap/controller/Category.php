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

namespace addon\module\Article\wap\controller;

use app\common\controller\BaseSite;

/**
 * 文章分类 控制器
 * 创建时间：2018年8月31日16:55:48
 */
class Category extends BaseSite
{
    
    protected $replace = [];
    
    public function __construct()
    {
        parent::__construct();
        $this->replace = [
            'ADDON_NC_WAP_ARTICLE_CSS' => __ROOT__ . '/addon/module/Article/wap/view/'.$this->wap_style.'/public/css',
            'ADDON_NC_WAP_ARTICLE_JS' => __ROOT__ . '/addon/module/Article/wap/view/'.$this->wap_style.'/public/js',
            'ADDON_NC_WAP_ARTICLE_IMG' => __ROOT__ . '/addon/module/Article/wap/view/'.$this->wap_style.'/public/img',
        ];
    }
	/**
	 * 文章分类 列表
	 * 创建时间：2018年8月31日17:22:45
	 */
	public function category()
	{
	    $this->assign("title", "文章分类");
	    return $this->fetch($this->wap_style . '/category/category', [], $this->replace);
	}
	
}