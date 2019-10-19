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

namespace app\api\controller;

use app\common\controller\BaseApi;
use app\common\model\Member as MemberModel;
use app\common\model\Address as AddressModel;

/**
 * 控制器
 */
class Member extends BaseApi
{
	
	/***************************************会员收货地址开始************************ */
	/**
	 * 添加地址
	 * @param array $params
	 */
	public function addMemberAddress($params)
	{
		
		$member_model = new MemberModel();
		$id = $params['id'] ? $params['id'] : 0;
		
		$member_id = $this->checkAccessToken($params['site_id'], $params['access_token']);
		$params['member_id'] = $member_id;
		unset($params['app_key']);
		unset($params['access_token']);
		if ($id == 0) {
			unset($params['id']);
			$res = $member_model->addMemberAddress($params);
		} else {
			$res = $member_model->editMemberAddress($params);
		}
		return $res;
	}
	
	/**
	 * 修改地址
	 * @param array $params
	 */
	public function editMemberAddress($params)
	{
		$member_model = new MemberModel();
		$res = $member_model->editMemberAddress($params[0]);
		return $res;
	}
	
	/**
	 * 获取收获地址列表
	 */
	public function getMemberAddressList($params)
	{
		$member_model = new MemberModel();
		$member_id = $this->checkAccessToken($params['site_id'], $params['access_token']);
		$condition['member_id'] = $member_id;
		$field = "*";
		$order = "is_default desc";
		if (!empty($params['field'])) {
			$field = $params['field'];
		}
		if (!empty($params['order'])) {
			$order = $params['order'];
		}
		$res = $member_model->getMemberAddressList($condition, $field, $order);
		return $res;
	}
	
	/**
	 * 获取收获地址详情
	 */
	public function getMemberAddressInfo($params)
	{
		$member_model = new MemberModel();
		$condition['id'] = $params['id'];
		$res = $member_model->getMemberAddressInfo($condition);
		return $res;
	}
	
	/**
	 * 删除收获地址
	 */
	public function deleteMemberAddress($params)
	{
		$member_model = new MemberModel();
		$condition['id'] = $params['id'];
		$res = $member_model->deleteMemberAddress($condition);
		return $res;
	}
	
	/**
	 * 获取默认收获地址
	 */
	public function getMemberAddressDefault($params)
	{
		$access_token = isset($params['access_token']) ? $params['access_token'] : '';
		if (empty($access_token)) {
			return error('', 'request parameter access_token!');
		}
		//检测access_token
		$member_id = $this->checkAccessToken($params['site_id'], $access_token);
		if ($member_id == false) {
			return error('', 'access_token is expire!');
		}
		
		$member_model = new MemberModel();
		$condition = array(
			'member_id' => $member_id,
			'is_default' => 1
		);
		$res = $member_model->getMemberAddressInfo($condition);
		return $res;
	}
	
	/**
	 * 设为默认
	 */
	public function defaultAddress($params = [])
	{
		$id = $params['id'] ? $params['id'] : 0;
		$member_id = $this->checkAccessToken($params['site_id'], $params['access_token']);
		$member_model = new MemberModel();
		$res = $member_model->setMemberDefaultAddress($id, $member_id);
		return $res;
	}
	
	/***************************************会员收货地址结束************************ */
	
	/**
	 * 获取 当前会员账户信息
	 * 创建时间：2018年9月8日19:13:54
	 * @param $params
	 */
	public function getMemberAccountInfo($params)
	{
		$member_model = new MemberModel();
		$account_config_list = $member_model->getMemberAccountConfig($params['site_id']);
		$member_id = $this->checkAccessToken($params['site_id'], $params['access_token']);
		// 查询会员账户信息
		$member_account_info = $member_model->getMemberInfo([
			'member_id' => $member_id
		], "credit1,credit2,credit3,credit4,credit5,credit6,credit7");
		
		$res = array();
		if (!empty($account_config_list['data']) && !empty($member_account_info['data'])) {
			
			foreach ($member_account_info['data'] as $k => $v) {
				
				foreach ($account_config_list['data']['value'] as $k_config => $v_config) {
					if ($v_config['is_use'] == 1 && $v_config['is_exchange']) {
						if ($account_config_list['data']['value'][ $k_config ]['key'] == $k) {
							$account_config_list['data']['value'][ $k_config ]['num'] = $v;
							$res[ $v_config["key"] ] = $account_config_list['data']['value'][ $k_config ];
						}
					}
				}
			}
			return success($res);
		}
		
		return $res;
	}
	
