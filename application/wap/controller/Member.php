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
namespace app\wap\controller;

use app\common\controller\BaseSite;

/**
 * 会员中心
 */
class Member extends BaseSite
{
	protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'
	
	public function __construct()
	{
		parent::__construct();

        if (!empty($this->access_token)) {
            return $this->getDiyView([ "name" => "DIYVIEW_MEMBER" ]);
        } else {
            $this->redirect(url('wap/login/login'));
        }
	}

    //个人中心
    public function index()
    {
        if (!empty($this->access_token)) {
            return $this->getDiyView([ "name" => "DIYVIEW_MEMBER" ]);
        } else {
            $this->redirect(url('wap/login/login'));
        }
    }

	
}