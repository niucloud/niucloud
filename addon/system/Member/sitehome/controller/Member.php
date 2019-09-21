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

namespace addon\system\Member\sitehome\controller;

use addon\system\Member\common\model\Member as MemberModel;
use app\common\controller\BaseSiteHome;
use app\common\model\Member as MemberCommonModel;
use think\Db;

/**
 * 商城会员
 * @author Administrator
 *
 */
class Member extends BaseSiteHome
{
	protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'
	
	public function __construct()
	{
		parent::__construct();
		$this->replace = [
			'ADDON_NS_MEMBER_CSS' => __ROOT__ . '/addon/system/Member/sitehome/view/public/css',
			'ADDON_NS_MEMBER_JS' => __ROOT__ . '/addon/system/Member/sitehome/view/public/js',
			'ADDON_NS_MEMBER_IMG' => __ROOT__ . '/addon/system/Member/sitehome/view/public/img',
		];
	}
	
	/**
	 * 商城会员
	 */
	public function memberList()
	{
		$member_model = new MemberModel();
		if (IS_AJAX) {
			$page_index = input('page', 1);
			$page_size = input('limit', PAGE_LIST_ROWS);
//			$order = input('order', 'nm.member_id desc');
			$search_keys = input('search_keys', '');
			$search_group = input('search_group', '');
			$search_label = input('search_label', '');
			$search_status = input('search_status', '');
			
			if ($search_keys !== '') {
				$condition['nm.username|nm.nick_name|nm.mobile|nm.email'] = [ 'like', '%' . $search_keys . '%' ];
			}
			if ($search_group !== '') {
				$condition['nm.member_group'] = $search_group;
			}
			if ($search_label) {
				$condition[] = [
					'exp',
					Db::raw("CONCAT(',',nm.member_label, ',') like '%," . $search_label . ",%'")
				];
			}
			if ($search_status !== '') {
				$condition['nm.status'] = $search_status;
			}
			$condition['nm.site_id'] = SITE_ID;
			
			$list = $member_model->getMemberPageList($condition, $page_index, $page_size);
			return $list;
		} else {
			$site_id = SITE_ID;
			$label_list = $member_model->getMemberLabelList([ 'site_id' => $site_id ]);
			$group_list = $member_model->getMemberGroupList([ 'site_id' => $site_id ]);
			$account_config_info = $member_model->getMemberAccountConfig($site_id);
			$this->assign('group_list', $group_list);
			$this->assign('label_list', $label_list);
			$this->assign('account_config_info', $account_config_info);
			return $this->fetch('member/member_list', [], $this->replace);
		}
		
	}
	
	/**
	 * 会员详情
	 */
	public function memberDetails()
	{
		$tab = input('tab', "basic_info");
		$this->assign('tab', $tab);
		
		$member_id = input('member_id', 0);
		$member_model = new MemberModel();
		$member_common_model = new MemberCommonModel();
		
		// 会员信息
		$info = $member_model->getMemberInfo([ 'member_id' => $member_id ]);
		$info = $info['data'];
		if (empty($info)) $this->error('未获取到会员信息');
		if (!empty($info['member_label'])) {
			$label = $member_model->getMemberLabelList([ 'site_id' => SITE_ID, 'label_id' => [ 'in', $info['member_label'] ] ]);
			$info['member_label_list'] = $label['data'];
		}
		
		if (!empty($info['member_group'])) {
			$member_group_info = $member_common_model->getMemberGroupInfo([ 'site_id' => SITE_ID, 'group_id' => $info['member_group'] ], 'group_name');
			$member_group_info = $member_group_info['data'];
			if (!empty($member_group_info)) {
				$info['member_group_name'] = $member_group_info['group_name'];
			}
		}
		
		// 会员标签
		$member_label_list = $member_model->getMemberLabelList([ 'site_id' => SITE_ID ]);
		$member_level_list = $member_model->getMemberLevelList([ 'site_id' => SITE_ID ]);
		$member_group_list = $member_model->getMemberGroupList([ 'site_id' => SITE_ID ]);
		$account_config_info = $member_model->getMemberAccountConfig(SITE_ID);
		$this->assign('account_config_info', $account_config_info['data']['value']);
		$this->assign('group_list', $member_group_list['data']);
		$this->assign('level_list', $member_level_list['data']);
		$this->assign('label_list', $member_label_list['data']);
		$this->assign('info', $info);
		return $this->fetch('member/member_details', [], $this->replace);
	}
	
