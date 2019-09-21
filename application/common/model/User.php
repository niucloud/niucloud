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
namespace app\common\model;

use think\Db;

/**
 * 管理员模型
 */
class User
{
	
	/**
	 * 获取用户信息
	 *
	 * @param array $condition
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getUserInfo($condition, $field = '*')
	{
		$info = model('nc_user')->getInfo($condition, $field);
		return success($info);
	}
	
	/**
	 * 用户修改密码
	 *
	 * @param integer $uid
	 * @param array $old_pass
	 * @param array $new_pass
	 */
	public function modifyUserPassword($uid, $old_pass, $new_pass)
	{
		$info = model('nc_user')->getInfo([
			'uid' => $uid,
			'password' => data_md5($old_pass)
		]);
		if (!empty($info)) {
			$res = model('nc_user')->update([
				'password' => data_md5($new_pass)
			], [
				'uid' => $uid
			]);
			if ($res === false) {
				return error('', 'UNKNOW_ERROR');
			}
			return success($res);
		} else {
			return error('', 'PASSWORD_ERROR');
		}
	}
	
	/**
	 * 获取会员列表
	 *
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getUserList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$list = model('nc_user')->getList($condition, $field, $order, '', '', '', $limit);
		return success($list);
	}
	
	/**
	 * 获取会员分页列表
	 *
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getUserPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$list = model('nc_user')->pageList($condition, $field, $order, $page, $page_size);
		return success($list);
	}
	
	/**
	 * 获取某会员下面的站点列表
	 */
	public function getUserSiteList($condition = [])
	{
		$field = 'nsu.site_user_id, nsu.uid, nsu.site_id, nsu.group_id, ns.site_name, ns.icon, ns.qrcode_url, ns.`desc`, ns.create_time, ns.addon_app,
            ns.status, na.name, na.title, na.description, ng.group_name, ns.validity_time, na.type, na.icon as app_icon, na.support_app_type';
		$order = 'create_time desc';
		$alias = 'nsu';
		$join = [
			[
				'nc_site ns',
				'nsu.site_id = ns.site_id',
				'INNER'
			],
			[
				'nc_addon na',
				'ns.addon_app = na.name',
				'LEFT'
			],
			[
				'nc_group ng',
				'ng.group_id = nsu.group_id',
				'left'
			]
		];
		$list = model('nc_site_user')->getList($condition, $field, $order, $alias, $join);
		return success($list);
	}
	
