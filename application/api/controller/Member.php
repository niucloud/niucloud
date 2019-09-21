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

/**
 * 控制器
 */
class Member extends BaseApi
{
	/**
	 * 会员信息查询
	 * @param array $params 传access_token
	 * @return multitype:string mixed |\app\common\model\unknown
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

}