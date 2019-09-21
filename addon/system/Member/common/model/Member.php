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
namespace addon\system\Member\common\model;

use app\common\model\Member as BaseMember;
use think\Log;

/**
 * 会员插件扩展
 *
 * @author Administrator
 *
 */
class Member extends BaseMember
{
	
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
		return success($member_info);
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
	 * 添加会员
	 *
	 * @param array $data
	 * @return multitype:string mixed
	 */
	public function addMember($data)
	{
		if ($data['username']) {
			$count = model('nc_member')->getCount([
				'username' => $data['username'],
				'site_id' => $data['site_id']
			]);
			Log::write($count);
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
	 * @return multitype:string mixed
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
	 * @param unknown $site_id
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
	 * @return multitype:string mixed
	 */
	public function getMemberLevelPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'sort asc, level_id asc', $field = '*')
	{
		$res = model('nc_member_level')->pageList($condition, $field, $order, $page, $page_size);
		return success($res);
	}
	
	/**
	 * 删除会员等级
	 *
	 * @param array $condition
	 * @return multitype:string mixed
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
	 * 删除当前账户设置
	 *
	 * @param int $site_id
	 * @return multitype:string mixed
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
	
	/***************************************会员收货地址开始************************ */
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
	 * @return multitype:string mixed
	 */
	public function getMemberAddressInfo($condition)
	{
		$res = model('nc_member_address')->getInfo($condition);
		return success($res);
	}
	
	/**
	 * 删除收获地址
	 * @param array $condition
	 * @return multitype:string mixed
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
	
	/***************************************会员收货地址结束************************ */
	
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
	 * 用户条数
	 * @param array $condition
	 */
	public function getMemberCount($condition = [])
	{
		$count = model("nc_member")->getCount($condition);
		return success($count);
		
	}
	public function deleteSite($site_id)
	{
	    model("nc_member")->delete(['site_id' => $site_id]);
	    model("nc_member_account_list")->delete(['site_id' => $site_id]);
	    model("nc_member_group")->delete(['site_id' => $site_id]);
	    model("nc_member_label")->delete(['site_id' => $site_id]);
	    model("nc_member_level")->delete(['site_id' => $site_id]);
	    model("nc_member_recharge")->delete(['site_id' => $site_id]);
	    model("nc_member_address")->delete(['site_id' => $site_id]);
	    return success();
	}
}