	public function getUserSitePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'create_time desc', $field = '*')
	{
//		$condition ['nsu.uid'] = $uid;
//		$condition['ns.site_name'] = ['like','%' . $name . '%'];
//		$condition['ns.addon_app'] = $addon_name;
		
		$field = 'nsu.site_user_id, nsu.uid, nsu.site_id, nsu.group_id, ns.site_name, ns.icon, ns.qrcode_url, ns.`desc`, ns.create_time, ns.addon_app,
            ns.status, na.name, na.title, na.description, ng.group_name, ns.validity_time, na.type, na.icon as app_icon';
		$alias = 'nsu';
		$join = [
			[
				'nc_site ns',
				'nsu.site_id = ns.site_id',
				'INNER'
			],
			[
				'nc_addon na',
				'ns.addon_app = na.name',
				'LEFT'
			],
			[
				'nc_group ng',
				'ng.group_id = nsu.group_id',
				'INNER'
			]
		];
		$list = model('nc_site_user')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
		return success($list);
	}
	
	/**
	 * 用户菜单格式
	 */
	public function userCenterFormat()
	{
		$url = request()->url(true);
		$user_menu = $this->userCenterMenu();
		foreach ($user_menu as $k => $v) {
			foreach ($v as $k_child => $v_child) {
				$data_url = addon_url($v_child['url']);
				$v_child['data_url'] = $data_url;
				if (strpos($url, $data_url) !== false) {
					$v_child['is_select'] = 1;
				} else {
					$v_child['is_select'] = 0;
				}
				$v['child_list'][ $k_child ] = $v_child;
			}
			$user_menu[ $k ] = $v;
		}
		return $user_menu;
	}
	
	/**
	 * 获取用户手机绑定信息
	 * @param unknown $uid
	 */
	public function getUserBindMobileInfo($uid)
	{
		$info = model('nc_user')->getInfo([ 'uid' => $uid ], 'mobile');
		$result = [
			'is_bind_mobile' => 0,
			'mobile' => ''
		];
		if (isset($info['mobile']) && !empty($info['mobile'])) {
			$result = [
				'is_bind_mobile' => 1,
				'mobile' => $info['mobile']
			];
		}
		return $result;
	}
	
	/**
	 * 检测手机号是否存在
	 * @param unknown $mobile
	 */
	public function checkMobileIsExist($mobile, $uid = 0)
	{
		if ($uid) {
			$count = model('nc_user')->getCount([ 'mobile' => $mobile, 'uid' => [ '<>', $uid ] ]);
		} else {
			$count = model('nc_user')->getCount([ 'mobile' => $mobile ]);
		}
		return success($count);
	}
	
	/**
	 * 绑定手机号
	 * @param unknown $mobile
	 */
	public function bindMobile($mobile, $uid)
	{
		$count = model('nc_user')->update([ 'mobile' => $mobile ], [ 'uid' => $uid ]);
		return success($count);
	}
	
	/*******************************************************************用户注册登录*****************************************************/
	
	/**
	 * 用户登录
	 * @param unknown $mobile
	 * @param unknown $password
	 */
	public function login($mobile, $password)
	{
		// 验证参数 预留
		$user_info = model('nc_user')->getInfo([
			'username|mobile' => $mobile
		]);
		
		if (empty($user_info)) {
			return error('', 'USER_NOT_EXIST');
		} else if (data_md5($password) !== $user_info['password']) {
			return error([], 'PASSWORD_ERROR');
		} else if ($user_info['status'] !== 1) {
			return error([], 'USER_IS_LOCKED');
		}
		
		// 记录登录SESSION
		$auth = array(
			'uid' => $user_info['uid'],
			'username' => $user_info['username'],
			'last_login_time' => time()
		);
		//更新登录记录
		$data = [
			'login_time' => time(),
			'login_ip' => request()->ip(),
			'last_login_time' => $user_info['login_time'],
			'last_login_ip' => $user_info['login_ip'],
			'login_num' => $user_info['login_num'] + 1,
		];
		model('nc_user')->update($data, [ 'uid' => $user_info['uid'] ]);
		session('user_auth', $user_info);
		session('user_auth_sign', data_auth_sign($auth));
		return success($user_info);
	}


	/**
	 * 刷新用户信息session
	 * @param $uid
	 */
