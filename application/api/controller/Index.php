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

use app\common\controller\BaseController;
use util\api\ApiProtocol;
use app\common\model\Site;

/**
 * 控制器
 */
class Index extends BaseController
{
	
	private $site_info = '';
	
	public function index()
	{
		$method = input('method', '');
		$version = input('version', '');
		$app_key = input(ApiProtocol::APP_ID_KEY, '');
		$sign = input(ApiProtocol::SIGN_KEY, '');
		$params = input();
		if (empty($app_key)) {
			return json_encode(error('', 'PARAMETER_ERROR'), JSON_UNESCAPED_UNICODE);
		}
		if (empty($method)) {
			return json_encode(error('', 'PARAMETER_ERROR'), JSON_UNESCAPED_UNICODE);
		}
		unset($params[ ApiProtocol::SIGN_KEY ]);
		$method_array = explode('.', $method);
		if ($method_array[0] == 'NcWeb' || $method_array[0] == 'NiuCloud') {
			//niucloud官网接口
			$auth_info = model('nc_user')->getInfo([ 'app_key' => $params[ ApiProtocol::APP_ID_KEY ] ]);
			if (empty($auth_info)) {
				return json_encode(error('', 'request parameter app_key!'), JSON_UNESCAPED_UNICODE);
			}
			// 根据公钥查询私钥
			$check_key = ApiProtocol::sign($auth_info['app_secret'], $params, $params[ ApiProtocol::SIGN_METHOD_KEY ]);
			if ($check_key != $sign) {
				return json_encode(error('', 'SIGN_ERROR'), JSON_UNESCAPED_UNICODE);
			}
			
		} else {
			$site_info = model('nc_site')->getInfo([
				'app_key' => $params[ ApiProtocol::APP_ID_KEY ]
			]);
			
			if (empty($site_info)) {
				return json_encode(error('', FAIL), JSON_UNESCAPED_UNICODE);
			}
			$this->site_info = $site_info;
			// 根据公钥查询私钥
			$check_key = ApiProtocol::sign($site_info['app_secret'], $params, $params[ ApiProtocol::SIGN_METHOD_KEY ]);
			if ($check_key != $sign) {
				return json_encode(error('', 'SIGN_ERROR'), JSON_UNESCAPED_UNICODE);
			}
			request()->siteid($site_info['site_id']);
			$params['site_id'] = $site_info['site_id'];
		}
		$data = get_api_data($method, $params);
		return json_encode($data, JSON_UNESCAPED_UNICODE);
	}
	
	/**
	 * api通过内部调用
	 */
	public function get()
	{
		header('content-type:application:json;charset=utf8');
		header('Access-Control-Allow-Origin:' . SOURCE_URL);
		header('Access-Control-Allow-Methods:POST');
		header('Access-Control-Allow-Headers:x-requested-with,content-type');
		$method = input('method', '');
		$version = input('version', '');
		$param = input('param', '');
		$param = json_decode($param, true);
		if (empty($method)) {
			return json_encode(error('', 'PARAMETER_ERROR'), JSON_UNESCAPED_UNICODE);
		}
		if (empty($param['app_key'])) {
			return json_encode(error('', 'PARAMETER_ERROR'), JSON_UNESCAPED_UNICODE);
		}
		$site_info = model('nc_site')->getInfo([
			'app_key' => $param['app_key']
		]);
		
		if (empty($site_info)) {
			return json_encode(error('', FAIL), JSON_UNESCAPED_UNICODE);
		}
		$this->site_info = $site_info;
		request()->siteid($site_info['site_id']);
		$site = new Site();
		$site->initHook($site_info['site_id']);
		$param['site_id'] = $site_info['site_id'];
		$data = get_api_data($method, $param);
		return json_encode($data, JSON_UNESCAPED_UNICODE);
	}
	
}