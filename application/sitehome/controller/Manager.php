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

namespace app\sitehome\controller;

use app\common\model\Addon;
use app\common\model\User;
use app\common\model\Auth;
use app\common\controller\BaseSiteHome;
use app\common\model\Site;
use think\Cache;

/**
 * 管理员权限
 * @author Administrator
 *
 */
class Manager extends BaseSiteHome
{
	/**
	 * 用户列表
	 */
	public function index()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$site_id = $this->siteId;
			$limit = input('limit', PAGE_LIST_ROWS);
			$user_model = new User();
			$res = $user_model->getSiteUserPageList($site_id, $page, $limit);
			return $res;
		}
		//分组
		$auth_model = new Auth();
		$list = $auth_model->getGroupList([ 'site_id' => $this->siteId ]);
		$this->assign('group_list', $list['data']);
		return $this->fetch('Manager/index');
	}
	
	/**
	 * 添加用户
	 */
	public function addUser()
	{
		if (IS_AJAX) {
			$mobile = input('mobile', '');
			$nick_name = input('nick_name', '');
			$username = input('username', '');
			$password = input('password', '');
			$sms_code = input('sms_code', '');
			$sms_result = hook("getSiteSmsType", [ "site_id" => 0 ]);//判断凭条是否配置的短信
			
			if (!empty($sms_result[0]["data"])) {
				if (empty($mobile) || empty($sms_code)) {
					return error([], "手机号和验证码不可为空!");
				}
				$key = md5("mobile_code_" . 0 . "_" . $mobile);
				$code = Cache::get($key);
				if (empty($code)) {
					return error("", "短信动态码已失效");
				}
				if ($sms_code != $code) {
					return error("", "短信动态码错误");
				}
			}
			$data = array(
				"mobile" => $mobile,
				"nick_name" => $nick_name,
				"real_name" => $nick_name,
				"username" => $username,
				"password" => $password,
				"site_id" => $this->siteId,
			);
			
			$user_model = new User();
			$res = $user_model->addUser($data);
			return $res;
		}
	}
	
	/**
	 * 编辑用户
	 */
	public function editUser()
	{
		if (IS_AJAX) {
			$uid = input('uid', '');
			$group_id = input('group_id', '');
			$nick_name = input('edit_nick_name', '');
			$mobile = input('edit_mobile', '');
			$user_model = new User();
			$res = $user_model->editSiteUser([ 'group_id' => $group_id ], [ 'uid' => $uid ], [ 'nick_name' => $nick_name, 'mobile' => $mobile ]);
			return $res;
		}
	}
	
	/**
	 * 用户站点信息
	 */
	public function siteUserInfo()
	{
		$uid = input('uid', '');
		$user_model = new User();
		$user_info = $user_model->getSiteUserInfo([ 'uid' => $uid, 'site_id' => $this->siteId ]);
		return $user_info;
	}
	
	/**
	 *  编辑用户状态
	 */
	public function setUserStatus()
	{
		$uid = input('uid', '');
		$status = input('status', 0);
		$user_model = new User();
		$res = $user_model->modifyUserStatus([ 'status' => $status ], [ 'uid' => [ 'in', $uid ], 'site_id' => $this->siteId ]);
		return $res;
	}
	
	/**
	 * 删除用户
	 */
	public function deleteUser()
	{
		if (IS_AJAX) {
			$uid = input('uid', '');
			$condition = array(
				'uid' => [ 'in', $uid ],
				'site_id' => $this->siteId
			);
			$user_model = new User();
			$res = $user_model->deleteSiteUser($condition);
			return $res;
		}
	}
	
	/**
	 * 管理组列表
	 */
	public function group()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$condition = [ 'site_id' => $this->siteId ];
			$auth_model = new Auth();
			$list = $auth_model->getGroupPageList($condition, $page, $limit);
			return $list;
		}
		return $this->fetch('Manager/group');
	}
	
	/**
	 * 添加用户组
	 */
	public function addGroup()
	{
		$auth_model = new Auth();
		if (IS_AJAX) {
			$group_name = input('group_name', '');
			$group_array = input('group_array', '');
			$addon_array = input('addon_array', '');
			$diyview_page_array = input('diyview_page_array', '');
			$auth_page_array = input('auth_page_array', '');
			
			$data = array(
				'site_id' => $this->siteId,
				'group_name' => $group_name,
				'status' => 1,
				'is_system' => 0,
				'array' => $group_array,
				'create_time' => time(),
				'addon_array' => $addon_array,
				'diyview_page_array' => $diyview_page_array,
				'auth_page_array' => $auth_page_array,
			);
			$res = $auth_model->addGroup($data);
			return $res;
		} else {
			$condition = [];
			$condition['site_id'] = $this->siteId;
			$tree = $this->getGroupTree();
			
			$this->assign("tree", $tree);
			return $this->fetch('Manager/group_add');
		}
	}
	
	/**
	 * 修改用户组
	 */
	public function editGroup()
	{
		$auth_model = new Auth();
		if (IS_AJAX) {
			$group_id = input('group_id', 0);
			$group_name = input('group_name', '');
			$group_array = input('group_array', '');
			$addon_array = input('addon_array', '');
			$diyview_page_array = input('diyview_page_array', '');
			$auth_page_array = input('auth_page_array', '');
			$data = array(
				'site_id' => $this->siteId,
				'group_name' => $group_name,
				'status' => 1,
				'is_system' => 0,
				'array' => $group_array,
				'update_time' => time(),
				'addon_array' => $addon_array,
				'diyview_page_array' => $diyview_page_array,
				'auth_page_array' => $auth_page_array,
			);
			$condition = array(
				"site_id" => $this->siteId,
				"group_id" => $group_id,
			);
			$res = $auth_model->editGroup($data, $condition);
			return $res;
		} else {
			$group_id = input('group_id', '');
			$group_info = $auth_model->getGroupInfo([ 'group_id' => $group_id, "site_id" => $this->siteId ]);
			
			$condition = [];
			$condition['site_id'] = $this->siteId;
			$tree = $this->getGroupTree($group_info['data'], $condition);
			$this->assign("tree", $tree);
			$this->assign('group_id', $group_id);
			$this->assign('group_name', $group_info['data']['group_name']);
			$addon_array = explode(",", $group_info['data']['addon_array']);
			$this->assign('addon_array', $addon_array);
			return $this->fetch('Manager/group_edit');
		}
	}
	
	/**
	 * 改进后的list_to_tree方法
	 * $list 原始数据
	 * $id 自身标识
	 * $pid 父级标识
	 * $root 根节点
	 * $child 子级数组名称
	 * $selected_arr 做默认的数据数组
	 */
	private function listToTree($list, $id, $pid, $root = 0, $child = 'child_list', $selected_arr = [])
	{
		$refer = [];
		foreach ($list as $key => $val) {
			$refer[ $val[ $id ] ] = $val;
			$refer[ $val[ $id ] ]['child_num'] = 0;
			$refer[ $val[ $id ] ]['checked'] = false;
			
		}
		
		if (count($selected_arr) > 0) {
			foreach ($selected_arr as $val) {
				if (isset($refer[ $val ])) $refer[ $val ]['checked'] = true;
			}
		}
		$tree = [];
		foreach ($refer as $key => $val) {
			if ($val[ $pid ] == $root) {
				$tree[ $key ] = &$refer[ $key ];
			} else {
				if (isset($refer[ $val[ $pid ] ])) {
					$refer[ $val[ $pid ] ][ $child ][ $key ] = &$refer[ $key ];
					$refer[ $val[ $pid ] ]['child_num'] += 1;
				}
			}
		}
		return $tree;
	}
	
	/**
	 * 编辑用户组状态
	 */
	public function setGroupStatus()
	{
		$group_id = input('group_id', 0);
		$is_system = input('is_system', 0);
		$auth_model = new Auth();
		$res = $auth_model->modifyGroupStatus([ 'is_system' => $is_system ], [ 'group_id' => $group_id, "site_id" => $this->siteId ]);
		return $res;
	}
	
	/**
	 * 删除分组
	 */
	public function deleteGroup()
	{
		if (IS_AJAX) {
			$group_id = input('group_id', 0);
			$auth_model = new Auth();
			$res = $auth_model->deleteGroup([ 'group_id' => [ "in", $group_id ], "site_id" => $this->siteId ]);
			return $res;
		}
	}
	
	/**
	 * 编辑站点信息
	 * @return \multitype
	 */
	public function editSiteInfo()
	{
		if (IS_AJAX) {
			
			$site_model = new Site();
			
			//基础信息
			$site_name = input('site_name', '');
			$icon = input('icon', '');
			$desc = input('desc', '');
			$data = [
				'site_name' => $site_name,
				'icon' => $icon,
				'desc' => $desc
			];
			$condition['site_id'] = $this->siteId;
			$res = $site_model->editSite($data, $condition);
			
			//SEO设置
			$title = input('title', '');
			$keywords = input('keywords', '');
			$description = input('description', '');
			$other = input('other', '');
			$site_seo_id = input('site_seo_id', '');
			
			$value = json_encode([ 'title' => $title, 'keywords' => $keywords, 'description' => $description, 'other' => $other ]);
			$data = [
				'name' => 'SITE_SEO_CONFIG',
				'site_id' => $this->siteId,
				'value' => $value,
				'remark' => '网站SEO设置',
				'title' => '网站SEO设置',
			];
			if ($site_seo_id == '') {
				$data['create_time'] = time();
				$res = $site_model->addSiteConfig($data);
			} else {
				$data['update_time'] = time();
				$res = $site_model->editSiteConfig($data, [ 'id' => $site_seo_id, 'site_id' => $this->siteId, 'name' => 'SITE_SEO_CONFIG' ]);
			}
			
			//备案信息
			$cip_code = input('cip_code', '');
			$public_security = input('public_security', '');
			$public_security_url = input('public_security_url', '');
			$third_count = input('third_count', '');
			$site_record_id = input('site_record_id', '');
			
			$value = json_encode([ 'cip_code' => $cip_code, 'public_security' => $public_security, 'public_security_url' => $public_security_url, 'third_count' => $third_count ]);
			$data = [
				'name' => 'SITE_RECORD',
				'site_id' => $this->siteId,
				'value' => $value,
				'remark' => '网站备案信息',
				'title' => '网站备案信息',
			];
			if ($site_record_id == '') {
				$data['create_time'] = time();
				$res = $site_model->addSiteConfig($data);
			} else {
				$data['update_time'] = time();
				$res = $site_model->editSiteConfig($data, [ 'id' => $site_record_id, 'site_id' => $this->siteId, 'name' => 'SITE_RECORD' ]);
			}
			
			//版权信息
			$pc_logo = input('pc_logo', '');
			$wap_logo = input('wap_logo', '');
			$company_name = input('company_name', '');
			$copyright_url = input('copyright_url', '');
			$copyright_desc = input('copyright_desc', '');
			$site_copyright_id = input('site_copyright_id', '');
			$value = json_encode([ 'pc_logo' => $pc_logo, 'wap_logo' => $wap_logo, 'company_name' => $company_name, 'copyright_url' => $copyright_url, 'copyright_desc' => $copyright_desc ]);
			
			$data = [
				'name' => 'SITE_COPYRIGHT',
				'site_id' => $this->siteId,
				'value' => $value,
				'remark' => '版权信息',
				'title' => '版权信息',
			];
			if ($site_copyright_id == '') {
				$data['create_time'] = time();
				$res = $site_model->addSiteConfig($data);
			} else {
				$data['update_time'] = time();
				$res = $site_model->editSiteConfig($data, [ 'id' => $site_copyright_id, 'site_id' => $this->siteId, 'name' => 'SITE_COPYRIGHT' ]);
			}
			
			return $res;
		}
	}
	
	/**
	 * 站点基本信息设置
	 */
	public function siteSetting()
	{
		$site_model = new Site();
		$condition['site_id'] = $this->siteId;
		
		//站点信息
		$site_info = $site_model->getSiteInfo($condition);
		$site_info = $site_info['data'];
		//默认生成二维码
		$qrcode_url = addon_url('');
		$path = 'attachment/' . SITE_ID . '/images';
		$name = 'site_qrcode_' . SITE_ID;
		if ($site_info['qrcode_url'] == '' || file_exists($path . '/' . $name . '.png') === false) {
			$qrcode_url = qrcode($qrcode_url, $path, $name);
			$qrcode_url = str_replace('attachment/', '', $qrcode_url);
			$site_model->editSite([ 'qrcode_url' => $qrcode_url ], [ 'site_id' => $site_info['site_id'] ]);
			$site_info['qrcode_url'] = $qrcode_url;
		}
		$this->assign('site_info', $site_info);
		
		//SEO配置信息
		$site_seo_info = $site_model->getSiteConfigInfo([ 'site_id' => $this->siteId, 'name' => 'SITE_SEO_CONFIG' ]);
		$site_seo_info = $site_seo_info['data'];
		$this->assign('site_seo_info', json_decode($site_seo_info['value'], true));
		$this->assign('site_seo_id', $site_seo_info['id']);
		
		//备案信息
		$site_record_info = $site_model->getSiteConfigInfo([ 'site_id' => $this->siteId, 'name' => 'SITE_RECORD' ]);
		$site_record_info = $site_record_info['data'];
		$this->assign('site_record_info', json_decode($site_record_info['value'], true));
		$this->assign('site_record_id', $site_record_info['id']);
		
		//版权信息
		$site_copyright_info = $site_model->getSiteConfigInfo([ 'site_id' => $this->siteId, 'name' => 'SITE_COPYRIGHT' ]);
		$site_copyright_info = $site_copyright_info['data'];
		$this->assign('site_copyright_info', json_decode($site_copyright_info['value'], true));
		$this->assign('site_copyright_id', $site_copyright_info['id']);
		
		return $this->fetch('Manager/site_setting');
	}
	
	/**
	 * 站点联系信息设置
	 */
	public function contactSetting()
	{
		$site_model = new Site();
		$condition['site_id'] = $this->siteId;
		if (IS_AJAX) {
			$value_json = input('value', '');
			$value = json_decode($value_json, true);
			$data = [
				'latitude' => $value['latitude'],
				'longitude' => $value['longitude'],
				'province' => $value['province'],
				'city' => $value['city'],
				'district' => $value['district'],
				'subdistrict' => $value['subdistrict'],
				'address' => $value['address'],
				'area_code' => $value['area_code'],
				'phone' => $value['phone'],
				'full_address' => $value['full_address'],
				'business_hours' => $value['business_hours']
			];
			$res = $site_model->editSite($data, $condition);
			return $res;
		} else {
			
			$info = $site_model->getSiteInfo($condition);
			$this->assign('info', $info['data']);
			return $this->fetch('Manager/contact_setting');
		}
	}
	
	/**
	 * 更新站点二维码
	 */
	public function updateSiteQrcode()
	{
		$site_id = $this->siteId;
		$domain = input('domain', '');
		$path = 'attachment/' . $site_id . '/images';
		$name = 'site_qrcode_' . $site_id;
		$qrcode_url = qrcode($domain, $path, $name);
		if ($qrcode_url) {
			//在更新了二维码之后立即执行数据库的更新操作 并删除原有二维码图片
			$site_model = new Site();
			$condition['site_id'] = $this->siteId;
			$info = $site_model->getSiteInfo($condition);
			if (!empty($info['data']['qrcode_url'])) {
				$path = 'attachment/' . $info['data']['qrcode_url'];
			}
			$qrcode_url = str_replace('attachment/', '', $qrcode_url);
			$data = [
				'domain' => $domain,
				'qrcode_url' => $qrcode_url,
			];
			$site_model->editSite($data, $condition);
			return success($qrcode_url);
		} else {
			return error($qrcode_url);
		}
	}
	
	/**
	 * 菜单管理
	 */
	public function menuManagement()
	{
		$auth_model = new Auth();
		if (IS_AJAX) {
			$data_arr = input();
			$data = [
				'title' => $data_arr['title'],
				'menu_pid' => $data_arr['menuPid'],
				'is_menu' => $data_arr['isMenu'],
				'desc' => $data_arr['desc'],
				'sort' => $data_arr['sort']
			];
			
			$res = $auth_model->editSiteMenu($data_arr['menuId'], $this->siteId, $data);
			return $res;
		} else {
			$menu_list = $auth_model->getSiteMenuList([ 'site_id' => $this->siteId ], '*', 'sort asc');
			
			$tree = $this->listToTree($menu_list['data'], 'name', 'menu_pid', '', 'child_list', []);
			$this->assign('tree_data', $tree);
			
			return $this->fetch('Manager/menu_management');
		}
	}
	
	/**
	 * 修改菜单基本信息
	 */
	public function editMenuBaseInfo()
	{
		if (IS_AJAX) {
			$menu_model = new Auth();
			$menu_id = input('menu_id', 0);
			$type = input('type', '');
			$value = input('value', '');
			$res = $menu_model->editMenuBaseInfo($menu_id, $type, $value, $this->siteId);
			return $res;
		}
	}
	
	/**
	 * 域名设置
	 */
	public function sitesetdomain()
	{
		$site_model = new Site();
		$condition['site_id'] = $this->siteId;
		if (IS_AJAX) {
			$value_json = input('value', '');
			$value = json_decode($value_json, true);
			$data = [
				'domain' => $value['domain'],
				'qrcode_url' => $value['qrcode_url'],
				'default_link' => $value['default_link']
			];
			$res = $site_model->editSite($data, $condition);
			return $res;
		} else {
			$info = $site_model->getSiteInfo($condition);
			$this->assign('info', $info['data']);
			return $this->fetch('Manager/site_set_domain');
		}
	}
	
	/**
	 * 会员是否注册
	 */
	public function memberPresence()
	{
		$user = new User();
		$mobile = input('mobile', '');
		$info = $user->getUserInfo([ 'username|mobile' => $mobile ]);
		if ($mobile != '' && !empty($info['data'])) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 秘钥管理
	 */
	public function secretKeyManage()
	{
		$site_model = new Site();
		$condition['site_id'] = $this->siteId;
		if (IS_AJAX) {
			$data = [
				'app_secret' => input('app_secret', '')
			];
			$res = $site_model->editSite($data, $condition);
			return $res;
		}
		$info = $site_model->getSiteInfo($condition);
		$this->assign('info', $info['data']);
		return $this->fetch('Manager/secret_key_manage');
	}
	
	/**
	 * 获取32位随机数
	 */
	public function getRandom()
	{
		if (IS_AJAX) {
			$res = random_keys(32);
			return $res;
		}
	}
	
	/**
	 * 构建权限树
	 */
	public function getGroupTree($group_info = [], $condition = [])
	{
		$addon_name = "";
		if (!empty($condition["module"])) {
			$addon_name = $condition["module"];
		}
		$select_arr = [];
		$diyview_arr = [];
		$auth_arr = [];
		
		if (!empty($group_info)) {
			$select_arr = explode(',', $group_info["array"]);//当前选中
			$diyview_arr = explode(',', $group_info["diyview_page_array"]);//当前选中
			$auth_arr = explode(',', $group_info["auth_page_array"]);//当前选中
		}
		
		$condition['site_id'] = $this->siteId;
		$auth_model = new Auth();
		$temp_menu_list = $auth_model->getSiteMenuList($condition);
		
		$addon_app = $this->siteInfo["addon_app"];
		$menu_list = [];
		//区分系统插件和应用插件
		$addon_model = new Addon();
		$addon_app_info_result = $addon_model->getAddonInfo([ "name" => $addon_app ], "preset_addon");
		$addon_app_info = $addon_app_info_result["data"];
		$preset_addon = explode(",", $addon_app_info["preset_addon"]);
		$addon_modules = explode(",", $this->siteInfo["addon_modules"]);
		$system_addon = array_intersect($preset_addon, $addon_modules);
		$system_addon[] = $addon_app;
		$other_mobules = array_diff($addon_modules, $system_addon);
		if (!empty($other_mobules)) {
			$addon_list_result = $addon_model->getAddonList([ "name" => [ "in", $this->siteInfo["addon_modules"] ] ], "name,type,category,title");
			$addon_list = $addon_list_result["data"];
			$addon_list = array_column($addon_list, null, "name");
			foreach ($other_mobules as $k => $v) {
				if (!empty($addon_name)) {
					if ($addon_name == $v) {
						$menu_list[ $v ] = [ "menu" => [], "info" => $addon_list[ $v ] ];
					}
				} else {
					$menu_list[ $v ] = [ "menu" => [], "info" => $addon_list[ $v ] ];
				}
				
			}
		}
		
		$system_addon[] = "SITEHOME";
		
		foreach ($temp_menu_list["data"] as $menu_k => $menu_v) {
			//SITEHOME的菜单默认认为是系统菜单
			if (in_array($menu_v["module"], $system_addon)) {
				if (empty($menu_list["system"])) {
					$menu_list["system"] = [ "menu" => [], "info" => [ "title" => "系统" ] ];
				}
				$menu_list["system"]["menu"][] = $menu_v;
				continue;
			}
			if (!empty($menu_list[ $menu_v["module"] ])) {
				$menu_list[ $menu_v["module"] ]["menu"][] = $menu_v;
			} else {
				if (empty($menu_list["system"])) {
					$menu_list["system"] = [ "menu" => [], "info" => [ "title" => "系统" ] ];
				}
				$menu_list["system"]["menu"][] = $menu_v;
			}

//				foreach ($addon_list["data"] as $k => $v) {
//					if ($v["name"] == $menu_v["module"]) {
//						//系统插件下的菜单
//						if ($v["type"] == "ADDON_SYSTEM") {
//							if (empty($menu_list["system"])) {
//								$menu_list["system"] = [ "menu" => [], "info" => [ "title" => "系统" ] ];
//							}
//							$menu_list["system"]["menu"][] = $menu_v;
//						} else {
//							if (empty($menu_list[ $menu_v["module"] ])) {
//								$menu_list[ $menu_v["module"] ] = [ "menu" => [], "info" => $v ];
//							}
//							$menu_list[ $menu_v["module"] ]["menu"][] = $menu_v;
//						}
//						break;
//					}
//				}
		}
		
		foreach ($menu_list as $k => $v) {
			$tree = $this->tree($v["menu"], $select_arr);
			if ($k != "system") {
				$diyview_checked = in_array($k, $diyview_arr) ? true : false;
				$auth_checked = in_array($k, $auth_arr) ? true : false;
				$tree["ADDON_AUTH"] = [ "name" => "ADDON_AUTH", "title" => "功能设置", "level" => 1, "child_list" => [], "child_num" => 0, "module" => $k, "sort" => 1, "checked" => $auth_checked ];
				$tree["ADDON_DIYVIEW"] = [ "name" => "ADDON_DIYVIEW", "title" => "页面装修", "level" => 1, "child_list" => [], "child_num" => 0, "module" => $k, "sort" => 1, "checked" => $diyview_checked ];
				
			}
			
			$menu_list[ $k ]["tree"] = json_encode($tree);
		}
		return $menu_list;
	}
	
	/**
	 *
	 * @param $data
	 * @param array $select_arr
	 * @return array
	 */
	public function tree($data, $select_arr = [])
	{
		$tree = [];
		//先获取最高级别
		if (!empty($data)) {
			$level = 0;
			$temp_data = [];
			foreach ($data as $k => $v) {
				$checked = in_array($v["name"], $select_arr) ? true : false;
				$v['child_num'] = 0;
				$v["child_list"] = [];
				$v["checked"] = $checked;
				$temp_item = $v;
				$temp_data[ $v["name"] ] = $temp_item;
				//冒泡排序
				if ($level == 0 || $v["level"] < $level) {
					$level = $v["level"];
				}
			}
			
			foreach ($temp_data as $key => $val) {
				if ($val["level"] == $level) {
					$tree[ $key ] = &$temp_data[ $key ];
				} else {
					if (isset($temp_data[ $val["menu_pid"] ])) {
						$temp_data[ $val["menu_pid"] ]["child_list"][ $key ] = &$temp_data[ $key ];
						$temp_data[ $val["menu_pid"] ]['child_num'] += 1;
					}
				}
			}
		}
		return $tree;
	}
	
	/**
	 * 查询用户信息
	 * @return \app\common\model\multitype
	 */
	public function getUserInfo()
	{
		$user_name = input("username", "");
		$user_model = new User();
		$condition = array(
			"username|mobile" => $user_name
		);
		$res = $user_model->getUserInfo($condition, "username,mobile,nick_name,uid");
		return $res;
	}
	
	/**
	 * 绑定用户权限
	 */
	public function bindSiteUser()
	{
		$uid = input('uid', 0);
		if (IS_AJAX) {
			$uid = input('uid', 0);
			$site_id = $this->siteId;
			$group_type = input('group_type', 2);
			$group_id = input('group_id', 0);
			$group_array = input('group_array', '');//权限组
			$group_name = input('group_name', '');
			$addon_array = input('addon_array', '');
			$diyview_page_array = input('diyview_page_array', '');
			$auth_page_array = input('auth_page_array', '');
			$data = array(
				"site_id" => $site_id,
				"uid" => $uid
			);
			//$group_typ为 1为已存在用户组权限    2自定义权限
			if ($group_type == 1) {
				$data["group_id"] = $group_id;
			} else {
				//创建用户组权限
				$group_data = array(
					'site_id' => $this->siteId,
					'group_name' => $group_name,
					'status' => 1,
					'is_system' => 0,
					'array' => $group_array,
					'create_time' => time(),
					'addon_array' => $addon_array,
					'diyview_page_array' => $diyview_page_array,
					'auth_page_array' => $auth_page_array,
				);
				$auth_model = new Auth();
				$res = $auth_model->addGroup($group_data);
				if ($res["code"] != 0) {
					return $res;
				}
				$data["group_id"] = $res["data"];
			}
			$user_model = new User();
			
			$res = $user_model->bingSiteUser($data);
			return $res;
		} else {
			$user_info = [];
			if ($uid > 0) {
				$user_model = new User();
				$userinfo_res = $user_model->getUserInfo([ "uid" => $uid ], "username,mobile,nick_name,uid");
				if (!empty($userinfo_res["data"])) {
					$user_info = $userinfo_res["data"];
					$site_user_result = $user_model->getSiteUserInfo([ "uid" => $uid, "site_id" => $this->siteId ]);
					$user_info["group_id"] = $site_user_result["data"]["group_id"];
				} else {
					$uid = 0;
				}
			}
			$this->assign("current_user_info", $user_info);
			$this->assign("uid", $uid);
			
			//权限组
			$tree_condition = [];
			$tree = $this->getGroupTree([], $tree_condition);
			$this->assign("tree", $tree);
			$auth_model = new Auth();
			$list = $auth_model->getGroupList([ 'site_id' => $this->siteId ]);
			$this->assign('group_list', $list['data']);
			$sms_result = hook("getSiteSmsType", [ "site_id" => 0 ]);//判断凭条是否配置的短信
			$this->assign("sms_type", $sms_result[0]["data"]);
			return $this->fetch('Manager/bind_site_user');
		}
	}
	
	/**
	 * 模块权限组管理
	 */
	public function moduleGroup()
	{
		$addon_name = input("addon_name", "");//应用名称
		
		$auth_model = new Auth();
		if (!$auth_model->checkModuleAuth($addon_name, $this->groupInfo, "auth_page_array")) {
			$this->error("当前操作无权限！");
		}
		if (IS_AJAX) {
			$site_id = $this->siteId;
			$group_type = input('group_type', 1);
			$group_id = input('group_id', 0);
			$group_array = input('group_array', '');//权限组
			$addon_array = input('addon_array', '');//权限组
			$group_name = input('group_name', '');
			$diyview_page_array = input('diyview_page_array', '');
			$auth_page_array = input('auth_page_array', '');
			
			if (($addon_array != $addon_name && !empty($addon_array)) || ($diyview_page_array != $addon_name && !empty($diyview_page_array)) || ($auth_page_array != $addon_name && !empty($auth_page_array))) {
				return error([], '只能编辑当前应用的权限');
			}
			
			$menu_list_result = $auth_model->getSiteMenuList([ "site_id" => $this->siteId, "module" => $addon_name ], "name");
			$menu_list = $menu_list_result["data"];
			$menu_data = [];
			
			foreach ($menu_list as $k => $v) {
				$menu_data[] = $v["name"];
			}
			$group_data = explode(",", $group_array);
			
			//防止权限超出
			if (!empty(array_diff($group_data, $menu_data))) {
				return error();
			}
			//$group_type为 1为已存在用户组权限    2自定义权限
			if ($group_type == 1) {
				$group_info = $auth_model->getGroupInfo([ "site_id" => $site_id, "group_id" => $group_id ]);
				if ($group_info["is_system"] == 1)
					return error([], '默认用户组不可编辑');
				
				$temp_group_data = explode(",", $group_info["data"]["array"]);
				$diff_group_data = arrDelArr($temp_group_data, $menu_data);
				$temp_group = array_merge($diff_group_data, $group_data);
				$group_string = implode(",", $temp_group);
				
				//去权限应用
				$temp_addon_array = explode(",", $group_info["data"]["addon_array"]);
				if (in_array($addon_name, $temp_addon_array)) {
					$temp_addon_array = array_diff_key($temp_addon_array, array_keys($temp_addon_array, $addon_name));
				}
				if (!empty($addon_array)) {
					$temp_addon_array[] = $addon_array;
				}
				$addon_str = implode(",", $temp_addon_array);
				
				
				//应用自定义装修页
				$temp_diyview_page_array = explode(",", $group_info["data"]["diyview_page_array"]);
				if (in_array($addon_name, $temp_diyview_page_array)) {
					$temp_diyview_page_array = array_diff_key($temp_diyview_page_array, array_keys($temp_diyview_page_array, $addon_name));
				}
				if (!empty($diyview_page_array)) {
					$temp_diyview_page_array[] = $diyview_page_array;
				}
				$diyview_page_str = implode(",", $temp_diyview_page_array);
				
				//应用权限页
				$temp_auth_page_array = explode(",", $group_info["data"]["auth_page_array"]);
				if (in_array($addon_name, $temp_auth_page_array)) {
					$temp_auth_page_array = array_diff_key($temp_auth_page_array, array_keys($temp_auth_page_array, $addon_name));
				}
				if (!empty($auth_page_array)) {
					$temp_auth_page_array[] = $auth_page_array;
				}
				$auth_page_str = implode(",", $temp_auth_page_array);
				
				$group_data = array(
					'site_id' => $site_id,
					'array' => $group_string,
					'update_time' => time(),
					'addon_array' => $addon_str,
					'diyview_page_array' => $diyview_page_str,
					'auth_page_array' => $auth_page_str,
				);
				$res = $auth_model->editGroup($group_data, [ "site_id" => $site_id, "group_id" => $group_id ]);
			} else {
				//创建用户组权限
				$group_data = array(
					'site_id' => $site_id,
					'group_name' => $group_name,
					'status' => 1,
					'is_system' => 0,
					'array' => $group_array,
					'create_time' => time(),
					'addon_array' => $addon_array,
					'diyview_page_array' => $diyview_page_array,
					'auth_page_array' => $auth_page_array,
				);
				$res = $auth_model->addGroup($group_data);
			}
			return $res;
		} else {
			
			$this->assign("title", "权限设置");
			
			//权限组
			$tree_condition = [];
			if (!empty($addon_name)) {
				$tree_condition["module"] = $addon_name;
			}
			$this->assign("addon_name", $addon_name);
			$tree = $this->getGroupTree([], $tree_condition);
			$this->assign("tree", $tree);
			$list = $auth_model->getGroupList([ 'site_id' => $this->siteId ]);
			//将不存在的公共菜单作为菜单引入权限
			foreach ($list["data"] as $k => $v) {
				$temp_auth_array = [];
				if (in_array($addon_name, explode(",", $v["diyview_page_array"]))) {
					$temp_auth_array[] = "ADDON_DIYVIEW";
				}
				if (in_array($addon_name, explode(",", $v["auth_page_array"]))) {
					$temp_auth_array[] = "ADDON_AUTH";
				}
				if (!empty($temp_auth_array)) {
					if (empty($v["array"])) {
						$list["data"][ $k ]["array"] = implode(",", $temp_auth_array);
					} else {
						$array = explode(",", $v["array"]);
						$temp_auth_array = array_merge($array, $temp_auth_array);
						$list["data"][ $k ]["array"] = implode(",", $temp_auth_array);
					}
				}
				
			}
			$this->assign('group_list', $list['data']);
			return $this->fetch('Manager/module_group');
		}
	}
	
	/**
	 *发送短信验证码
	 */
	public function sendCode()
	{
		$code = rand(100000, 999999);
		$mobile = input("mobile", '');
		if (empty($mobile)) {
			return error([], "手机号不可以为空!");
		}
		$user_model = new User();
		$exist_result = $user_model->checkMobileIsExist($mobile);
		$exist_count = $exist_result["data"];
		
		if ($exist_count > 0) {
			return error([], "当前手机号已存在!");
		}
		$data = [ "keyword" => "REGISTER", "site_id" => 0, 'code' => $code, 'support_type' => "Sms", 'mobile' => $mobile ];//仅支持短信发送
		$res = hook("sendMessage", $data);
		if ($res[0]["code"] == 0) {
			$key = md5("mobile_code_" . 0 . "_" . $mobile);
			Cache::set($key, $code, 180);
		}
		return $res[0];
	}
}