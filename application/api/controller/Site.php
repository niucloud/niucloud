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
use app\common\model\Site as SiteModel;

/**
 * 站点信息
 * @author Administrator
 *
 */
class Site extends BaseController
{
	
	/**
	 * 站点信息查询
	 * @param array $params 传site_id
	 * @return multitype:string mixed |\app\common\model\unknown
	 */
	public function getSiteInfo($params)
	{
		$site_model = new SiteModel();
		$info = $site_model->getSiteInfo($params['condition']);
		return $info;
	}
	
	/**
	 * 站点联系信息设置
	 * 赵海雷
	 */
	public function contactSetting()
	{
		$site_model = new Site();
		$condition['site_id'] = $this->siteId;
		if (IS_AJAX) {
			$value = input('value', '');
			$data = json_decode($value, true);
			
			$res = $site_model->editSite($data, $condition);
			return $res;
		} else {
			
			$info = $site_model->getSiteInfo($condition);
			$this->assign('info', $info['data']);
			return $this->fetch('Manager/contact_setting');
		}
	}
	
	/**
	 * 更新站点二维码
	 * 赵海雷
	 */
	public function updateSiteQrcode()
	{
		
		$site_id = $this->siteId;
		
		$domain = input('domain', '');
		$path = 'attachment/' . $site_id . '/images';
		$name = 'site_qrcode_' . time() . rand(1000, 9999);
		$qrcode_url = qrcode($domain, $path, $name);
		
		if ($qrcode_url) {
			//在更新了二维码之后立即执行数据库的更新操作 并删除原有二维码图片
			$site_model = new Site();
			$condition['site_id'] = $this->siteId;
			$info = $site_model->getSiteInfo($condition);
			if (!empty($info['data']['qrcode_url'])) {
				$path = 'attachment/' . $info['data']['qrcode_url'];
				@unlink($path);
			}
			$qrcode_url = str_replace('attachment/', '', $qrcode_url);
			$data = [
				'domain' => $domain,
				'qrcode_url' => $qrcode_url,
			];
			$site_model->editSite($data, $condition);
			return success($qrcode_url);
		} else {
			return error($qrcode_url);
		}
	}
}