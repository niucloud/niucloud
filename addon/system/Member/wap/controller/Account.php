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

namespace addon\system\Member\wap\controller;

use app\common\controller\BaseSite;

/**
 * 会员账户 控制器
 * 创建时间：2018年8月31日16:55:48
 */
class Account extends BaseSite
{
    
    protected $replace = [];
    
    public function __construct()
    {
        parent::__construct();
        $this->replace = [
            'ADDON_NC_WAP_ACCOUNT_CSS' => __ROOT__ . '/addon/system/Member/wap/view/'.$this->wap_style.'/public/css',
            'ADDON_NC_WAP_ACCOUNT_JS' => __ROOT__ . '/addon/system/Member/wap/view/'.$this->wap_style.'/public/js',
            'ADDON_NC_WAP_ACCOUNT_IMG' => __ROOT__ . '/addon/system/Member/wap/view/'.$this->wap_style.'/public/img',
        ];
    }
	/**
	 * 余额账户
	 */
	public function balance()
	{
	    $this->assign("title", "余额明细");
	    return $this->fetch($this->wap_style . '/account/balance', [], $this->replace);
	}
    /**
     * 积分账户
     */
    public function integral()
    {
        $this->assign("title", "积分明细");
        return $this->fetch($this->wap_style . '/account/integral', [], $this->replace);
    }
}