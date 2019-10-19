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

namespace addon\app\Applet2\sitehome\controller;

use app\common\controller\BaseSiteHome;
use app\common\model\Member as MemberModel;

/**
 * 商城会员分组
 * @author Administrator
 *
 */
class Group extends BaseSiteHome
{
	protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'
	
	public function __construct()
	{
		parent::__construct();
		$this->replace = [];
	}
	
	/**
	 * 会员等级
	 */
	public function groupList()
	{
		if (IS_AJAX) {
			$page = input('page', 1);
			$limit = input('limit', PAGE_LIST_ROWS);
			$member_model = new MemberModel();
			$list = $member_model->getMemberGroupPageList([ 'site_id' => SITE_ID ], $page, $limit, 'create_time desc');
			return $list;
		}
		return $this->fetch('group/group_list', [], $this->replace);
	}
	
	/**
	 * 等级信息
	 */
	public function groupInfo()
	{
		if (IS_AJAX) {
			$level_id = input('level_id', 0);
			$member_model = new MemberModel();
			$info = $member_model->getMemberGroupInfo([ 'level_id' => $level_id, 'site_id' => SITE_ID ]);
			return $info;
		}
	}
	
	/**
	 * 添加等级
	 */
	public function addGroup()
	{
		if (IS_AJAX) {
			$group_name = input("group_name", "");
			$group_id = input("group_id", "");
			$credit = input("credit", "");//成长值
			$data = array(
				'group_name' => $group_name,
				'credit' => $credit,
				'site_id' => $this->siteId,
			);
			$member_model = new MemberModel();
			if ($group_id == 0) {
				$data['create_time'] = time();
				$res = $member_model->addMemberGroup($data);
			} else {
				$data['modify_time'] = time();
				$res = $member_model->editMemberGroup($data, [ 'site_id' => SITE_ID, 'group_id' => $group_id ]);
			}
			return $res;
		}
	}
	
	/**
	 * 删除等级
	 */
	public function deleteGroup()
	{
		if (IS_AJAX) {
			$group_id = input('group_id', 0);
			$member_model = new MemberModel();
			$res = $member_model->deleteMemberGroup([ 'group_id' => [ 'in', $group_id ], 'site_id' => $this->siteId ]);
			return $res;
		}
	}
	
	
	/**
	 * 获取会员组变更设置
	 */
	public function getMemberGroupConfig()
	{
		$member_model = new MemberModel();
		$config = $member_model->getMemberGroupConfig($this->siteId);
		return $config;
	}
	
	/**
	 * 会员组变更设置
	 * @return mixed
	 */
	public function setMemberGroupConfig()
	{
		$type = input("group_config", 1);
		$json_data = array(
			"type" => $type
		);
		$data = array(
			"value" => json_encode($json_data),
			"site_id" => $this->siteId
		);
		$member_model = new MemberModel();
		$result = $member_model->setMemberGroupConfig($data);
		return $result;
	}
}