	/**
	 * 添加会员
	 */
	public function addMember()
	{
		if (IS_AJAX) {
			$username = input('username', '');
			$mobile = input('mobile',  '');
			$email = input('email',  '');
			$nick_name = input('nick_name',  '');
			$real_name = input('real_name',  '');
			$member_group = input('member_group', 0);
			$member_label = input('member_label', 0);
			$password = input('password', 0);
			$data = array(
				'username' => $username,
				'mobile' => $mobile,
				'email' => $email,
				'nick_name' => $nick_name,
				'real_name' => $real_name,
				'member_group' => $member_group,
				'member_label' => $member_label,
				'site_id' => SITE_ID,
			);
			$member_model = new MemberModel();
			$data['password'] = data_md5($password);
			$data['register_time'] = time();
			$res = $member_model->addMember($data);
			return $res;
		}
	}
	
	/**
	 * 获取会员信息
	 */
	public function memberInfo()
	{
		if (IS_AJAX) {
			$member_id = input('member_id', 0);
			$member_model = new MemberModel();
			$info = $member_model->getMemberInfo([ 'member_id' => $member_id, 'site_id' => SITE_ID ]);
			return $info;
		}
	}
	
	/**
	 * 删除会员
	 */
	public function delmember()
	{
		if (IS_AJAX) {
			$member_id = input('member_id', 0);
			$member_model = new MemberModel();
			$res = $member_model->delMember([ 'member_id' => [ 'in', $member_id ], 'site_id' => SITE_ID ]);
			return $res;
		}
	}
	
	/**
	 * 修改会员状态
	 */
	public function modifyMemberStatus()
	{
		if (IS_AJAX) {
			$member_id = input('member_id', 0);
			$status = input('status', 1);
			$member_model = new MemberModel();
			$res = $member_model->modifyMemberStatus($status, [ 'member_id' => $member_id, 'site_id' => SITE_ID ]);
			return $res;
		}
	}
	
	/**
	 * 会员账户调整
	 */
	public function setAccount()
	{
		if (IS_AJAX) {
			$member_id = input('member_id', 0);
			$account = input()["account"];
			$member_model = new MemberModel();
			$count = 0;
			$res = error();
			foreach ($account as $k => $v) {
				if ((float) $v != 0) {
					$res = $member_model->addMemberAccount(SITE_ID, $member_id, $k, $v, '', '后台会员账户调整');
					if ($res['code'] != 0) {
						return $res;
					}
					$count++;
				}
			}
			if ($count == 0) {
				return success();
			}
			return $res;
		}
	}
	
	/**
	 * 重置会员密码
	 */
	public function resetPass()
	{
		if (IS_AJAX) {
			$member_id = input('member_id', 0);
			$member_model = new MemberModel();
			$res = $member_model->modifyMemberPassword('123456', [ 'member_id' => $member_id, 'site_id' => SITE_ID ]);
			return $res;
		}
	}
	
