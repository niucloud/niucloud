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
namespace addon\system\Wechat\sitehome\controller;

use addon\system\Wechat\common\model\Wechat;

/**
 * 微信留言控制器
 */
class Customservice extends Base
{
	/**
	 * 客服人员列表
	 */
	public function index()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$field = '*';
			$limit = input("limit", PAGE_LIST_ROWS);
			$condition = [ 'site_id' => $this->siteId ];
			$order = input('order', '');
			$wechat_model = new Wechat();
			$customservices_list = $wechat_model->getWechatCustomservicesList($condition, $page, $limit, $order, $field);
			return $customservices_list;
		}
		return $this->fetch('customservice/index', [], $this->replace);
	}
	
	/**
	 * 添加客服账号
	 * @return array
	 */
	public function saveWechatCustomservices()
	{
		$nickname = input("nickname", "");
		$wechat_model = new Wechat();
		$result = $wechat_model->saveWechatCustomservices($nickname, $this->siteId);//邀请绑定客服人员
		return $result;
	}
	
	/**
	 * 更新客服列表
	 */
	public function updateWechatCustomservicesList()
	{
		$wechat_model = new Wechat();
		$result = $wechat_model->updateWechatCustomservicesList($this->siteId);
		return $result;
		
	}
	
	/**
	 * 绑定客服
	 */
	public function bindingCustomservices()
	{
		$kf_account = input("kf_account", "");
		$wx_account = input("wx_account", "");
		$id = input("id", "");
		$wechat_model = new Wechat();
		$result = $wechat_model->bindingWechatCustomservices($kf_account, $wx_account, $id, $this->siteId);//邀请绑定客服人员
		return $result;
	}
	
	/**
	 * 删除客服
	 * @return \addon\system\Wechat\common\model\multitype
	 */
	public function deleteCustomservices()
	{
		$kf_account = input("account", "");
		$id = input("id", "");
		$wechat_model = new Wechat();
		$result = $wechat_model->deleteCustomservices($kf_account, $id, $this->siteId);//邀请绑定客服人员
		return $result;
	}
	
}