//	public function refreshUserInfoSession($uid)
//	{
//		$user_info = model('nc_user')->getInfo([
//			'uid' => $uid
//		]);
//		// 记录登录SESSION
//		$auth = array(
//			'uid' => $user_info['uid'],
//			'username' => $user_info['username'],
//			'last_login_time' => time()
//		);
//		session('user_auth', $user_info);
//		session('user_auth_sign', data_auth_sign($auth));
//	}
	
	/**
	 * 用户注册
	 * @param unknown $username
	 * @param unknown $password
	 */
	public function register($param = [])
	{
		$data = array(
			'username' => $param['username'],
			'password' => data_md5($param['password']),
			'mobile' => $param['mobile'],
			'nick_name' => $param['nickname'],
			'real_name' => $param['realname'],
			'is_admin' => 0,
			'status' => 1,
			'app_key' => random_keys(13),
			'app_secret' => random_keys(32),
			'register_time' => time()
		);
		
		$user_info = Db::name('nc_user')->where('username', $param['username'])->whereOr('mobile', $param['mobile'])->find();
		
		if (!empty($user_info)) {
			
			if ($user_info['username'] == $param['username']) {
				return error('', 'USER_EXISTED');
			} elseif ($user_info['mobile'] == $param['mobile']) {
				return error('', 'NS_MOBILE_EXISTED');
			}
			
		} else {
			$result = model('nc_user')->add($data);
			if (!$result) {
				return error('', 'UNKNOW_ERROR');
			}
			$this->login($param['username'], $param['password']);
			hook('userRegisterAfter', [ 'mobile' => $param['mobile'], 'password' => $param['password'] ]);
			
			return success($result);
		}
	}
	
	/**
	 * 获取用户基础登录信息
	 * @return int 0=未登录  >0返回登录人id
	 */
	public function getLoginInfo()
	{
		$user = session('user_auth');
		return success($user);
	}

	/**
	 * 退出登录（清除session）
	 */
	public function logout()
	{
		session('user_auth', null);
		session('user_auth_sign', null);
	}
	
	/*******************************************************************用户注册登录结束**************************************************/
	
	/*******************************************************************添加修改用户开始**************************************************/
	/**
	 * 添加管理员
	 *
	 * @param array $data
	 * @return multitype:string mixed
	 */
	public function addUser($data, $group_id = "")
	{
		
		$user_admin_info = model('nc_user')->getInfo([
			'username|mobile' => $data['username']
		]);
		
		if (!empty($user_admin_info)) {
			return error('', 'NS_USERNAME_EXISTED');
		}
		
		if (!empty($data['mobile'])) {
			$count = model('nc_user')->getCount([ 'mobile' => $data['mobile'] ]);
			if ($count > 0) {
				return error('', 'NS_MOBILE_EXISTED');
			}
		}
		
		$data['password'] = data_md5($data['password']);
		$data['register_time'] = time();
		$site_id = isset($data['site_id']) ? $data['site_id'] : 0;
		if(!empty($site_id))
		{
		    unset($data['site_id']);
		}
		$res = model('nc_user')->add($data);
		if(!empty($site_id))
		{
		    //绑定站点用户
		    $site_user = array(
		        'uid' => $res,
		        'site_id' => $site_id,
		        'group_id' => 0,
		    );
		    model('nc_site_user')->add($site_user);
		}
		if (!empty($group_id)) {
			$site_user = array(
				'uid' => $res,
				'site_id' => 0,
				'group_id' => $group_id,
			);
			model('nc_site_user')->add($site_user);
		}
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 修改用户信息
	 *
	 * @param array $data
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function editUser($data = [], $condition = [], $group_id = "")
	{
		
		$user_admin_info = model('nc_user')->getInfo([
			'username|mobile' => $data['username'], 'uid' => [ 'neq', $condition['uid'] ]
		]);
		if (!empty($user_admin_info)) {
			return error('', 'NS_USERNAME_EXISTED');
		}
		
		if (!empty($data['mobile'])) {
			$count = model('nc_user')->getCount([ 'mobile' => $data['mobile'], 'uid' => [ '<>', $condition['uid'] ] ]);
			if ($count > 0) {
				return error('', 'NS_MOBILE_EXISTED');
			}
		}
		
		if (!empty($data)) {
			$res = model('nc_user')->update($data, $condition);
		}
		if (!empty($group_id)) {
			$user_info = model('nc_site_user')->getInfo([
				'uid' => $condition['uid']
			]);
			if (empty($user_info)) {
				$site_user_data = array(
					'uid' => $condition['uid'],
					'site_id' => 0,
					'group_id' => $group_id,
				);
				$res = model('nc_site_user')->add($site_user_data);
			} else {
				$res = model('nc_site_user')->update([ 'group_id' => $group_id, ], $condition);
			}
		}
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 删除管理员
	 *
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function deleteUser($condition)
	{
		$res = model('nc_user')->delete($condition);
		model('nc_site_user')->delete($condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	/*******************************************************************添加修改用户结束**************************************************/
	
	/***********************************************************站点会员管理************************************************************/
	
	/**
	 * 添加站点会员关联
	 *
	 * @param integer $siteId
	 * @param string $mobile
	 * @param integer $group_id
	 * @param string $password
	 * @return multitype:string mixed
	 */
	public function addSiteUser($data)
	{
		$site_id = $data["site_id"];
		$mobile = $data["mobile"];
		$group_id = $data["group_id"];
		$password = isset($data["password"]) ? $data["password"] : "123456";
		$username = $data["username"];
		$is_admin = isset($data["is_admin"]) ? $data["is_admin"] : 0;
		$nick_name = $data["nick_name"];
		// 启动事务
		Db::startTrans();
		try {
			$user_info = model('nc_user')->getInfo([
				'username|mobile' => $mobile,
			]);
			
			if ($username != '' && empty($user_info)) {
				$user_info = model('nc_user')->getInfo([
					'username' => $username,
				]);
			}
			
			if (empty($user_info)) {
				$data = array(
					'username' => $username ? $username : $mobile,
					'mobile' => $mobile,
					'password' => data_md5($password),
					'is_admin' => $is_admin,
					'status' => 1,
					'register_time' => time(),
					"nick_name" => $nick_name
				);
				$result = model('nc_user')->add($data);
				$uid = $result;
			} else {
				$uid = $user_info['uid'];
			}
			
			if ($is_admin == 1) {
				model('nc_user')->update([ 'is_admin' => 1 ], [ 'username|mobile' => $mobile ]);
			}
			
			$site_user_info = model('nc_site_user')->getInfo([ 'uid' => $uid, 'site_id' => $site_id, 'group_id' => $group_id ]);
			if (empty($site_user_info)) {
				// 同时给站点与会员关联表增加
				$data_site = array(
					'uid' => $uid,
					'site_id' => $site_id,
					'group_id' => $group_id
				);
				$res = model('nc_site_user')->add($data_site);
				Db::commit();
				return success($res);
			} else {
				Db::commit();
				return success('', 'ADMINISTRATOR_EXISTED');
			}
		} catch (\Exception $e) {
			Db::rollback();
			return error('', $e->getMessage());
		}
	}
	
	/**
	 * 绑定站点会员关联
	 * @param integer $siteId
	 * @param string $mobile
	 * @param integer $group_id
	 * @param string $password
	 * @return multitype:string mixed
	 */
	public function bingSiteUser($data)
	{
		$site_id = $data["site_id"];
		$group_id = $data["group_id"];
		$uid = $data["uid"];
		
		// 启动事务
		Db::startTrans();
		try {
			$user_info = model('nc_user')->getInfo([
				'uid' => $uid,
			]);
			if (empty($user_info))
				return error();
			
			$site_user_info = model('nc_site_user')->getInfo([ 'uid' => $uid, 'site_id' => $site_id ]);
			
			//判权限组是否与本站点匹配
			$group_info = model('nc_group')->getInfo([ 'group_id' => $group_id, 'site_id' => $site_id ]);
			if (empty($group_id))
				return error('', "所选权限组与本站点不匹配!");
			
			if (empty($site_user_info)) {
				// 同时给站点与会员关联表增加
				$data_site = array(
					'uid' => $uid,
					'site_id' => $site_id,
					'group_id' => $group_id
				);
				$res = model('nc_site_user')->add($data_site);
			} else {
				// 同时修改站点与会员关联表
				$data_site = array(
					'uid' => $uid,
					'site_id' => $site_id,
					'group_id' => $group_id
				);
				$res = model('nc_site_user')->update($data_site, [ "uid" => $uid, 'site_id' => $site_id ]);
			}
			Db::commit();
			return success($res);
		} catch (\Exception $e) {
			Db::rollback();
			return error('', $e->getMessage());
		}
	}
	
	/**
	 * 修改站点会员关联
	 *
	 * @param array $data
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function editSiteUser($data, $condition, $userdata = [])
	{
		
		$res = model('nc_site_user')->update($data, $condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		if (!empty($userdata)) {
			$user_admin_info = model('nc_user')->getInfo([
				'username|mobile' => $userdata['mobile'], 'uid' => [ 'neq', $condition['uid'] ]
			]);
			if (!empty($user_admin_info)) {
				return error('', 'UNKNOW_ERROR');
			}
			$res = model('nc_user')->update($userdata, [ 'uid' => $condition['uid'] ]);
			if ($res === false) {
				return error('', 'UNKNOW_ERROR');
			}
		}
		
		return success($res);
	}
	
	/**
	 * 获取会员站点关联信息
	 *
	 * @param array $condition
	 */
	public function getSiteUserInfo($condition, $field = '*')
	{
		$info = model('nc_site_user')->getInfo($condition, $field);
		return success($info);
	}
	
	/**
	 * 修改会员状态
	 *
	 * @param array $data
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function modifyUserStatus($data, $condition)
	{
		$res = model('nc_site_user')->update($data, $condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 删除管理员
	 *
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function deleteSiteUser($condition)
	{
		$res = model('nc_site_user')->delete($condition);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 获取站点会员分页列表
	 *
	 * @param integer $site_id
	 * @param string $page
	 * @param string $page_size
	 * @return multitype:string mixed
	 */
	public function getSiteUserPageList($site_id, $page, $page_size)
	{
		$condition = [
			'nsu.site_id' => $site_id
		];
		$field = 'ns.*, ng.group_name, nsu.status, nsu.group_id';
		$order = 'nsu.site_user_id asc';
		$alias = 'nsu';
		$join = [
			[
				'nc_user ns',
				'nsu.uid = ns.uid',
				'INNER'
			],
			[
				'nc_group ng',
				'nsu.group_id = ng.group_id and nsu.site_id = ng.site_id',
				'left'
			]
		];
		$list = model('nc_site_user')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
		return success($list);
	}
	
	/*****************************************************************站点会员结束******************************************************/
	/**
	 * 会员中心菜单
	 */
	public function userCenterMenu()
	{
		
		$menu = array();
		$menu[] = [
			[
				'title' => '个人资料',
				'url' => 'home/personal/usercenter',
			],
			[
				'title' => '账户安全',
				'url' => 'home/personal/security',
			]
		];
		return $menu;
	}
	
	/**站点会员列表
	 * @param int $page
	 * @param int $page_size
	 * @param string $username
	 * @param string $mobile
	 * @param string $status
	 * @return array
	 */
	public function getUserPageListBysite($page = 1, $page_size = PAGE_LIST_ROWS, $username = '', $mobile = '', $status = '')
	{
		$where = '';
		$condition = [];
		$condition['is_admin'] = 0;
		if (!empty($username)) {
			$where .= " and nu.username LIKE '%$username%'";
			$condition['username'] = [ "like", "%$username%" ];
		}
		if (!empty($mobile)) {
			$where .= " and nu.mobile LIKE '%$mobile%'";
			$condition['mobile'] = [ "like", "%$mobile%" ];
		}
		if ($status != "") {
			$where .= " and nu.status = $status";
			$condition['status'] = $status;
		}
		$start = ($page - 1) * $page_size; //计算每次分页的开始位置
		$sql = "SELECT  nu.uid,  nu.headimg,  nu.mobile, nu.status, nu.register_time,
                  (SELECT COUNT(*) FROM nc_user_addon nua WHERE nua.uid = nu.uid) AS addon_num,
                  (SELECT COUNT(*) FROM nc_site ns WHERE ns.uid = nu.uid AND ns.status = 0) AS site_trial_num,
                  (SELECT COUNT(*) FROM nc_site ns WHERE ns.uid = nu.uid AND ns.status = 1) AS site_normal_num,
                  (SELECT COUNT(*) FROM nc_site ns WHERE ns.uid = nu.uid AND ns.status = 2) AS site_overdue_num
                  FROM nc_user AS nu where nu.is_admin=0 " . $where . " limit $start,$page_size";
		
		$data = model('nc_user')->query($sql);
		$count = model('nc_user')->getCount($condition);
		
		$list = [
			'list' => $data,
			'count' => $count
		];
		return success($list);
	}
	
	/**
	 * 获取会员模块列表
	 *
	 * @param integer $uid
	 * @return multitype:string mixed
	 */
	public function getUserAddonModuleList($uid)
	{
		$alias = 'nua';
		$join = [
			[ 'nc_addon na', 'nua.addon_name = na.name', 'LEFT' ]
		];
		$field = ' nua.*, na.autoload';
		$list = model('nc_user_addon')->getList([ 'uid' => $uid, 'na.type' => 'ADDON_MODULE' ], $field, '', $alias, $join);
		return success($list);
	}
	
	/**
	 * 获取会员插件列表
	 *
	 * @param integer $uid
	 * @return multitype:string mixed
	 */
	public function getUserAddonList($uid)
	{
		$condition = [
			'nua.uid' => $uid
		];
		
		$field = 'nua.uid,nua.addon_name,nua.status,na.name,na.icon,na.title,na.description,na.config,na.content,na.type,na.try_time';
		$order = 'nua.create_time desc';
		$alias = 'nua';
		$join = [
			[
				'nc_addon na',
				'nua.addon_name = na.name',
				'INNER'
			]
		];
		$list = model('nc_user_addon')->getList($condition, $field, $order, $alias, $join);
		return success($list);
	}
	
	/**
	 * 获取会员插件分页列表
	 *
	 * @param integer $uid
	 * @return multitype:string mixed
	 */
	public function getUserAddonPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		
		$field = 'nua.uid,nua.addon_name,nua.status,na.name,na.icon,na.title,na.description,na.config,na.content,na.type';
		$order = 'nua.create_time desc';
		$alias = 'nua';
		$join = [
			[
				'nc_addon na',
				'nua.addon_name = na.name',
				'INNER'
			]
		];
		$list = model('nc_user_addon')->pageList($condition, $field, $order, $page, $page_size, $alias = 'a', $join = [], $group = '');
		return success($list);
	}
	
	/**
	 * 获取用户操作日志
	 * @param unknown $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getUserLogPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$field = 'uid, username, site_id, url, title, ip, create_time, module';
		$order = 'create_time desc';
		$alias = 'a';
		$join = [];
		$list = model('nc_user_log')->pageList($condition, $field, $order, $page, $page_size, $alias, $join, $group = '');
		return success($list);
	}
	
	/**
	 * 添加用户日志
	 * @param unknown $data
	 * @return unknown
	 */
	public function addUserLog($data)
	{
		$res = model('nc_user_log')->add($data);
		return $res;
	}
	
	/**
	 * 设置用户状态
	 * @param unknown $status
	 * @param unknown $uid
	 */
	public function setUserStatus($uid, $status)
	{
		$res = model('nc_user')->update([ 'status' => $status ], [ 'uid' => $uid ]);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
}