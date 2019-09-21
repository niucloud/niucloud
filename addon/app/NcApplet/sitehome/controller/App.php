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
namespace addon\app\NcApplet\sitehome\controller;

use addon\app\NcApplet\common\model\Applet as AppletModel;
use app\common\model\Addon;
use app\common\model\DiyView as DiyViewModel;
use app\common\model\Notice as NoticeModel;

/**
 * 应用单页入口
 */
class App extends Base
{
	
	/**
	 * 应用单页入口
	 */
	public function index()
	{
		//功能快捷入口
		$addon_name = input("addon_name", "");
		
		$notice_model = new NoticeModel();
		
		//系统公告
		$notice_list = $notice_model->getNoticePageList([ 'is_display' => 1 ], 1, PAGE_LIST_ROWS, 'create_time desc', 'notice_id,title,create_time');
		$this->assign("notice_list", $notice_list['data']);
		
		$diy_view_model = new DiyViewModel();
		$condition = [
			'ncl.addon_name' => $addon_name
		];
		$quick_entry = $diy_view_model->getDiyLinkList($condition, "ncl.addon_name,ncl.name,ncl.title,ncl.h5_url,ncl.icon");
		$this->assign("quick_entry", $quick_entry['data']);
		
		return $this->fetch('app/index', [], $this->replace);
	}
	
	/**
	 * 更多应用
	 */
	public function moreApp()
	{
		
		$addon = new Addon();
		$addons_list = $addon->getSiteAddonModuleList($this->siteId);
		$addons_list = $addons_list['data'];
		
		$wechat_model = new AppletModel();
		$quick_entry = $wechat_model->getAppletQuickEntryConfig($this->siteId);
		foreach ($addons_list as $k => $v) {
			$addons_list[ $k ]['sort'] = -1;
			$addons_list[ $k ]['is_exist'] = false;
		}
		
		$addons_list = array_values($addons_list);
		
		if (!empty($quick_entry['data']['value'])) {
			$value = $quick_entry['data']['value'];
			foreach ($addons_list as $k => $v) {
				foreach ($value as $ck => $cv) {
					if ($cv['addon_name'] == $v['name']) {
						$addons_list[ $k ]['is_exist'] = true;
						$addons_list[ $k ]['sort'] = $cv['sort'];
					}
				}
			}
			
			//根据字段sort对数组进行降序排列
			$sort_arr = array_column($addons_list, 'sort');
			if (!empty($sort_arr)) {
				array_multisort($sort_arr, SORT_DESC, $addons_list);
			}
		}
		
		$this->assign("is_system", $this->groupInfo['is_system']);
		$this->assign("addons_list", $addons_list);
		
		return $this->fetch('app/more_app', [], $this->replace);
	}
	
	/**
	 * 应用快捷入口
	 */
	public function quickEntry()
	{
		if (IS_AJAX) {
			
			//插件名称
			$addon_name = input("addon_name", "");
			
			$diy_view_model = new DiyViewModel();
			$condition = [
				'ncl.addon_name' => $addon_name
			];
			$quick_entry = $diy_view_model->getDiyLinkList($condition, "ncl.addon_name,ncl.name,ncl.title,ncl.h5_url,ncl.weapp_url,ncl.aliapp_url,ncl.baiduapp_url,ncl.icon");
			
			$res['code'] = $quick_entry['code'];
			$res['message'] = $quick_entry['message'];
			$res['data'] = [
				'count' => !empty($quick_entry['data']) ? count($quick_entry['data']) : 0,
				'list' => $quick_entry['data']
			];
			
			return $res;
		}
		
		//插件名称
		$addon_name = input("addon_name", "");
		$this->assign("addon_name", $addon_name);
		
		return $this->fetch('app/quick_entry', [], $this->replace);
	}
	
	/**
	 * 添加业务应用菜单中
	 */
	public function addAppMenu()
	{
		if (IS_AJAX) {
			$addon_name = input("addon_name", "");
			
			$wechat_model = new AppletModel();
			$data = [];
			$info = $wechat_model->getAppletQuickEntryConfig($this->siteId);
			$info = $info['data'];
			$is_add = true;
			
			if (!empty($info['value'])) {
				$data = $info['value'];
				
				//获取排序号
				$sort = array_column($data, 'sort');
				rsort($sort);
				
				//防重复添加
				foreach ($data as $k => $v) {
					
					if ($v['addon_name'] == $addon_name) {
						$is_add = false;
						break;
					}
				}
				
				//在开头添加
				array_unshift($data, [
					'addon_name' => $addon_name,
					'sort' => ++$sort[0]
				]);
			} else {
				$data[] = [
					'addon_name' => $addon_name,
					'sort' => 0
				];
			}
			
			$res = error();
			if ($is_add) {
				$res = $wechat_model->setAppletQuickEntryConfig($this->siteId, $data);
			}
			return $res;
		}
	}
	
	/**
	 * 置顶
	 */
	public function stickApp()
	{
		
		if (IS_AJAX) {
			$addon_name = input("addon_name", "");
			
			$wechat_model = new AppletModel();
			$data = [];
			$info = $wechat_model->getAppletQuickEntryConfig($this->siteId);
			$info = $info['data'];
			
			if (!empty($info['value'])) {
				$data = $info['value'];
				
				//获取排序号
				$sort = array_column($data, 'sort');
				rsort($sort);
				
				//设置排序号
				foreach ($data as $k => $v) {
					
					if ($v['addon_name'] == $addon_name) {
						$data[ $k ]['sort'] = ++$sort[0];
						break;
					}
				}
				
				//根据sort字段对数组倒序排列
				$sort_arr = array_column($data, 'sort');
				array_multisort($sort_arr, SORT_DESC, $data);
				
			}
			
			$res = $wechat_model->setAppletQuickEntryConfig($this->siteId, $data);
			return $res;
		}
	}
	
	/**
	 * 移除业务应用菜单
	 */
	public function deleteAppMenu()
	{
		if (IS_AJAX) {
			$addon_name = input("addon_name", "");
			
			$wechat_model = new AppletModel();
			$info = $wechat_model->getAppletQuickEntryConfig($this->siteId);
			$info = $info['data'];
			$res = error();
			if (!empty($info['value'])) {
				$data = $info['value'];
				$is_del = false;//是否匹配到
				foreach ($data as $k => $v) {
					if ($v['addon_name'] == $addon_name) {
						unset($data[ $k ]);
						$is_del = true;
					}
				}
				if ($is_del) {
					$data = array_values($data);
					$res = $wechat_model->setAppletQuickEntryConfig($this->siteId, $data);
				}
			}
			
			return $res;
		}
	}
	
}