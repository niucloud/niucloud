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
use app\common\model\Site;

class Manage extends BaseApi
{
	
	/**
	 * seo设置
	 * @return \think\mixed
	 */
	public function seo($params)
	{
		$site_model = new Site();
		$info = $site_model->getSiteConfigInfo([ 'site_id' => $params['site_id'], 'name' => 'SITE_SEO_CONFIG' ]);
		return success(json_decode($info['data']['value'], true));
	}
	
	/**
	 * 备案信息
	 */
	public function record($params)
	{
		$site_model = new Site();
		$info = $site_model->getSiteConfigInfo([ 'site_id' => $params['site_id'], 'name' => 'SITE_RECORD' ]);
		return success(json_decode($info['data']['value'], true));
	}
	
	/**
	 * 搜索
	 * @return \think\mixed
	 */
	public function search($params)
	{
		$site_model = new Site();
		$info = $site_model->getSiteConfigInfo([ 'site_id' => $params['site_id'], 'name' => 'SITE_SEARCH_CONFIG' ]);
		return success(json_decode($info['data']['value'], true));
	}
	
	/**
	 * 版权信息
	 * @return \think\mixed
	 */
	public function copyright($params)
	{
		$site_model = new Site();
		$info = $site_model->getSiteConfigInfo([ 'site_id' => $params['site_id'], 'name' => 'SITE_COPYRIGHT' ]);
		return success(json_decode($info['data']['value'], true));
	}
	
	
	/**
	 * 注册信息
	 * @return \think\mixed
	 */
	public function register($params)
	{
		$site_model = new Site();
		$info = $site_model->getSiteConfigInfo([ 'site_id' => $params['site_id'], 'name' => 'SITE_REGISTER_CONFIG' ]);
		
		$config_info = [
			'is_allow_register' => '1',
			'register_type_common' => '1',
			'is_automatic' => 1
		];
		if (!empty($info['data']) && !empty($info['data']['value'])) {
			$config_info = json_decode($info['data']['value'], true);
		}
		return success($config_info);
	}
	
	/**
	 * 注册协议
	 * @return \think\mixed
	 */
	public function agreement($params)
	{
		$site_model = new Site();
		$info = $site_model->getSiteConfigInfo([ 'site_id' => $params['site_id'], 'name' => 'SITE_REGISTER_AGREEMENT' ]);
		return success(json_decode($info['data']['value'], true));
	}
}