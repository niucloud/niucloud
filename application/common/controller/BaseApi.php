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

namespace app\common\controller;

use think\Controller;
use app\common\model\Site;

/**
 * api基类
 * @author Administrator
 *
 */
class BaseApi extends Controller
{
	
	protected $params;
	
	public function __construct($params)
	{
		//执行父类构造函数
		parent::__construct();
		$this->params = $params;
		//检测access_token
	}
	
	/**
	 * 检测会员access_token(正确返回member_id,错误返回0)
	 * @param int $site_id
	 * @param string $access_token
	 */
	protected function checkAccessToken($site_id, $access_token)
	{
		$site_model = new Site();
		$site_info = $site_model->getSiteInfo([ 'site_id' => $site_id ]);
		$check_member_id = decrypt($access_token, $site_info['data']['app_secret']);
		if ($check_member_id) {
			return $check_member_id;
		} else {
			return 0;
		}
	}
	
	/**
	 * 获取会员id
	 * @param $access_token
	 * @param $site_id
	 * @return array
	 */
	protected function getMemberId($access_token, $site_id)
	{
		if (empty($access_token)) {
			return error('', 'NO_ACCESS_TOKEN');
		}
		//检测access_token
		$member_id = $this->checkAccessToken($site_id, $access_token);
		if ($member_id == false) {
			return error('', 'PARAMETER_ERROR');
		}
		return success($member_id);
	}
}