	/**
	 * 获取 当前会员账户流水
	 * @param $params
	 */
	public function getMemberAccountList($params)
	{
		$member_model = new MemberModel();
		$page = $params['page'] ? $params['page'] : 1;
		$page_size = $params['page_size'] ? $params['page_size'] : PAGE_LIST_ROWS;
		$member_id = $this->checkAccessToken($params['site_id'], $params['access_token']);
		$type = $params['type'] ? $params['type'] : '';
		// 查询会员账户信息
		$member_account_list = $member_model->getMemberAccountPageList([
			'nmal.member_id' => $member_id, 'nmal.account_type' => $type
		], $page, $page_size, 'id desc', "*");
		
		$account_config_list = $member_model->getMemberAccountConfig($params['site_id']);
		$member_account_list_new = $member_account_list;
		foreach ($member_account_list['data']['list'] as $k => $v) {
			
			foreach ($account_config_list['data']['value'] as $k_config => $v_config) {
				if ($v_config['key'] == $v['account_type']) {
					$member_account_list_new['data']['list'][ $k ]['account_type_name'] = $v_config['name'];
				}
			}
		}
		
		return $member_account_list_new;
	}
	
	/**
	 * 修改会员昵称
	 * 创建时间：2018年9月15日10:13:22
	 * @param $params
	 */
	public function modifyNickName($params)
	{
		if (empty($params)) {
			return error('', 'missing parameter!');
		}
		$member_id = $this->checkAccessToken($params['site_id'], $params['access_token']);
		
		if (empty($member_id)) {
			return error('', 'missing parameter access_token!');
		}
		if (empty($params['nick_name'])) {
			return error('', 'missing parameter nick_name!');
		}
		
		$member_model = new MemberModel();
		$res = $member_model->editMember([
			'nick_name' => $params['nick_name']
		], [
			'member_id' => $member_id
		]);
		
		$this->initMemberInfo([ 'access_token' => $params['access_token'], 'site_id' => $params['site_id'] ]);
		
		return $res;
	}
	
	/**
	 * 系统用户修改密码
	 *
	 * @param number $member_id
	 * @param string $old_password
	 * @param string $new_password
	 */
	public function modifyUserPassword($params)
	{
		$member_id = $this->checkAccessToken($params['site_id'], $params['access_token']);
		if (empty($member_id)) {
			return error('', 'missing parameter access_token!');
		}
		if (empty($params)) {
			return error('', 'missing parameter!');
		}
		
		if (empty($params['old_password'])) {
			return error('', 'missing parameter old_password!');
		}
		
		if (empty($params['new_password'])) {
			return error('', 'missing parameter new_password!');
		}
		
		$member_model = new MemberModel();
		$condition = array(
			'member_id' => $member_id,
			'password' => data_md5($params['old_password'])
		);
		$member_details = $member_model->getMemberInfo($condition, "member_id");
		if (!empty($member_details['data']['member_id'])) {
			$data = array(
				'password' => data_md5($params['new_password'])
			);
			$res = $member_model->editMember($data, [
				'member_id' => $member_id
			]);
			return $res;
		} else {
			return error('', "修改失败");
		}
	}
	
	/**
	 * 检测手机号是否已注册
	 * 创建时间：2018年9月15日16:38:24
	 * @param $params
	 */
	public function checkMobileIsRegister($params)
	{
		if (empty($params)) {
			return error('', 'missing parameter!');
		}
		
		if (empty($params['site_id'])) {
			return error('', 'missing parameter site_id!');
		}
		
		if (empty($params['mobile'])) {
			return error('', 'missing parameter mobile!');
		}
		$member_model = new MemberModel();
		$res = $member_model->getMemberInfo([
			'mobile' => $params['mobile'],
			'site_id' => $params['site_id']
		]);
		return $res;
	}
	
