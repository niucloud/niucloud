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

use app\common\model\Site;
use util\api\SignClient;
use think\Cookie;

/**
 * 站点基类
 * @author Administrator
 *
 */
class BaseSite extends BaseController
{
	
	protected $client;
	protected $siteId;
	protected $site_info;
	protected $member_info = null;
	protected $access_token = null;
	protected $web_style;
	protected $wap_style;
	
	public function __construct()
	{
		parent::__construct();
		
		//初始化站点数据
		$this->initSite();
		
		//初始化会员数据
		$this->initMember();
		
		//初始化账户秘钥
//        $this->initClient();
	
	}
	
	/**
	 * 初始化站点数据
	 */
	private function initSite()
	{
		$this->siteId = request()->siteid();
		
		if ($this->siteId != 0) {
			$site_model = new Site();
			$site_info = $site_model->getSiteInfo([
				'site_id' => $this->siteId
			]);
			
			$this->site_info = $site_info['data'];
			$this->assign("site_info", $this->site_info);
			
			if (!empty($this->site_info['pc_template'])) {
				$pc_template = json_decode($this->site_info['pc_template'], true);
				$this->web_style = $pc_template['style'];
			} else {
				$this->web_style = 'default_style';
			}
			
			$this->wap_style = 'default_style';
			
			//网站标题
			$this->assign("title", "");
			
			$this->assign('web_style', $this->web_style);
			
			$this->assign('wap_style', "wap@style/$this->wap_style/base");
			
			//api接口地址
			$this->assign('API_URL', API_URL);
			
			$this->assign("addon_app", strtolower($this->site_info['addon_app']));
			
			//电脑端主页风格加载
			$this->assign('web_base', "addon/app/" . $this->site_info['addon_app'] . "/web/view/" . $this->web_style . "/base.html");
			//电脑端会员风格加载
			$this->assign('web_member_base', "addon/app/" . $this->site_info['addon_app'] . "/web/view/" . $this->web_style . "/member/member_base.html");
			//手机端主页风格加载
			$this->assign('wap_base', "addon/app/" . $this->site_info['addon_app'] . "/wap/view/" . $this->wap_style . "/base.html");

			//手机端会员风格加载
			$this->assign('wap_member_base', "addon/app/" . $this->site_info['addon_app'] . "/wap/view/" . $this->wap_style . "/member/member_base.html");
			
		}
	}
	
	/**
	 * 初始化会员数据
	 */
	protected function initMember()
	{
		$this->access_token = Cookie::get("access_token_" . request()->siteid());
		$this->assign("access_token", $this->access_token);
		if (!empty($this->access_token)) {
			$member_info = cache("member_info_" . SITE_ID . $this->access_token);
			if (empty($member_info)) {
				$member_info = api("System.Member.memberInfo", [ 'access_token' => $this->access_token ]);
				
				if ($member_info['code'] == 0) {
					cache("member_info_" . SITE_ID . $this->access_token, $member_info);
				}
			}
			$this->member_info = $member_info['data'];
			$this->assign("member_info", $member_info);
			
		}
	}
	
	/**
	 * 初始化client
	 */
	protected function initClient()
	{
		if (!empty($this->site_info)) {
			if (!empty($this->site_info['app_key'])) {
				$this->client = new SignClient($this->site_info['app_key'], $this->site_info['app_secret']);
			}
		}
	}
	
	/**
	 * 获取自定义模板数据
	 * @param array $params name字段为模板标识
	 */
	protected function getDiyView($params)
	{
//		$params['diy_type'] = 'wap';
		if (!empty($params['data'])) {
			$params['data'] = json_encode($params['data']);
		}
		return api('DiyView.Diy.getDiyView', $params);
	}
	
	/**
	 * 加载页面(non-PHPdoc)
	 * @see \think\Controller::fetch()
	 */
	protected function fetch($template = '', $vars = [], $replace = [], $config = [])
	{
		$view_replace_str = [
			'WEB_ADDON_CSS' => __ROOT__ . '/addon/app/' . $this->site_info['addon_app'] . '/web/view/' . $this->web_style . '/public/css',
			'WEB_ADDON_JS' => __ROOT__ . '/addon/app/' . $this->site_info['addon_app'] . '/web/view/' . $this->web_style . '/public/js',
			'WEB_ADDON_IMG' => __ROOT__ . '/addon/app/' . $this->site_info['addon_app'] . '/web/view/' . $this->web_style . '/public/img',
			'WEB_ADDON_PLUGIN' => __ROOT__ . '/addon/app/' . $this->site_info['addon_app'] . '/web/view/' . $this->web_style . '/public/plugin',
			'WAP_ADDON_CSS' => __ROOT__ . '/addon/app/' . $this->site_info['addon_app'] . '/wap/view/' . $this->wap_style . '/public/css',
			'WAP_ADDON_JS' => __ROOT__ . '/addon/app/' . $this->site_info['addon_app'] . '/wap/view/' . $this->wap_style . '/public/js',
			'WAP_ADDON_IMG' => __ROOT__ . '/addon/app/' . $this->site_info['addon_app'] . '/wap/view/' . $this->wap_style . '/public/img',
			'WAP_ADDON_PLUGIN' => __ROOT__ . '/addon/app/' . $this->site_info['addon_app'] . '/wap/view/' . $this->wap_style . '/public/plugin',
			'WEB_STYLE' => $this->web_style,
			'ADDON_APP' => $this->site_info['addon_app']
		];
		
		if (empty($replace)) {
			$replace = $view_replace_str;
		} else {
			$replace = array_merge($view_replace_str, $replace);
		}
		return $this->view->fetch($template, $vars, $replace, $config);
	}
}