	/**
	 * 会员标签
	 */
	public function labelList()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$member = new MemberModel();
			$list = $member->getMemberLabelPageList([ 'site_id' => SITE_ID ], $page, $limit, 'create_time desc');
			return $list;
		}
		return $this->fetch('member/label_list', [], $this->replace);
	}
	
	/**
	 * 添加标签
	 */
	public function addLabel()
	{
		if (IS_AJAX) {
			$field = input('field/a', []);
			$label_id = input('label_id', 0);
			$data = array(
				'label_name' => $field['label_name'],
				'sort' => $field['sort'],
				'remark' => $field['remark'],
				'site_id' => SITE_ID,
			);
			
			$member_model = new MemberModel();
			if ($label_id == 0) {
				$data['create_time'] = time();
				$res = $member_model->addMemberLabel($data);
			} else {
				$data['modify_time'] = time();
				$res = $member_model->editMemberLabel($data, [ 'site_id' => SITE_ID, 'label_id' => $label_id ]);
			}
			
			return $res;
		}
	}
	
	/**
	 * 获取标签信息
	 */
	public function labelInfo()
	{
		if (IS_AJAX) {
			$label_id = input('label_id', 0);
			$member_model = new MemberModel();
			$info = $member_model->getMemberLabelInfo([ 'label_id' => $label_id, 'site_id' => SITE_ID ]);
			return $info;
		}
	}
	
	/**
	 * 删除标签
	 */
	public function deleteLabel()
	{
		if (IS_AJAX) {
			$label_id = input('label_id', 0);
			$member_model = new MemberModel();
			$res = $member_model->deleteMemberLabel([ 'label_id' => [ 'in', $label_id ], 'site_id' => SITE_ID ]);
			return $res;
		}
	}
	
	/**
	 * 会员等级
	 */
	public function levelList()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$member = new MemberModel();
			$list = $member->getMemberLevelPageList([ 'site_id' => SITE_ID ], $page, $limit, 'sort asc, level_id asc');
			return $list;
		}
		return $this->fetch('member/level_list', [], $this->replace);
	}
	
	/**
	 * 等级信息
	 */
	public function levelInfo()
	{
		if (IS_AJAX) {
			$level_id = input('level_id', 0);
			$member_model = new MemberModel();
			$info = $member_model->getMemberLevelInfo([ 'level_id' => $level_id, 'site_id' => SITE_ID ]);
			return $info;
		}
	}
	
	/**
	 * 添加等级
	 */
	public function addLevel()
	{
		if (IS_AJAX) {
			$field = input('field/a', []);
			$level_id = input('level_id', 0);
			$data = array(
				'level_name' => $field['level_name'],
				'sort' => $field['sort'],
				'site_id' => SITE_ID,
			);
			
			$member_model = new MemberModel();
			if ($level_id == 0) {
				$data['create_time'] = time();
				$res = $member_model->addMemberLevel($data);
			} else {
				$data['modify_time'] = time();
				$res = $member_model->editMemberLevel($data, [ 'site_id' => SITE_ID, 'level_id' => $level_id ]);
			}
			
			return $res;
		}
	}
	
	/**
	 * 删除等级
	 */
	public function deleteLevel()
	{
		if (IS_AJAX) {
			$level_id = input('level_id', 0);
			$member_model = new MemberModel();
			$res = $member_model->deleteMemberLevel([ 'level_id' => [ 'in', $level_id ], 'site_id' => SITE_ID ]);
			return $res;
		}
	}
	
	/**
	 * 调整排序
	 */
	public function setSort()
	{
		if (IS_AJAX) {
			$sort = input('sort', 0);
			$level_id = input('level_id', 0);
			$label_id = input('label_id', 0);
			$data = array(
				'sort' => $sort,
				'modify_time' => time()
			);
			$member_model = new MemberModel();
			if ($level_id > 0) {
				$res = $member_model->editMemberLevel($data, [ 'site_id' => SITE_ID, 'level_id' => $level_id ]);
			}
			
			if ($label_id > 0) {
				$res = $member_model->editMemberLabel($data, [ 'site_id' => SITE_ID, 'label_id' => $label_id ]);
			}
			
			return $res;
		}
	}
	
	/**
	 * 账户配置
	 */
	public function accountConfig()
	{
		$member = new MemberModel();
		$account_config_list = $member->getMemberAccountConfig(SITE_ID);
		if (IS_AJAX) {
			$key = input("key", "");
			$name = input("name", "");
			$is_use = input("is_use", "");
			$res = error();
			if (!empty($account_config_list['data']['value'])) {
				$data = $account_config_list['data']['value'];
				foreach ($data as $k => $v) {
					if ($v['key'] == $key) {
						$data[ $k ]['name'] = $name;
						//积分、余额特殊处理，不能禁用
						if ($key != "credit1" && $key != "credit2" && $key != "credit3") {
							$data[ $k ]['is_use'] = !empty($is_use) ? $is_use : 0;
						} else {
							$data[ $k ]['is_use'] = 1;
						}
					}
				}
				$res = $member->setMemberAccountConfig(SITE_ID, $data);
			}
			return $res;
		}
		$member_model = new MemberModel();
		$account_config = $member_model->getSiteAccountConfig($this->siteId);
		$this->assign("account_config", $account_config["data"]);
		$basic_tactics_config = $member_model->getBasicTacticsConfig($this->siteId);//账户默认业务策略
		$pay_tactics_config = $member_model->getPayTacticsConfig($this->siteId);//账户支付业务策略
		$this->assign("basic_tactics_config", $basic_tactics_config["data"]);
		$this->assign("pay_tactics_config", $pay_tactics_config["data"]);
		
		$this->assign('list', $account_config_list);
		return $this->fetch('member/account_config', [], $this->replace);
	}
	
	/**
	 * 会员账户明细
	 * 创建时间：2018年9月27日18:35:30
	 */
	public function accountDetail()
	{
		$member = new MemberModel();
		$account_config = $member->getMemberAccountConfig(SITE_ID);
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$account_type = input('account_type', '');
			$start_time = input('start_time', '');
			$end_time = input('end_time', '');
			$member_id = input("member_id", 0);
			$condition = array();
			$condition['nmal.member_id'] = $member_id;
			if ($account_type != '') $condition['nmal.account_type'] = $account_type;
			$condition['nmal.site_id'] = SITE_ID;
			if (!empty($start_time) && empty($end_time)) {
				$condition["nmal.create_time"] = [ "egt", date_to_time($start_time) ];
			} elseif (empty($start_time) && !empty($end_time)) {
				$condition["nmal.create_time"] = [ "elt", date_to_time($end_time) ];
			} elseif (!empty($start_time) && !empty($end_time)) {
				$condition["nmal.create_time"] = [ "between", [ date_to_time($start_time), date_to_time($end_time) ] ];
			}
			$list = $member->getMemberAccountPageList($condition, $page, $limit, 'nmal.create_time desc');
			$account_list = $account_config['data']['value'];
			foreach ($list['data']['list'] as $k => $v) {
				foreach ($account_list as $child_k => $child_v) {
					if ($v['account_type'] == $child_v['key']) {
						$list['data']['list'][ $k ]['account_name'] = $child_v['name'];
					}
				}
			}
			return $list;
		}
		
		$member_id = input("member_id", 0);
		$member_info = $member->getMemberInfo([ 'member_id' => $member_id ]);
		if (!empty($member_info['data'])) {
			$this->assign("account_config_info", $account_config);
			$this->assign('member_info', $member_info['data']);
			return $this->fetch('member/account_detail', [], $this->replace);
		} else {
			$this->error("未查询到该会员！");
		}
	}
	
	/**
	 * 账户配置
	 * @return \addon\system\Member\common\model\multitype
	 */
	public function getAccountConfig()
	{
		$member = new MemberModel();
		$config_list = $member->getMemberAccountConfig(SITE_ID);
		return $config_list;
	}
	
	/**
	 * 策划策略编辑
	 */
	public function basicTacticsConfig()
	{
		$value = input("tactics_value", "");
		$member = new MemberModel();
		$res = $member->setBasicTacticsConfig([ "value" => $value, "site_id" => $this->siteId ]);
		return $res;
	}
	
	/**
	 * 策划策略编辑
	 */
	public function payTacticsConfig()
	{
		$value = input("tactics_value", "");
		$member = new MemberModel();
		$res = $member->setPayTacticsConfig([ "value" => $value, "site_id" => $this->siteId ]);
		return $res;
	}
	
	/**
	 * 编辑会员教育状况信息
	 */
	public function editMemberInfo()
	{
		if (IS_AJAX) {
			
			$member = new MemberModel();
			$member_id = input('member_id', 0);
			$site_id = request()->siteid();
			
			$data = [];
			
			//基础信息
			$member_label = input('member_label', -1);
			$username = input('username', '');
			$nick_name = input('nick_name', '');
			$real_name = input('real_name', '');
			$mobile = input('mobile', '');
			$email = input('email', '');
			
			if ($member_label != -1) $data['member_label'] = $member_label;
			if ($username) $data['username'] = $username;
			if ($nick_name) $data['nick_name'] = $nick_name;
			if ($real_name) $data['real_name'] = $real_name;
			if ($mobile) $data['mobile'] = $mobile;
			if ($email) $data['email'] = $email;
			
			//详细信息
			$address = input('address', '');
			$alipay_no = input('alipay_no', '');
			$city = input('city', '');
			$district = input('district', '');
			$msn_no = input('msn_no', '');
			$province = input('province', '');
			$qq = input('qq', '');
			$sex = input('sex', '');
			$taobao_no = input('taobao_no', '');
			$weixin_no = input('weixin_no', '');
			$zipcode = input('zipcode', '');
			$full_address = input('full_address', '');
			if ($address) $data['address'] = $address;
			if ($alipay_no) $data['alipay_no'] = $alipay_no;
			if ($city) $data['city'] = $city;
			if ($district) $data['district'] = $district;
			if ($msn_no) $data['msn_no'] = $msn_no;
			if ($province) $data['province'] = $province;
			if ($qq) $data['qq'] = $qq;
			if ($sex) $data['sex'] = $sex;
			if ($taobao_no) $data['taobao_no'] = $taobao_no;
			if ($weixin_no) $data['weixin_no'] = $weixin_no;
			if ($zipcode) $data['zipcode'] = $zipcode;
			if ($full_address) $data['full_address'] = $full_address;
			$birthday = input('birthday', '');
			if (!empty($birthday)) {
				$birthday_arr = explode('-', $birthday);
				$data['birthdayyear'] = $birthday_arr[0];
				$data['birthdaymonth'] = $birthday_arr[1];
				$data['birthday'] = $birthday_arr[2];
				$data['zodiac'] = get_zodiac($birthday_arr[0]);
				$data['constellation'] = get_constellation($birthday_arr[1], $birthday_arr[2]);
			}
			
			//教育状况
			$education = input('education', '');
			$student_no = input('student_no', '');
			$grade = input('grade', '');
			$graduateschool = input('graduateschool', '');
			if (!empty($education)) $data['education'] = $education;
			if (!empty($student_no)) $data['student_no'] = $student_no;
			if (!empty($grade)) $data['grade'] = $grade;
			if (!empty($graduateschool)) $data['graduateschool'] = $graduateschool;
			
			//工作状况
			$company = input('company', '');
			$occupation = input('occupation', '');
			$position = input('position', '');
			$revenue = input('revenue', '');
			if (!empty($company)) $data['company'] = $company;
			if (!empty($occupation)) $data['occupation'] = $occupation;
			if (!empty($position)) $data['position'] = $position;
			if (!empty($revenue)) $data['revenue'] = $revenue;
			
			$res = $member->editMember($data, [ 'member_id' => $member_id, 'site_id' => $site_id ]);
			return $res;
		}
	}
	
	/**
	 * 修改会员分组
	 * @return string[]
	 */
	public function modifyMemberGroup()
	{
		$member_model = new MemberCommonModel();
		$member_id = input('member_id', 0);
		$member_group = input('member_group', 0);
		
		$site_id = request()->siteid();
		$condition = array(
			"site_id" => $site_id,
			"member_id" => $member_id,
		);
		$res = $member_model->modifyMemberGroup($member_group, $condition);
		return $res;
	}
}