	/**
	 * 修改手机号
	 * 创建时间：2018年9月15日16:50:17
	 * @param $params
	 */
	public function modifyMobile($params)
	{
		
		if (empty($params)) return error('', 'missing parameter!');
		if (empty($params['mobile'])) return error('', 'missing parameter mobile!');
		if (empty($params['access_token'])) return error('', 'missing parameter access_token!');
		$member_id = $this->checkAccessToken($params['site_id'], $params['access_token']);
		$member_model = new MemberModel();
		
		$account_is_exist = $member_model->checkAccountIsExist('mobile', $params['mobile'], $params['site_id'], $member_id);
		if ($account_is_exist['data']) return error('', '手机号已存在');
		
		$res = $member_model->editMember([
			'mobile' => $params['mobile']
		], [
			'member_id' => $member_id
		]);
		
		$this->initMemberInfo([ 'access_token' => $params['access_token'], 'site_id' => $params['site_id'] ]);
		
		return $res;
	}
	
	/**
	 * 修改真实姓名
	 * 创建时间：2018年9月15日16:50:17
	 * @param $params
	 */
	public function modifyRealName($params)
	{
		if (empty($params)) return error('', 'missing parameter!');
		if (empty($params['real_name'])) return error('', 'missing parameter real_name!');
		if (empty($params['access_token'])) return error('', 'missing parameter access_token!');
		$member_id = $this->checkAccessToken($params['site_id'], $params['access_token']);
		if (empty($member_id)) return error('', 'missing parameter access_token!');
		$member_model = new MemberModel();
		
		$res = $member_model->editMember([
			'real_name' => $params['real_name']
		], [
			'member_id' => $member_id
		]);
		
		$this->initMemberInfo([ 'access_token' => $params['access_token'], 'site_id' => $params['site_id'] ]);
		
		return $res;
	}
	
	/**
	 * 检测邮箱是否已注册
	 * 创建时间：2018年9月15日16:55:12
	 * @param $params
	 */
	public function checkEmailIsRegister($params)
	{
		if (empty($params)) {
			return error('', 'missing parameter!');
		}
		
		if (empty($params['site_id'])) {
			return error('', 'missing parameter site_id!');
		}
		
		if (empty($params['email'])) {
			return error('', 'missing parameter email!');
		}
		$member_model = new MemberModel();
		$res = $member_model->getMemberInfo([
			'email' => $params['email'],
			'site_id' => $params['site_id']
		]);
		return $res;
	}
	
	/**
	 * 修改邮箱
	 * 创建时间：2018年9月15日16:55:28
	 * @param $params
	 */
	public function modifyEmail($params)
	{
		if (empty($params)) return error('', 'missing parameter!');
		if (empty($params['email'])) return error('', 'missing parameter email!');
		if (empty($params['access_token'])) return error('', 'missing parameter access_token!');
		
		$member_id = $this->checkAccessToken($params['site_id'], $params['access_token']);
		$member_model = new MemberModel();
		
		$account_is_exist = $member_model->checkAccountIsExist('email', $params['email'], $params['site_id'], $member_id);
		if ($account_is_exist['data']) return error('', '邮箱已存在');
		
		$res = $member_model->editMember([
			'email' => $params['email']
		], [
			'member_id' => $member_id
		]);
		
		$this->initMemberInfo([ 'access_token' => $params['access_token'], 'site_id' => $params['site_id'] ]);
		
		return $res;
	}
	
	/**
	 * 修改会员头像
	 * 创建时间：2018年9月15日17:00:58
	 * @param $params
	 */
	public function modifyFace($params)
	{
		
		if (empty($params)) {
			return error('', 'missing parameter!');
		}
		
		if (empty($params['headimg'])) {
			return error('', 'missing parameter headimg!');
		}
		$member_id = $this->checkAccessToken($params['site_id'], $params['access_token']);
		if (empty($member_id)) {
			return error('', 'missing parameter access_token!');
		}
		
		$member_model = new MemberModel();
		$res = $member_model->editMember([
			'headimg' => $params['headimg']
		], [
			'member_id' => $member_id
		]);
		
		$this->initMemberInfo([ 'access_token' => $params['access_token'], 'site_id' => $params['site_id'] ]);
		
		return $res;
	}
	
