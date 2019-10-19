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

namespace addon\system\Sms\admin\controller;

use addon\system\Message\common\model\Message;
use addon\system\Sms\common\model\SmsSend;
use app\common\controller\BaseAdmin;
use addon\system\Sms\common\model\MessageRecords;
use app\common\model\Site;


class Config extends BaseAdmin
{
	protected $replace = [];
	protected $site_id = 0;
	
	public function __construct()
	{
		parent::__construct();
		$this->replace = [
			'ADDON_NS_SMS_IMG' => __ROOT__ . '/addon/system/Sms/admin/view/public/image',
			'ADDON_NS_SMS_JS' => __ROOT__ . '/addon/system/Sms/admin/view/public/js'
		];
	}
	
	/**
	 * 概况
	 */
	public function index()
	{
		if (IS_AJAX) {
			$list = hook('getSmsConfig', [ 'site_id' => $this->site_id ]);
			return success([ "count" => count($list), "list" => $list ]);
		}
		return $this->fetch('config/index', [], $this->replace);
	}
	
	/**
	 * 设置短信配置
	 */
	public function setSmsConfig()
	{
		$name = input('name', '');
		hook('setSiteSmsConfig', [ 'name' => $name, 'site_id' => $this->site_id ]);
	}
	
	/**
	 * 短信记录
	 */
	public function smsList()
	{
		$site_sms_list = new MessageRecords();
		$site_id = request()->siteid();
		if (IS_AJAX) {
			$condition = [ 'site_id' => $site_id ];
			$page = input('page', 1);
			$field = '*';
			$order = 'create_time desc';
			$limit = PAGE_LIST_ROWS;
			$list = $site_sms_list->getSmsMessageReocrdsPageList($condition, $page, $limit, $order, $field);
			return $list;
		}
		$this->assign('statistics_arr', '');
		return $this->fetch('config/smsList', [], $this->replace);
	}
	
	/**
	 * 修改插件启用状态
	 */
	public function setConfigStatus()
	{
		if (IS_AJAX) {
			$name = input('name', '');
			$status = input('status', 0);
			$site_model = new Site();
			if ($status == 1) {
				hook("closeSms", [ "site_id" => $this->site_id ]);
			}
			$res = $site_model->setSiteConfig([ 'name' => $name, 'status' => $status, 'site_id' => $this->site_id ]);
			return $res;
		}
	}
	
	/**
	 * 短信模板
	 */
	public function template()
	{
		
		if (IS_AJAX) {
			$message_model = new Message();
			$condition = [ "keyword" => [ "in", [ "REGISTER", "FIND_PASSWORD", "BING_MOBILE", "REGISTER_SUCCESS" ] ] ];
			$page = input('page', 1);
			$field = '*';
			$order = '';
			$limit = PAGE_LIST_ROWS;
			$list = $message_model->getMessageTypePageList($condition, $page, $limit, $order, $field);
			return $list;
		} else {
			return $this->fetch('config/template', [], $this->replace);
		}
	}
	
	/**
	 * 编辑
	 */
	public function editTemplate()
	{
		$keyword = input("keyword", "");
		$addon_name = "Sms";
		$res = hook("doEditMessage", [ 'keyword' => $keyword, 'site_id' => 0, "name" => $addon_name ]);
		if (empty($res)) {
			$this->error("当前消息发送方式暂未开启!");
		}
	}

    /**
     * 发送短信
     * @return \multitype
     */
    public function smsSend(){
        if (IS_AJAX) {
            $id = input('id', '');
            $params = array(
                "site_id" => 0,
                "id" => $id
            );
            $sms_send = new SmsSend();
            $result = $sms_send->sendSms($params);
            return $result;
        }
    }
	
}