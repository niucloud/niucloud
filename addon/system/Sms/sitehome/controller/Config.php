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

namespace addon\system\Sms\sitehome\controller;

use addon\system\Sms\common\model\SmsSend;
use app\common\controller\BaseSiteHome;
use addon\system\Sms\common\model\MessageRecords;
use app\common\model\Site;


class Config extends BaseSiteHome
{
	protected $replace = [];
	
	public function __construct()
	{
		parent::__construct();
		$this->replace = [
			'ADDON_NS_SMS_IMG' => __ROOT__ .'/addon/system/Sms/sitehome/view/public/image',
			'ADDON_NS_SMS_CSS' => __ROOT__ .'/addon/system/Sms/sitehome/view/public/css',
		];
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
		// 查询签名
		$site_model = new Site();
		$signature = $site_model->getSiteConfigInfo([ 'name' => 'NC_SITE_SIGNATURE', 'site_id' => $site_id ], 'value');
		$this->assign('signature', $signature['data']['value']);
		$this->assign('statistics_arr', '');
		// 查询短信记录数量
		$sms_num['total_num'] = $site_sms_list->getSmsCount([ 'site_id' => $site_id ]);
		$sms_num['send_fail_num'] = $site_sms_list->getSmsCount([ 'site_id' => $site_id, 'status' => -1 ]);
		$sms_num['send_success_num'] = $site_sms_list->getSmsCount([ 'site_id' => $site_id, 'status' => 1 ]);
		$sms_num['to_be_send_num'] = $site_sms_list->getSmsCount([ 'site_id' => $site_id, 'status' => 0 ]);
		$this->assign('sms_num', $sms_num);
		return $this->fetch('config/smsList',[],$this->replace);
	}
	
	/*
	 * 修改插件启用状态
	 */
	public function setConfigStatus()
	{
		if (IS_AJAX) {
			$name = input('name', '');
			$status = input('status', 0);
			$site_model = new Site();
			if ($status == 1) {
				hook("closeSms", [ "site_id" => SITE_ID ]);
			}
			$res = $site_model->setSiteConfig([ 'name' => $name, 'status' => $status, 'site_id' => SITE_ID ]);
			return $res;
		}
	}
	
	/**
	 * 修改短信签名
	 */
	public function editSmsSignature()
	{
		if (IS_AJAX) {
			$signature = input('signature', '');
			$site_model = new Site();
			if (!empty($signature)) {
				$data = [
					'name' => 'NC_SITE_SIGNATURE',
					'title' => '站点签名',
					'value' => $signature,
					'site_id' => request()->siteid()
				];
				$res = $site_model->setSiteConfig($data);
				return $res;
			}
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
                "site_id" => $this->siteId,
                "id" => $id
            );
            $sms_send = new SmsSend();
            $result = $sms_send->sendSms($params);
            return $result;
        }
    }
	
}