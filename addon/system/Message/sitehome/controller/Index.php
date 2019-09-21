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
namespace addon\system\Message\sitehome\controller;

use addon\system\Message\common\model\SiteMessage;
use app\common\model\Addon;
use app\common\model\Auth;

class Index extends Base
{
	public $replace;
	
	public function __construct()
	{
		parent::__construct();
		$this->replace = [
			'ADDON_NS_MSGTPL_IMG' => __ROOT__ . '/addon/system/Message/sitehome/view/public/img',
		];
	}
	
	public function index()
	{
		$message_model = new SiteMessage();
		$addon_model = new Addon();
        $site_module_info = $addon_model->getSiteSystemModule($this->siteId);
        $condition = array(
            "nmt.addon" => ["in", $site_module_info["data"]["system"]]
        );
		$list = $message_model->getSiteMessageTypeViewList($this->siteId, $condition, "*,nmt.addon");
		$this->assign('list', $list);
		return $this->fetch('index/index', [], $this->replace);
	}
	
	/**
	 * 编辑
	 */
	public function edit()
	{
		$keyword = input("keyword", "");
		$addon_name = input("addon_name", "");
		$res = hook("doEditMessage", [ 'keyword' => $keyword, 'site_id' => SITE_ID, "name" => $addon_name ]);
		if (empty($res)) {
			$this->error("当前消息发送方式暂未开启!");
		}
	}

    /**
     * 应用模块消息模板管理
     */
	public function moduleMessageType(){
        $addon_name = input("addon_name", "");//应用名称

        $auth_model = new Auth();
        if (!$auth_model->checkModuleAuth($addon_name, $this->groupInfo, "auth_page_array")) {
            $this->error("当前操作无权限！");
        }
        $message_model = new SiteMessage();
        $condition = array(
            "nmt.addon" => $addon_name
        );
        $list = $message_model->getSiteMessageTypeViewList($this->siteId, $condition, "*,nmt.addon");
        $this->assign('list', $list);
        return $this->fetch('index/index', [], $this->replace);
    }
}