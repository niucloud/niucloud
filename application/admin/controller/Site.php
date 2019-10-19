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

namespace app\admin\controller;

use app\common\model\Site as SiteModel;
use app\common\controller\BaseAdmin;
use app\common\model\Addon;
use app\common\model\User as UserModel;


/**
 * 站点控制器
 */
class Site extends BaseAdmin
{
	
	public function index()
	{
		$this->redirect('site/lists');
	}
	
	/**
	 * 站点列表
	 * @return mixed
	 */
	public function siteList()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$search_text = input('search_text', '');
			$search_type = input('search_type', '');
			$uid = input('uid', 0);
			$condition = [
				'nsu.site_id' => [ 'neq', 0 ]
			];
			if ($search_text) {
				$condition['ns.site_name'] = [ 'like', '%' . $search_text . '%' ];
			}
			if ($search_type) {
				$condition['ns.addon_app'] = $search_type;
			}
			if (!empty($uid)) {
				$condition['nsu.uid'] = $uid;
			}
			$order = "ns.create_time desc";
			$site_model = new SiteModel();
			$site_list = $site_model->getSitePageListByUid($condition, $page, $limit, $order);
			return $site_list;
		}
		$uid = input('uid', 0);
		$this->assign('uid', $uid);
		$addon_model = new Addon();
		$addon_list = $addon_model->getAddonList([ 'type' => 'ADDON_APP', 'visble' => 1 ]);
		$this->assign('addon_list', $addon_list['data']);
		return $this->fetch('site/site_list');
	}
	
	/**
	 * 站点具体模块信息
	 */
	public function getModuleInformationList()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$uid = input('uid', 0);
			$site_id = input('site_id', 0);
			$condition = [];
			if (!empty($uid)) {
				$condition['uid'] = $uid;
			}
			if (!empty($site_id)) {
				$condition['site_id'] = $site_id;
			}
			$site_model = new SiteModel();
			$site_list = $site_model->getModuleInformationList($condition, $page, $limit);
			return $site_list;
		}
	}
	
	/**
	 * 站点详情
	 * @return mixed
	 */
	public function siteDetail()
	{
		$site_model = new SiteModel();
		
		$site_id = SITE_ID;
		$this->assign('site_id', $site_id);
		
		$condition = [];
		$condition['nsu.site_id'] = $site_id;
		$site_result = $site_model->getSitePageListByUid($condition);
		$site_info = $site_result['data']["list"][0];
		$this->assign('site_info', $site_info);
		
		$uid = $site_info["uid"];
		$this->assign('uid', $uid);
		
		$tab = input('tab', "basic_info");
		$this->assign('tab', $tab);
		
		$user_model = new UserModel();
		$user_info = $user_model->getUserInfo([ 'uid' => $uid ]);
		$this->assign("user_info", $user_info['data']);
		
		return $this->fetch('site/site_detail');
	}
	
	/**
	 *
	 * 设置站点的 状态
	 */
	public function setStatus()
	{
		$site_id = input('site_id', '');
		$status = input('status', '');
		if (empty($site_id)) {
			return error();
		}
		$site_model = new SiteModel();
		$res = $site_model->editSite([ 'status' => $status ], [ 'site_id' => $site_id ]);
		return $res;
	}
	
	public function addSite()
	{
		if (IS_AJAX) {
			$data = input('post.');
			$data['uid'] = UID;
			$data['create_time'] = time();
			$site_model = new SiteModel();
			$res = $site_model->addSite($data);
			return $res;
		}
		
		$this->assign('user_info', $this->userInfo);
		$addon_model = new Addon();
		$addon_list = $addon_model->getAddonList([ 'type' => 'ADDON_APP', 'visble' => 1 ]);
		
		$list_new = $addon_list;
		foreach ($addon_list['data'] as $k => $v) {
			$addon_info_result = $addon_model->getAddonInfo([ "name" => $v["name"] ]);
			$addon_info = $addon_info_result["data"];
			
			$v["support_app_type"] = getSupportPort($addon_info["support_app_type"]);
			
			$list_new['data'][ $k ] = $v;
			unset($v);
		}
		
		$this->assign("title", "新建站点");
		$this->assign('addon_list', $list_new['data']);
		return $this->fetch('site/add_site');
	}
	
	/**
	 * 删除站点
	 */
	public function deleteSite()
	{
		if (IS_AJAX) {
			$site_id = input('site_id', 0);
			if (!$site_id) {
				return error('', 'SITE_DELETE_FAIL_NO_SITEID');
			}
			$site_model = new SiteModel();
			$res = $site_model->deleteSite($site_id, UID);
			return $res;
		}
	}
	
	public function copySite()
	{
		if (IS_AJAX) {
			$site_id = input('site_id', 0);
			if (!$site_id) {
				return error('', 'SITE_COPY_FAIL_NO_SITEID');
			}
			$site_model = new SiteModel();
			$res = $site_model->copySite($site_id);
			return $res;
		}
	}
	
	//批量删除站点方法，开发测试用，正式发布删除该方法
	public function deleteSiteTest()
	{
		$site_id = input('site_id', 0);
		$site_model = new SiteModel();
		$site_id_list = $site_model->getSiteList([ 'site_id' => [ '<=', $site_id ] ], "site_id");
		foreach ($site_id_list['data'] as $k => $v) {
			$res = $site_model->deleteSite($v['site_id'], UID);
		}
		var_dump($res);
	}
	
	//批量删除站点，开发测试用，正式发布删除该方法
	public function deletesite1()
	{
		$site_model = new SiteModel();
		$site_list = $site_model->getSiteList();
		for ($i = 0; $i < 10; $i++) {
			$res = $site_model->deleteSite($site_list['data'][ $i ]['site_id'], UID);
		}
		
	}
	
}