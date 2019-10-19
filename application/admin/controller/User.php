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


use app\common\controller\BaseAdmin;
use app\common\model\Auth;
use app\common\model\User as UserModel;

/**
 * 系统用户
 */
class User extends BaseAdmin
{
	
	public function index()
	{
		$this->redirect('user/user_list');
	}
	
	/**
	 * 用户列表
	 */
	public function userlist()
	{
		if (IS_AJAX) {
			$page_index = input('page', 1);
			$page_size = input('limit', PAGE_LIST_ROWS);
			$condition = [];
			$status = input('status', "");
			if (!empty($status)) {
				$condition['status'] = $status;
			}
			
			$is_admin = input('is_admin', "");
			if (!empty($is_admin)) {
				$condition['is_admin'] = $is_admin;
			}
			
			$search_keys = input('search_keys', "");
			if (!empty($search_keys)) {
				$condition['username|nick_name|mobile'] = [ 'like', '%' . $search_keys . '%' ];
			}
			
			$user_model = new UserModel();
			$list = $user_model->getUserPageList($condition, $page_index, $page_size, "register_time desc");
			return $list;
		}
		return $this->fetch('user/user_list');
	}
	
	/**
	 * 添加用户
	 * @return mixed
	 */
	public function addUser()
	{
		if (IS_AJAX) {
			$username = input('username', '');
			$password = input('password', '');
			$group_id = input('group_id', '');
			$mobile = input('mobile', '');
			$real_name = input('real_name', '');
			$user_model = new UserModel();
			$res = $user_model->addUser([ 'mobile' => $mobile, 'password' => $password, 'username' => $username, 'nick_name' => $real_name, 'real_name' => $real_name ], $group_id);
			return $res;
		}
		
		$Auth = new Auth();
		$condition = [
			'site_id' => 0,
			'is_system' => [ '<>', 1 ],
			'status' => 1
		];
		$list = $Auth->getGroupList($condition);
		$this->assign('group_list', $list['data']);
		return $this->fetch('user/add_user');
	}
	
	/**
	 * 修改用户
	 */
	public function editUser()
	{
		$user_model = new UserModel();
		if (IS_AJAX) {
			$uid = input('uid', '');
			$headimg = input('headimg', '');
			$password = input('password', '');
			$nick_name = input('nick_name', '');
			$real_name = input('real_name', '');
			$username = input('username', '');
			$mobile = input('mobile', '');
			$group_id = input('group_id', '');
			$data = [];
			if (!empty($headimg)) $data['headimg'] = $headimg;
			if (!empty($password)) $data['password'] = data_md5($password);
			if (!empty($nick_name)) $data['nick_name'] = $nick_name;
			if (!empty($real_name)) $data['real_name'] = $real_name;
			if (!empty($username)) $data['username'] = $username;
			if (!empty($mobile)) $data['mobile'] = $mobile;
			
			$res = $user_model->editUser($data, [ 'uid' => $uid ], $group_id);
			return $res;
		}
		
		$uid = input('uid', '');
		$this->assign('uid', $uid);
		
		$tab = input('tab', "basic_info");
		$this->assign('tab', $tab);
		
		$user_info = $user_model->getUserInfo([ 'uid' => $uid ]);
		$this->assign("local_user_info", $user_info['data']);
		
		$site_user_info = $user_model->getSiteUserInfo([ 'uid' => $uid ]);
		$this->assign("site_user_info", $site_user_info['data']);
		
		$Auth = new Auth();
		$condition = [
			'site_id' => 0,
			'is_system' => [ '<>', 1 ],
			'status' => 1
		];
		$list = $Auth->getGroupList($condition);
		$this->assign('group_list', $list['data']);
		
		return $this->fetch('user/edit_user');
	}
	
	/**
	 * 用户密码修改
	 */
	public function editUserPwd()
	{
		if (IS_AJAX) {
			$uid = input('uid', ''); //用户ID
			$old_pass = input('old_pass', ''); //原密码
			$new_pass = input('password', ''); //新密码
			
			$user_model = new UserModel();
			$res = $user_model->modifyUserPassword($uid, $old_pass, $new_pass);
			return $res;
		}
	}
	
