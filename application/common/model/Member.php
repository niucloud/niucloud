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

/**
 * 会员管理
 *
 * @author Administrator
 *
 */
class Member
{
	/********************************************************************* 会员 start******************************************************************************************/
	/**
	 * 注销当前用户
	 */
	public function logout()
	{
		session('member_info', null);
		return success(1);
	}
	
	/**
	 * 添加会员
	 *
	 * @param array $data
	 */
	public function addMember($data)
	{
		if ($data['username']) {
			$count = model('nc_member')->getCount([
				'username' => $data['username'],
				'site_id' => $data['site_id']
			]);
			if ($count > 0) {
				return error('', 'NS_USERNAME_EXISTED');
			}
		}
		
		if ($data['mobile']) {
			$count = model('nc_member')->getCount([
				'mobile' => $data['mobile'],
				'site_id' => $data['site_id']
			]);
			if ($count > 0) {
				return error('', 'NS_MOBILE_EXISTED');
			}
		}
		
		if ($data['email']) {
			$count = model('nc_member')->getCount([
				'email' => $data['email'],
				'site_id' => $data['site_id']
			]);
			if ($count > 0) {
				return error('', 'NS_EMAIL_EXISTED');
			}
		}
		$res = model('nc_member')->add($data);
		if ($res === false) {
			return error('', 'RESULT_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 修改会员
	 *
	 * @param array $data
	 * @param array $condition
	 */
	public function editMember($data, $condition)
	{
		if ($data['username']) {
			$where = [
				'username' => $data['username'],
				'site_id' => $condition['site_id'],
				'member_id' => [
					'<>',
					$condition['member_id']
				]
			];
			$count = model('nc_member')->getCount($where);
			if ($count > 0) {
				return error('', 'NS_USERNAME_EXISTED');
			}
		}
		
		if ($data['mobile']) {
			$where = [
				'mobile' => $data['mobile'],
				'site_id' => $condition['site_id'],
				'member_id' => [
					'<>',
					$condition['member_id']
				]
			];
			$count = model('nc_member')->getCount($where);
			if ($count > 0) {
				return error('', 'NS_MOBILE_EXISTED');
			}
		}
		
		if ($data['email']) {
			$where = [
				'email' => $data['email'],
				'site_id' => $condition['site_id'],
				'member_id' => [
					'<>',
					$condition['member_id']
				]
			];
			$count = model('nc_member')->getCount($where);
			if ($count > 0) {
				return error('', 'NS_EMAIL_EXISTED');
			}
		}
		$res = model('nc_member')->update($data, $condition);
		if ($res === false) {
			return error('', 'RESULT_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 修改会员状态
	 *
	 * @param int $status
	 * @param array $condition
	 */
	public function modifyMemberStatus($status, $condition)
	{
		$res = model('nc_member')->update([
			'status' => $status
		], $condition);
		if ($res === false) {
			return error('', 'RESULT_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 删除会员
	 *
	 * @param array $condition
	 */
	public function delMember($condition)
	{
		$res = model('nc_member')->delete($condition);
		if ($res === false) {
			return error('', 'RESULT_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 获取会员信息
	 *
	 * @param array $condition
	 * @param string $field
	 * @return unknown
	 */
	public function getMemberInfo($condition = [], $field = '*')
	{
		$member_info = model('nc_member')->getInfo($condition, $field);
		if (!empty($member_info)) {
			if (!empty($member_info['member_label'])) {
				$member_info['member_label_list'] = model('nc_member_label')->getList([ 'label_id' => [ 'in', $member_info['member_label'] ] ]);
			}
			if (!empty($member_info['member_group'])) {
				$group_info = model("nc_member_group")->getInfo([ "group_id" => $member_info['member_group'] ], 'group_name');
				$member_info['member_group_name'] = $group_info["group_name"];
			} else {
				$member_info['member_group_name'] = '';
			}
		}
		return success($member_info);
	}
	
	/**
	 * 重置密码
	 *
	 * @param string $password
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function modifyMemberPassword($password = '123456', $condition)
	{
		$res = model('nc_member')->update([
			'password' => data_md5($password)
		], $condition);
		if ($res === false) {
			return error('', 'RESULT_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 获取会员分页列表
	 *
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getMemberPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$field = 'nm.member_id,nm.site_id,nm.member_level,nm.username,nm.mobile,nm.email,nm.nick_name,nm.real_name,nm.headimg,nm.register_time,
                nm.status,nm.qq_openid,nm.wx_openid,nm.wx_weapp_openid,nm.credit1,nm.credit2,nm.credit3,nm.credit4,nm.credit5,nm.credit6,
                nm.credit7,nm.member_label,nmg.group_name,GROUP_CONCAT(nmla.label_name) as label_name';
		$order = 'nm.register_time desc';
		$alias = 'nm';
		$join = [
			[
				'nc_member_group nmg',
				'nmg.group_id = nm.member_group',
				'LEFT'
			],
			[
				'nc_member_label nmla',
				'CONCAT(",",nm.member_label, ",") like CONCAT("%,",nmla.label_id, ",%")',
				'LEFT'
			]
		];
		$list = model('nc_member')->pageList($condition, $field, $order, $page, $page_size, $alias, $join, $group = 'nm.member_id');
		return success($list);
	}
	
	/**
	 * 获取会员列表
	 *
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getMemberList($where = [], $field = true, $order = '', $alias = 'a', $join = [], $group = '', $limit = null)
	{
		$res = model('nc_member')->getList($where, $field, $order, $alias, $join, $group, $limit);
		return success($res);
	}
	
	/**
	 * 获取会员数量
	 * @param array $condition
	 * @return array
	 */
	public function getMemberCount($condition = [])
	{
		$num = model('nc_member')->getCount($condition);
		return success($num);
	}
	
	/**
	 * 检测账号是否存在
	 * @param string $type
	 * @param unknown $account
	 */
	public function checkAccountIsExist($type, $account, $site_id, $member_id = 0)
	{
		if ($member_id) {
			$count = model('nc_member')->getCount([ $type => $account, 'site_id' => $site_id, 'member_id' => [ '<>', $member_id ] ]);
		} else {
			$count = model('nc_member')->getCount([ $type => $account, 'site_id' => $site_id ]);
		}
		return success($count);
	}
	
	/**
	 * 退出当前用户
	 *
	 * @return void
	 */
	public function loginOut()
	{
		setcookie("access_token_" . SITE_ID, '');
		cache("member_info_" . SITE_ID, null);
	}
	
	/**
	 * 关注人数总数
	 *
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function getWechatFansCount($condition)
	{
		$count = model('nc_wechat_fans')->getCount($condition);
		return success($count);
	}
	
	/********************************************************************* 会员 end******************************************************************************************/
	/********************************************************************* 会员地址 start******************************************************************************************/
	/**
	 * 获取收获地址列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 * @return multitype:string mixed
	 */
	public function getMemberAddressList($condition = [], $field = '*', $order = 'is_default desc', $limit = null)
	{
		$list = model('nc_member_address')->getList($condition, $field, $order, '', '', '', $limit);
		return success($list);
		
	}
	
	/**
	 * 获取详情收获地址
	 * @param array $condition
	 */
	public function getMemberAddressInfo($condition)
	{
		$res = model('nc_member_address')->getInfo($condition);
		return success($res);
	}
	
	/**
	 * 获取地区列表
	 * @param unknown $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 * @return multitype:string mixed
	 */
	public function getAreaList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$address = new Address();
		$area_list = $address->getAreaList($condition, $field, $order, $limit);
		return $area_list;
	}
	
	/**
	 * 添加收货地址
	 * @param array $params
	 */
	public function addMemberAddress($params)
	{
		$params['create_time'] = time();
		if ($params['is_default'] == 1) {
			model('nc_member_address')->update([ 'is_default' => 0 ], [ 'member_id' => $params['member_id'] ]);
		}
		$res = model('nc_member_address')->add($params);
		$count = model('nc_member_address')->getCount([ 'member_id' => $params['member_id'] ]);
		if ($count == 1) model('nc_member_address')->update([ 'is_default' => 1 ], [ 'member_id' => $params['member_id'], 'id' => $res ]);
		return success($res);
	}
	
	/**
	 * 修改收货地址
	 * @param array $params
	 */
	public function editMemberAddress($params)
	{
		$params['update_time'] = time();
		if ($params['is_default'] == 1) {
			model('nc_member_address')->update([ 'is_default' => 0 ], [ 'member_id' => $params['member_id'] ]);
		}
		$res = model('nc_member_address')->update($params, [ 'id' => $params['id'] ]);
		return success($res);
	}
	
	/**
	 * 删除收获地址
	 * @param array $condition
	 */
	public function deleteMemberAddress($condition)
	{
		$res = model('nc_member_address')->delete($condition);
		if ($res === false) {
			return error('', 'RESULT_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 设置默认收货地址
	 */
	public function setMemberDefaultAddress($id, $member_id)
	{
		model('nc_member_address')->startTrans();
		try {
			model('nc_member_address')->update([ 'is_default' => 0 ], [ 'member_id' => $member_id ]);
			$res = model('nc_member_address')->update([ 'is_default' => 1 ], [ 'member_id' => $member_id, 'id' => $id ]);
			model('nc_member_address')->commit();
			return success($res);
		} catch (\Exception $e) {
			model('nc_member_address')->rollback();
			return error('', $e->getMessage());
		}
	}
	/********************************************************************* 会员地址 end******************************************************************************************/
	/**************************************************************************第三方登录 start*************************************************************************************/
	/**
	 * 第三方账号登录 完善信息
	 * @param unknown $user_name
	 * @param unknown $password
	 * @param unknown $nick_name
	 * @param unknown $head_img
	 * @param unknown $tag
	 * @param unknown $openid
	 * @param unknown $site_id
	 */
	public function perfectInfo($user_name, $password, $nick_name, $head_img, $tag, $openid, $site_id)
	{
		// 是否已被注册
		$condition = array(
			'username' => $user_name,
			'site_id' => request()->siteid()
		);
		$member_info = model('nc_member')->getInfo($condition);
		if (!empty($member_info)) {
			return error('', '该用户名已存在');
		}
		
		$data = array(
			'site_id' => $site_id,
			'username' => $user_name,
			'nick_name' => $nick_name,
			'password' => data_md5($password),
			'headimg' => $head_img,
			'register_time' => time(),
			$tag => $openid
		);
		$res = model('nc_member')->add($data);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 第三方账号登录 绑定账号
	 * @param unknown $user_name
	 * @param unknown $password
	 * @param unknown $nick_name
	 * @param unknown $head_img
	 * @param unknown $tag
	 * @param unknown $openid
	 */
	public function bindAccount($user_name, $password, $nick_name, $head_img, $tag, $openid, $site_id)
	{
		$member_info = model('nc_member')->getInfo([ 'username|mobile|email' => $user_name, 'site_id' => $site_id ], 'password');
		
		if (empty($member_info)) return error('', '账号不存在');
		if ($member_info['password'] != data_md5($password)) return error('', '密码输入错误');
		
		$data = array(
			'nick_name' => $nick_name,
			$tag => $openid,
			'headimg' => $head_img
		);
		
		$res = model('nc_member')->update($data, [ 'username|mobile|email' => $user_name, 'site_id' => $site_id ]);
		if ($res === false) {
			return error('', 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	
	/**
	 * 绑定第三方账号
	 * @param unknown $site_id
	 * @param unknown $member_id
	 * @param unknown $tag
	 * @param unknown $open_id
	 */
	public function bindThirdAccount($site_id, $member_id, $tag, $open_id)
	{
		$is_bound = model('nc_member')->getCount([ 'site_id' => $site_id, $tag => $open_id, 'member_id' => [ '<>', $member_id ] ]);
		if (!$is_bound) {
			$res = model('nc_member')->update([ $tag => $open_id ], [ 'site_id' => $site_id, 'member_id' => $member_id ]);
			return success($res);
		} else {
			return error('', '已绑定过本站点其他账号');
		}
	}
	/**************************************************************************第三方登录 end*************************************************************************************/
	
	
	/**************************************************************************会员账户 start*************************************************************************************/
	/**
	 * 会员账户配置
	 * @param $site_id
	 * @return \multitype
	 */
	public function getSiteAccountConfig($site_id)
	{
		$get_config = model('nc_site_config')->getInfo([
			'name' => 'NS_MEMBER_ACCOUNT_CONFIG',
			'site_id' => $site_id
		]);
		
		if (empty($get_config) || $get_config == null) {
			// 返回默认值
			$get_config['value'] = array();
		} else {
			$get_config['value'] = json_decode($get_config['value'], true);
		}
		return success($get_config);
	}
	
	/**
	 * 获取账户类型详情
	 * @param $key
	 */
	public function getSiteAccountInfo($data)
	{
		$account_info = [];
		if (empty($data["key"]))
			return [];
		
		$account_data = $this->getSiteAccountConfig($data["site_id"]);
		if (empty($account_data["data"]["value"]))
			return [];
		
		foreach ($account_data["data"]["value"] as $k => $v) {
			if ($data["key"] == $v["key"]) {
				$account_info = $v;
				continue;
			}
		}
		return $account_info;
	}
	
	/**
	 * 获取当前账户设置（兑换额度|账户名称|账户开关）
	 *
	 * @param int $site_id
	 * @return multitype:string mixed
	 */
	public function getMemberAccountConfig($site_id)
	{
		$get_config = model('nc_site_config')->getInfo([
			'name' => 'NS_MEMBER_ACCOUNT_CONFIG',
			'site_id' => $site_id
		]);
		
		if (empty($get_config) || $get_config == null) {
			// 返回默认值
			$get_config['value'] = array();
		} else {
			$get_config['value'] = json_decode($get_config['value'], true);
		}
		return success($get_config);
	}
	
	/**
	 * 获取会员账户流水分页列表
	 * 创建时间：2018年9月27日17:33:27
	 * @param array $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @param string $field
	 * @return array
	 */
	public function getMemberAccountPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		
		$field = 'nmal.*,nm.nick_name';
		$alias = 'nmal';
		$join = [
			[
				'nc_member nm',
				'nmal.member_id = nm.member_id',
				'LEFT'
			]
		];
		$res = model("nc_member_account_list")->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
		
		return success($res);
	}
	
	/**
	 * 账户的系统或营销策略
	 * @param $site_id
	 */
	public function getBasicTacticsConfig($site_id)
	{
		$site_model = new Site();
		$config = $site_model->getSiteConfigInfo([ 'name' => 'NS_BASIC_TACTICS_CONFIG', 'site_id' => $site_id ]);
		$config["data"]["value_info"] = $this->getSiteAccountInfo([ "site_id" => $site_id, "key" => $config["data"]["value"] ]);
		return $config;
	}
	
	/**
	 * 设置账户营销策略
	 * @param $data
	 * @return \multitype
	 */
	public function setBasicTacticsConfig($data)
	{
		$data["name"] = 'NS_BASIC_TACTICS_CONFIG';
		$site_model = new Site();
		$res = $site_model->setSiteConfig($data);
		return $res;
	}
	
	/**
	 * 账户的交易和支付策略
	 * @param $site_id
	 */
	public function getPayTacticsConfig($site_id)
	{
		$site_model = new Site();
		$config = $site_model->getSiteConfigInfo([ 'name' => 'NS_PAY_TACTICS_CONFIG', 'site_id' => $site_id ]);
		$config["data"]["value_info"] = $this->getSiteAccountInfo([ "site_id" => $site_id, "key" => $config["data"]["value"] ]);
		return $config;
	}
	
	/**
	 * 设置账户交易和支付策略
	 * @param $data
	 * @return \multitype
	 */
	public function setPayTacticsConfig($data)
	{
		$data["name"] = 'NS_PAY_TACTICS_CONFIG';
		$site_model = new Site();
		$res = $site_model->setSiteConfig($data);
		return $res;
	}
	
	/**
	 * 添加基础或营销业务的账户流水
	 * @param $param
	 */
	public function addBasicAccount($param)
	{
		$config_info = $this->getBasicTacticsConfig($param["site_id"]);
		//检测账户是否配置
		if (empty($config_info["data"]["value"]))
			return error();
		
		$key = $config_info["data"]["value"];
		//用户添加账户流水
		$res = $this->addMemberAccount($param["site_id"], $param["member_id"], $key, $param["money"], $param['addon'], $param['relate_tag'], $param["remark"]);
		return $res;
		
	}
	
	/**
	 * 添加交易或支付业务的账户流水
	 * @param $param
	 */
	public function addPayAccount($param)
	{
		$config_info = $this->getPayTacticsConfig($param["site_id"]);
		//检测账户是否配置
		if (empty($config_info["data"]["value"]))
			return error();
		
		$key = $config_info["data"]["value"];
		//用户添加账户流水
		$res = $this->addMemberAccount($param["site_id"], $param["member_id"], $key, $param["money"], $param['addon'], $param['relate_tag'], $param["remark"]);
		return $res;
	}
	
	/**
	 * 添加会员账户数据
	 *
	 * @param int $site_id
	 * @param int $member_id
	 * @param int $account_type
	 * @param float $account_data
	 * @param string $relate_url
	 * @param string $remark
	 */
	public function addMemberAccount($site_id, $member_id, $account_type, $account_data, $addon, $relate_tag, $remark)
	{
		$data = array(
			'site_id' => $site_id,
			'member_id' => $member_id,
			'account_type' => $account_type,
			'account_data' => $account_data,
			'addon' => $addon,
			'relate_tag' => $relate_tag,
			'create_time' => time(),
			'remark' => $remark
		);
		
		$member_account = model('nc_member')->getInfo([
			'member_id' => $member_id
		], $account_type);
		$account_new_data = (float) $member_account[ $account_type ] + (float) $account_data;
		if ((float) $account_new_data < 0) {
			return error('', 'RESULT_ERROR');
		}
		
		$res = model('nc_member_account_list')->add($data);
		
		if ($res === false) {
			return error('', 'RESULT_ERROR');
		}
		
		$res = model('nc_member')->update([
			$account_type => $account_new_data
		], [
			'member_id' => $member_id
		]);
		
		
		//如果成长史发生变化会尝试会员组页进行变化
		if ($account_type == "credit3") {
			$this->tryUpMemberGroup([ "site_id" => $site_id, "member_id" => $member_id ]);
		}
		if ($res === false) {
			return error('', 'RESULT_ERROR');
		}
		return success($res);
	}
	
	/**************************************************************************会员账户 end*************************************************************************************/
	/**************************************************************************会员分组 begin*************************************************************************************/
	
	/**
	 * 添加会员分组
	 *
	 * @param array $data
	 */
	public function addMemberGroup($data)
	{
		//判断当前成长条件是否存在
		$info = $this->getMemberGroupInfo([ "site_id" => $data["site_id"], "credit" => $data["credit"] ]);
		if (!empty($info["data"]))
			return error('', "GROUP_EXIST_ERROR");
		
		$res = model('nc_member_group')->add($data);
		if ($res === false) {
			return error('', 'RESULT_ERROR');
		}
		return success($res);
	}
	
	
	/**
	 * 修改会员分组
	 *
	 * @param array $data
	 * @param array $condition
	 */
	public function editMemberGroup($data, $condition)
	{
		//判断当前成长条件是否存在
		$info = $this->getMemberGroupInfo([ "site_id" => $data["site_id"], "credit" => $data["credit"] ]);
		if (!empty($info["data"]) && $condition["group_id"] != $info["data"]["group_id"])
			return error('', "GROUP_EXIST_ERROR");
		
		$res = model('nc_member_group')->update($data, $condition);
		if ($res === false) {
			return error('', 'RESULT_ERROR');
		}
		return success($res);
	}
	
	
	/**
	 * 获取会员分组信息
	 *
	 * @param array $condition
	 * @param string $field
	 */
	public function getMemberGroupInfo($condition = [], $field = '*')
	{
		$info = model('nc_member_group')->getInfo($condition, $field);
		return success($info);
	}
	
	/**
	 * 获取会员分组列表
	 *
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getMemberGroupList($condition = [], $field = '*', $order = 'credit asc', $limit = null)
	{
		$list = model('nc_member_group')->getList($condition, $field, $order, '', '', '', $limit);
		return success($list);
	}
	
	/**
	 * 获取会员分组分页列表
	 *
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getMemberGroupPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'credit asc', $field = '*')
	{
		$res = model('nc_member_group')->pageList($condition, $field, $order, $page, $page_size);
		return success($res);
	}
	
	/**
	 * 删除会员分组
	 *
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function deleteMemberGroup($condition)
	{
		$res = model('nc_member_group')->delete($condition);
		if ($res === false) {
			return error('', 'RESULT_ERROR');
		}
		return success($res);
	}
	
	
	/**
	 * 会员分组变更设置
	 * @param $data
	 */
	public function setMemberGroupConfig($data)
	{
		$data["name"] = 'MEMBER_GROUP_CONFIG';
		$site_model = new Site();
		$res = $site_model->setSiteConfig($data);
		return $res;
	}
	
	/**
	 * 查询数据
	 * @param unknown $where
	 * @param unknown $field
	 * @param unknown $value
	 */
	public function getMemberGroupConfig($site_id)
	{
		$site_model = new Site();
		$config = $site_model->getSiteConfigInfo([ 'name' => 'MEMBER_GROUP_CONFIG', 'site_id' => $site_id ]);
		$value = [];
		if (!empty($config["data"]["value"])) {
			$value = json_decode($config["data"]["value"], true);
		}
		$config["data"]["value"] = $value;
		return $config;
	}
	
	
	/**
	 * 修改会员分组(仅限后台直接修改)
	 * @param $group_id
	 * @param $condition
	 */
	public function modifyMemberGroup($group_id, $condition)
	{
		model("nc_member")->startTrans();
		try {
			$member_info = model("nc_member")->getInfo($condition, "member_group, contribution_value, credit3");//贡献值和成长值
			if (empty($member_info)) {
				model("nc_member")->rollback();
				return error();
			}
			
			$data = array(
				"member_group" => $group_id
			);
			
			$group_info_result = $this->getMemberGroupInfo([ "group_id" => $group_id ]);
			$group_info = $group_info_result["data"];
			$credit = 0;
			if (!empty($group_info)) {
				$credit = $group_info["credit"];
			}
			//如果后台直接修改会员分组,并且成长值加贡献值不足的话,通过补足贡献值来实现
			$difference_value = $credit - $member_info["credit3"];
			//如果差值小于0,将贡献值设为0
			if ($difference_value < 0) {
				$difference_value = 0;
			}
			$data["contribution_value"] = $difference_value;
			$res = model("nc_member")->update($data, $condition);
			
			model("nc_member")->commit();
			return success($res);
		} catch (\Exception $e) {
			model("nc_member")->rollback();
			return error('', $e->getMessage());
		}
	}
	
	
	/**
	 * 尝试执行会员分组变更(条件达到就变更,达不到就不变更)
	 * @param $group_id
	 * @param $member_id
	 */
	public function tryUpMemberGroup($data)
	{
		$site_id = $data["site_id"];
		$member_id = $data["member_id"];
		
		$group_config_result = $this->getMemberGroupConfig($site_id);
		$group_config = $group_config_result["data"];
		$type = 0;
		if (!empty($group_config["value"])) {
			$type = $group_config["value"]["type"];//type 1:不自动变更。 2:根据积分多少自动升降 3:根据积分多少只升不降
		}
		//不自动变更不参与自动升级分组
		if ($type == 1)
			return success();
		
		$condition = array(
			"member_id" => $member_id,
			"site_id" => $site_id,
		);
		$member_info = model("nc_member")->getInfo($condition, "member_group, contribution_value, credit3");//贡献值和成长值
		//验证会员的合法性
		if (empty($member_info)) {
			return error();
		}
		$member_value = $member_info["contribution_value"] + $member_info["credit3"];//会员的成长值+贡献值
		$group_condition = array(
			"credit" => [ "elt", $member_value ],
			"site_id" => $site_id
		);
		$group_info = model('nc_member_group')->pageList($group_condition, "*", "credit desc", 1, 1);//查询当前会员现有条件最高的会员组
		
		if (!empty($group_info["list"])) {
			$max_group_info = $group_info["list"][0];
			$before_group_info = model('nc_member_group')->getInfo([ "site_id" => $site_id, "group_id" => $member_info["member_group"] ]);
			if ($before_group_info["credit"] <= $max_group_info["credit"]) {
				//当前存在可升级会员组,并且笔现在的组的条件要高
				//如果存在可升级会员,判断其与当前等级是否相同,不同的话才会触发升级会员组
				if ($max_group_info["group_id"] != $member_info["member_group"]) {
					$res = model("nc_member")->update([ "member_group" => $max_group_info["group_id"] ], $condition);
					if ($res !== false)
						return success($res);
				}
			} else {
				if ($type == 2) {
					$res = model("nc_member")->update([ "member_group" => $max_group_info["group_id"] ], $condition);
					if ($res !== false)
						return success($res);
				}
			}
			
		}
		return error();
	}
	
	
	/**************************************************************************会员分组 end*************************************************************************************/
	
	/**
	 * 添加会员等级
	 *
	 * @param array $data
	 */
	public function addMemberLevel($data)
	{
		$count = model('nc_member_level')->getCount([
			'level_name' => $data['level_name'],
			'site_id' => $data['site_id']
		]);
		if ($count > 0) {
			return error('', 'NS_LEVEL_NAME_EXISTED');
		}
		$res = model('nc_member_level')->add($data);
		if ($res === false) {
			return error('', 'RESULT_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 添加初始化会员等级
	 * @param int $site_id
	 */
	public function addDefaultMemberLevel($site_id)
	{
		$data = [
			'level_name' => '默认等级',
			'site_id' => $site_id,
			'create_time' => time(),
			'sort' => 1
		];
		$res = model('nc_member_level')->add($data);
		return success($res);
	}
	
	/**
	 * 修改会员等级
	 *
	 * @param array $data
	 * @param array $condition
	 */
	public function editMemberLevel($data, $condition)
	{
		$where = [
			'level_name' => $data['level_name'],
			'site_id' => $condition['site_id'],
			'level_id' => [
				'<>',
				$condition['level_id']
			]
		];
		$count = model('nc_member_level')->getCount($where);
		if ($count > 0) {
			return error('', 'NS_LEVEL_NAME_EXISTED');
		}
		$res = model('nc_member_level')->update($data, $condition);
		if ($res === false) {
			return error('', 'RESULT_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 获取会员等级信息
	 *
	 * @param array $condition
	 * @param string $field
	 */
	public function getMemberLevelInfo($condition = [], $field = '*')
	{
		$info = model('nc_member_level')->getInfo($condition, $field);
		return success($info);
	}
	
	/**
	 * 获取会员等级列表
	 *
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getMemberLevelList($condition = [], $field = '*', $order = 'sort asc, level_id asc', $limit = null)
	{
		$list = model('nc_member_level')->getList($condition, $field, $order, '', '', '', $limit);
		return success($list);
	}
	
	/**
	 * 获取会员等级分页列表
	 *
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getMemberLevelPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'sort asc, level_id asc', $field = '*')
	{
		$res = model('nc_member_level')->pageList($condition, $field, $order, $page, $page_size);
		return success($res);
	}
	
	/**
	 * 删除会员等级
	 * @param array $condition
	 */
	public function deleteMemberLevel($condition)
	{
		$res = model('nc_member_level')->delete($condition);
		if ($res === false) {
			return error('', 'RESULT_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 添加会员标签
	 *
	 * @param array $data
	 */
	public function addMemberLabel($data)
	{
		$count = model('nc_member_label')->getCount([
			'label_name' => $data['label_name'],
			'site_id' => $data['site_id']
		]);
		if ($count > 0) {
			return error('', 'NS_LABEL_NAME_EXISTED');
		}
		$res = model('nc_member_label')->add($data);
		if ($res === false) {
			return error('', 'RESULT_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 获取会员标签信息
	 *
	 * @param array $condition
	 * @param string $field
	 */
	public function getMemberLabelInfo($condition = [], $field = '*')
	{
		$info = model('nc_member_label')->getInfo($condition, $field);
		return success($info);
	}
	
	/**
	 * 获取会员标签列表
	 *
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getMemberLabelList($condition = [], $field = '*', $order = 'sort asc, label_id asc', $limit = null)
	{
		$list = model('nc_member_label')->getList($condition, $field, $order, '', '', '', $limit);
		return success($list);
	}
	
	/**
	 * 获取会员标签分页列表
	 *
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 * @return multitype:string mixed
	 */
	public function getMemberLabelPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'sort asc, label_id asc', $field = '*')
	{
		$res = model('nc_member_label')->pageList($condition, $field, $order, $page, $page_size);
		return success($res);
	}
	
	/**
	 * 修改会员标签
	 *
	 * @param array $data
	 * @param array $condition
	 */
	public function editMemberLabel($data, $condition)
	{
		$where = [
			'label_name' => $data['label_name'],
			'site_id' => $condition['site_id'],
			'label_id' => [
				'<>',
				$condition['label_id']
			]
		];
		$count = model('nc_member_label')->getCount($where);
		if ($count > 0) {
			return error('', 'NS_LABEL_NAME_EXISTED');
		}
		$res = model('nc_member_label')->update($data, $condition);
		if ($res === false) {
			return error('', 'RESULT_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 删除会员标签
	 *
	 * @param array $condition
	 */
	public function deleteMemberLabel($condition)
	{
		$res = model('nc_member_label')->delete($condition);
		if ($res === false) {
			return error('', 'RESULT_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 账户设置
	 *
	 * @param int $site_id
	 * @param array $data_array
	 */
	public function setMemberAccountConfig($site_id, $data_array)
	{
		$get_config = model('nc_site_config')->getInfo([
			'name' => 'NS_MEMBER_ACCOUNT_CONFIG',
			'site_id' => $site_id
		]);
		if (empty($get_config)) {
			// 查询该数据是否存在
			$data = array(
				'site_id' => $site_id,
				'name' => 'NS_MEMBER_ACCOUNT_CONFIG',
				'type' => 1,
				'title' => '会员账户配置',
				'status' => 1,
				'value' => json_encode($data_array),
				'remark' => '会员账户配置',
				'create_time' => time()
			);
			$res = model('nc_site_config')->add($data);
		} else {
			
			$data = array(
				'update_time' => time(),
				'status' => 1,
				'value' => json_encode($data_array)
			);
			$res = model('nc_site_config')->update($data, [
				'name' => 'NS_MEMBER_ACCOUNT_CONFIG',
				'site_id' => $site_id
			]);
		}
		return $res === false ? error('', 'UNKNOW_ERROR') : success($res);
	}
	
	/**
	 * 删除当前账户设置
	 *
	 * @param int $site_id
	 */
	public function deleteMemberAccountConfig($site_id)
	{
		$res = model('nc_site_config')->delete([
			'name' => 'NS_MEMBER_ACCOUNT_CONFIG',
			'site_id' => $site_id
		]);
		if ($res === false) {
			return error('', 'RESULT_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 传入账户以及数量返回兑换金额
	 *
	 * @param array $member_account_info [['account_type', 'num']]
	 */
	public function getMemberAccountMoney($data)
	{
		$member_info = model('nc_member')->getInfo([
			'member_id' => $data['member_id']
		]);
		$get_config = model('nc_site_config')->getInfo([
			'name' => 'NS_MEMBER_ACCOUNT_CONFIG',
			'site_id' => $member_info['site_id']
		]);
		
		if (empty($get_config) || $get_config == null) {
			return error();
		} else {
			$config_value = json_decode($get_config['value'], true);
			foreach ($config_value as $k_value => $v_value) {
				$config_value[ $v_value['key'] ] = $v_value;
			}
			$new_array = [];
			foreach ($data['account_info'] as $k => $v) {
				$v['money'] = $config_value[ $v['account_type'] ]['rate'] * $v['num'];
				$new_array[] = $v;
			}
			return success($new_array);
		}
	}
	
	public function deleteSite($site_id)
	{
		model("nc_member")->delete([ 'site_id' => $site_id ]);
		model("nc_member_account_list")->delete([ 'site_id' => $site_id ]);
		model("nc_member_group")->delete([ 'site_id' => $site_id ]);
		model("nc_member_label")->delete([ 'site_id' => $site_id ]);
		model("nc_member_level")->delete([ 'site_id' => $site_id ]);
		model("nc_member_recharge")->delete([ 'site_id' => $site_id ]);
		model("nc_member_address")->delete([ 'site_id' => $site_id ]);
		return success();
	}
}