	/**
	 * 修改会员用户名
	 * 创建时间：2018年10月16日11:50:22
	 * @param $params
	 */
	public function modifyUserName($params)
	{
		$member_info = api("System.Member.memberInfo", [ 'access_token' => $params['access_token'] ]);
		
		if (empty($params)) {
			return error('', 'missing parameter!');
		}
		if (empty($member_info['data'])) {
			return error('', 'missing parameter access_token!');
		}
		if (empty($params['username'])) {
			return error('', 'missing parameter username!');
		}
		
		$member_model = new MemberModel();
		$res = $member_model->editMember([
			'username' => $params['username']
		], [
			'member_id' => $member_info['data']['member_id']
		]);
		return $res;
	}
	
	/**
	 * 更新会员数据
	 */
	public function initMemberInfo($params)
	{
		if (!empty($params['access_token'])) {
			$member_info = api("System.Member.memberInfo", [ 'access_token' => $params['access_token'] ]);
			if ($member_info['code'] == 0) {
				cache("member_info_" . $params['site_id'] . $params['access_token'], $member_info['data']);
			}
		}
	}
	
	/**
	 * 会员信息查询
	 * @param array $params 传access_token
	 */
	public function memberInfo($params)
	{
		$access_token = isset($params['access_token']) ? $params['access_token'] : '';
		if (empty($access_token)) {
			return error('', 'NO_ACCESS_TOKEN');
		}
		//检测access_token
		$member_id = $this->checkAccessToken($params['site_id'], $access_token);
		if ($member_id == false) {
			return error('', 'PARAMETER_ERROR');
		}
		$member = new MemberModel();
		$member_info = $member->getMemberInfo([ 'member_id' => $member_id ]);
		return $member_info;
	}
	
	/**
	 * 获取地址
	 */
	public function getAreaList($params = [])
	{
		$member_model = new MemberModel();
		$area_level = $params['level'] ? $params['level'] : 1;
		$where['level'] = $area_level;
		$pid = $params['pid'] ? $params['pid'] : '';
		if ($pid) {
			$where['pid'] = $pid;
		}
		$area_list = $member_model->getAreaList($where, "id, pid, name, level", "id asc");
		return $area_list;
	}
	
	/**
	 * 获取地理位置id
	 */
	public function getGeographicId($params = [])
	{
		$address_model = new AddressModel();
		$address_array = explode(",", $params['address']);
		$province = $address_array[0];
		$city = $address_array[1];
		$district = $address_array[2];
		$subdistrict = $address_array[3];
		$province_list = $address_model->getAreaList([ "name" => $province, "level" => 1 ], "id", '');
		$province_id = !empty($province_list["data"]) ? $province_list["data"][0]["id"] : 0;
		$city_list = !empty($province_id) && !empty($city) ? $address_model->getAreaList([ "name" => $city, "level" => 2, "pid" => $province_id ], "id", '') : [];
		$city_id = !empty($city_list["data"]) ? $city_list["data"][0]["id"] : 0;
		$district_list = !empty($district) && $city_id > 0 && $province_id > 0 ? $address_model->getAreaList([ "name" => $district, "level" => 3, "pid" => $city_id ], "id", '') : [];
		$district_id = !empty($district_list["data"]) ? $district_list["data"][0]["id"] : 0;
		
		$subdistrict_list = !empty($subdistrict) && $city_id > 0 && $province_id > 0 && $district_id > 0 ? $address_model->getAreaList([ "name" => $subdistrict, "level" => 4, "pid" => $district_id ], "id", '') : [];
		$subdistrict_id = !empty($subdistrict_list["data"]) ? $subdistrict_list["data"][0]["id"] : 0;
		return [ "province_id" => $province_id, "city_id" => $city_id, "district_id" => $district_id, "subdistrict_id" => $subdistrict_id ];
	}
	