	/**
	 * 删除管理员
	 */
	public function deleteUser()
	{
		$uid = input('uid', 0);
		$res = error();
		if (!empty($uid)) {
			$condition = [ 'uid' => $uid ];
			$user_model = new UserModel();
			$res = $user_model->deleteUser($condition);
		}
		return $res;
	}
	
	/**
	 * 设置运营者的 状态
	 */
	public function setUserStatus()
	{
		$uid = input('uid', 0);
		$status = input('status', 0);
		$user_model = new UserModel();
		$res = $user_model->setUserStatus($uid, $status);
		return $res;
	}
	
	/**
	 * 用户组列表
	 */
	public function groupList()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$condition = [ 'site_id' => 0 ];
			$auth_model = new Auth();
			$list = $auth_model->getGroupPageList($condition, $page, $limit);
			return $list;
		}
		return $this->fetch('user/group_list');
	}
	
	/**
	 * 编辑用户组状态
	 */
	public function setGroupStatus()
	{
		$group_id = input('group_id', 0);
		$status = input('status', 0);
		$auth_model = new Auth();
		$res = $auth_model->modifyGroupStatus([ 'status' => $status ], [ 'group_id' => $group_id, 'site_id' => 0 ]);
		return $res;
	}
	
	/**
	 * 删除用户
	 */
	public function deleteGroup()
	{
		if (IS_AJAX) {
			$auth_model = new Auth();
			$group_id = input('group_id', 0);
			$condition = [];
			$condition["is_system"] = 0;
			$condition['group_id'] = [ "in", $group_id ];
			$condition['site_id'] = 0;
			
			$res = $auth_model->deleteGroup($condition);
			return $res;
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
	 * 添加管理用户组
	 */
	public function addGroup()
	{
		$auth_model = new Auth();
		if (IS_AJAX) {
			$group_name = input('group_name', '');
			$group_array = input('group_array', '');
			$data = array(
				'site_id' => 0,
				'group_name' => $group_name,
				'status' => 1,
				'is_system' => 0,
				'array' => $group_array,
				'create_time' => time()
			);
			$res = $auth_model->addGroup($data);
			return $res;
		}
		$menu_list = $auth_model->getMenuList([ 'module' => 'ADMIN' ]);
		
		$tree = $this->listToTree($menu_list['data'], 'name', 'menu_pid', '', 'child_list', []);
		$this->assign('tree_data', json_encode($tree));
		
		return $this->fetch('user/add_group');
	}
	
	/**
	 *  修改管理用户组
	 */
	public function editGroup()
	{
		$auth_model = new Auth();
		if (IS_AJAX) {
			$group_id = input('group_id', 0);
			$site_id = input('site_id', 0);
			$group_name = input('group_name', '');
			$group_array = input('group_array', '');
			$data = array(
				'site_id' => $site_id,
				'group_name' => $group_name,
				'status' => 1,
				'is_system' => 0,
				'array' => $group_array,
				'update_time' => time()
			);
			$condition['site_id'] = $site_id;
			$condition['group_id'] = $group_id;
			$res = $auth_model->editGroup($data, $condition);
			return $res;
		}
		$menu_list = $auth_model->getMenuList([ 'module' => 'ADMIN' ]);
		$group_id = input('group_id', '');
		$site_id = input('site_id', '');
		$group_info = $auth_model->getGroupInfo([ 'group_id' => $group_id, 'site_id' => $site_id ]);
		$group_array = $group_info['data']['array'];
		$user_tree_list = explode(',', $group_array);
		
		$tree = $this->listToTree($menu_list['data'], 'name', 'menu_pid', '', 'child_list', $user_tree_list);
		$this->assign('tree_data', json_encode($tree));
		
		$this->assign('group_id', $group_id);
		$this->assign('site_id', $site_id);
		$this->assign('group_name', $group_info['data']['group_name']);
		return $this->fetch('user/edit_group');
	}
}