	/**
	 * 修改会员信息
	 * 创建时间：2018年10月16日11:50:22
	 * @param $params
	 */
	public function modifyUserInfo($params)
	{
		$member_info = api("System.Member.memberInfo", [ 'access_token' => $params['access_token'] ]);
		if (empty($params)) {
			return error('', 'missing parameter!');
		}
		
		if (empty($member_info)) {
			return error('', 'missing parameter access_token!');
		}
		
		$member_model = new MemberModel();
		$res = $member_model->editMember([
			'nick_name' => $params['nick_name']
		], [
			'member_id' => $member_info['data']['member_id']
		]);
		return $res;
	}
	
	/**
	 * 绑定第三方账号
	 * @param array $params
	 */
	public function memberAccountBind($params = [])
	{
		$access_token = isset($params['access_token']) ? $params['access_token'] : '';
		
		if (empty($access_token)) return error('', 'NO_ACCESS_TOKEN');
		if (empty($params['tag'])) return error('', 'missing parameters tag');
		if (empty($params['open_id'])) return error('', 'missing parameters open_id');
		
		//检测access_token
		$member_id = $this->checkAccessToken($params['site_id'], $access_token);
		if ($member_id == false) return error('', 'PARAMETER_ERROR');
		
		$member = new MemberModel();
		$res = $member->bindThirdAccount($params['site_id'], $member_id, $params['tag'], $params['open_id']);
		
		return $res;
		
	}
	
	/**
	 * 检测账号是否存在
	 * @param array $params
	 */
	public function checkAccountIsExist($params = [])
	{
		$member_id = '';
		
		if (empty($params['type'])) return error('', 'missing parameters type');
		if (empty($params['account'])) return error('', 'missing parameters account');
		
		if (isset($params['access_token'])) {
			$member_id = $this->checkAccessToken($params['site_id'], $params['access_token']);
		}
		
		$member_model = new MemberModel();
		$account_is_exist = $member_model->checkAccountIsExist($params['type'], $params['account'], $params['site_id'], $member_id);
		
		return $account_is_exist;
	}
	
	/**
	 * 获取交易或支付业务的账户类型
	 * @param $params
	 */
	public function getPayTacticsConfig($params)
	{
		$member_model = new MemberModel();
		$res = $member_model->getPayTacticsConfig($params["site_id"]);
		return $res;
	}

	/**
     * 修改会员信息
     * 创建时间：2019年10月11日11:50:22
     * @param $params
     */
    public function editUserInfo($params)
    {
        $member_model = new MemberModel();
        $member_info = api("System.Member.memberInfo", ['access_token' => $params['access_token']]);
        if (empty($params)) {
            return error('', 'missing parameter!');
        }

        if (empty($member_info)) {
            return error('', 'missing parameter access_token!');
        }
        $member_id = $member_info['data']['member_id'];

		if(isset($params['nick_name'])){
			$data['nick_name'] = $params['nick_name'];
		}
		if(isset($params['sex'])){
			$data['sex'] = $params['sex'];
		}
		if(isset($params['birthday'])){
			$data['birthday'] = $params['birthday'];
		}
		if(isset($params['mobile'])){
			$data['mobile'] = $params['mobile'];
			// 检测手机号是否存在
			$account_is_exist = $member_model->checkAccountIsExist('mobile', $params['mobile'], $params['site_id'], $member_id);
        	if ($account_is_exist['data']) return error('', '手机号已存在');
		}
		if(isset($params['email'])){
			$data['email'] = $params['email'];
			// 检测邮箱是否存在
			$account_is_exist = $member_model->checkAccountIsExist('email', $params['email'], $params['site_id'], $member_id);
			if ($account_is_exist['data']) return error('', '邮箱已存在');
		}
		
        $res = $member_model->editMember($data, ['member_id' => $member_id]);
        $this->initMemberInfo(['access_token' => $params['access_token'], 'site_id' => $params['site_id']]);
        return $res;
    }
	
}