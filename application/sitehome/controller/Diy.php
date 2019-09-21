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
namespace app\sitehome\controller;

use app\common\controller\BaseSiteHome;
use app\common\model\Addon;
use app\common\model\DiyView;
use app\common\model\Site;
use addon\system\AliApp\common\model\AliApp;
use addon\system\BaiduApp\common\model\BaiduApp;
use addon\system\WeApp\common\model\WeApp;

/**
 * 系统自定义页面
 *
 * @package app\sitehome\controller
 */
class Diy extends BaseSiteHome
{
	
	/**
	 * 网站首页
	 * @return mixed
	 */
	public function index()
	{
		$name = input("name", "DIYVIEW_SITE");
		$name = strtoupper($name);
		$this->assign('name', $name);
		return $this->fetch('diy/index');
	}
	
	/**
	 * 会员中心
	 * @return mixed
	 */
	public function memberIndex()
	{
		$name = input("name", "DIYVIEW_MEMBER");
		$name = strtoupper($name);
		$this->assign('name', $name);
		return $this->fetch('diy/member_index');
	}
	
	/**
	 * 链接选择
	 */
	public function link()
	{
		$link = input("link", []);
		$is_array = true;//记录是否是数组，后续判断受该变量影响
		if (!empty($link)) {
			$link = json_decode($link, true);
			$is_array = is_array($link);
		}
		
		$diy_view_model = new DiyView();
		
		$site_model = new Site();
		$site_info_result = $site_model->getSiteInfo([ "site_id" => $this->siteId ]);
		$site_info = $site_info_result["data"];
		$site_addon_str = $site_info["addon_app"];
		$site_addon_str .= empty($site_addon_str) ? $site_info["addon_modules"] : "," . $site_info["addon_modules"];
		
		//查询当前站点的插件应用链接集合
		$res = $diy_view_model->getDiyLinkList([ "ncl.addon_name" => [ "in", $site_addon_str ] ]);
		$res = $res['data'];
		
		//查询当前站点的微页面
		$condition = array(
			'nsdv.site_id' => $this->siteId,
			'nsdv.type' => 'DEFAULT',
			'nsdv.show_type' => 'H5',
			'ndva.addon_name' => null, //排除插件中的自定义模板
		);
		
		$site_diy_view_list = $diy_view_model->getSiteDiyViewPageList($condition, 1, 0, "nsdv.create_time desc");
		$site_diy_view_list = $site_diy_view_list['data']['list'];
		
		$addon_model = new Addon();
		
		$link_list = array();
		
		// 调整链接结构，先遍历，插件分类
		foreach ($res as $k => $v) {
			$value = array();
			$value['addon_name'] = $v['addon_name'];
			$value['addon_title'] = $v['addon_title'];
			$value['icon'] = $v['addon_icon'];
			$value['list'] = [];
			$column = array_column($link_list, 'addon_name');
			if (!in_array($v['addon_name'], $column)) {
				array_push($link_list, $value);
			}
		}
		
		foreach ($site_diy_view_list as $k => $v) {
			$value = array();
			$addon_info = $addon_model->getAddonInfo([ 'name' => $v['addon_name'] ], 'title,icon');
			$addon_info = $addon_info ['data'];
			$value['addon_name'] = $v['addon_name'];
			$value['addon_title'] = $addon_info['title'];
			$value['icon'] = $addon_info['icon'];
			$value['list'] = [];
			$column = array_column($link_list, 'addon_name');
			if (!in_array($v['addon_name'], $column)) {
				array_push($link_list, $value);
			}
		}
		
		$temp_link = [];
		
		// 遍历每一个链接，将其添加到对应的插件分类中
		foreach ($link_list as $diy_k => $diy_v) {
			
			//遍历插件固定自定义页面
			foreach ($res as $fixed_k => $fixed_v) {
				if ($diy_v['addon_name'] == $fixed_v['addon_name']) {
					if (!empty($link) && $is_array && $link['name'] == $fixed_v['name']) {
						//对象方式匹配
						$fixed_v['selected'] = true;
					} elseif (!empty($link) && !$is_array && strtolower($link) == strtolower($fixed_v['h5_url'])) {
						//字符串方式匹配
						$fixed_v['selected'] = true;
						$temp_link = $fixed_v;
					} else {
						$fixed_v['selected'] = false;
					}
					array_push($link_list[ $diy_k ]['list'], $fixed_v);
				}
			}
			
			//遍历微页面
			foreach ($site_diy_view_list as $page_k => $page_v) {
				
				if ($diy_v['addon_name'] == $page_v['addon_name']) {
					
					$item = [
						'id' => $page_v['id'],
						'name' => $page_v['name'],
						'title' => $page_v['title'],
						'addon_icon' => "",
						'addon_name' => $page_v['addon_name'],
						'addon_title' => $diy_v['addon_title'],
						'design_url' => '',
						'web_url' => '',
						'h5_url' => addon_url("diyview://wap/index/page", [ 'name' => $page_v['name'] ]),
						'weapp_url' => '/pages/diyview/diyview?name=' . $page_v['name'],
						'aliapp_url' => '',
						'baiduapp_url' => '',
						'icon' => '',
						'type' => 0
					];
					
					if (!empty($link) && $is_array && $link['name'] == $page_v['name']) {
						//对象方式匹配
						$item['selected'] = true;
					} elseif (!empty($link) && !$is_array && strtolower($link) == strtolower($page_v['h5_url'])) {
						//字符串方式匹配
						$item['selected'] = true;
						$temp_link = $page_v;
					} else {
						$item['selected'] = false;
					}
					array_push($link_list[ $diy_k ]['list'], $item);
					
				}
			}
		}
		
		if (!$is_array) {
			$link = $temp_link;
		}
		
		$this->assign("link", $link);
		$this->assign("link_list", $link_list);
		return $this->fetch('diy/link');
	}
	
	
	/**
	 * 推广链接
	 */
	public function promote()
	{
		$data = input("data", '');
		if (!empty($data)) {
			$data = json_decode($data, true);
		}
		
		$class_name = get_addon_class($data['addon_name']);
		$class = new $class_name();
		
		$list = [];
		
		//插件支持的端口
		$app_support_port = getSupportPort($class->info['support_app_type']);
		foreach ($app_support_port as $k => $v) {
			$item = [
				'port' => $k,
				'name' => $v['name'],
				'logo' => $v['logo'],
				'qrcode' => '',
				'url' => ''
			];
			if ($k == 'wap') {
				
				$qrcode_url = addon_url($data['h5_url']);
				$path = 'attachment/' . SITE_ID . '/qrcode';
				$file_name = strtolower($data['name']) . '_quick_entry_qrcode';
				if (file_exists($path . '/' . $file_name . '.png') === false) {
					$qrcode_url = qrcode($qrcode_url, $path, $file_name);
					$qrcode_url = str_replace('attachment/', '', $qrcode_url);
				} else {
					$qrcode_url = $path . '/' . $file_name . '.png';
					$qrcode_url = str_replace('attachment/', '', $qrcode_url);
				}
				$item['name'] = 'H5';
				$item['qrcode'] = $qrcode_url;
				$item['url'] = $data['h5_url'];
			} elseif ($k == 'weapp') {
				//微信小程序配置信息
				$weapp_model = new WeApp();
				$weapp_config_info = $weapp_model->getWeAppConfigInfo($this->siteId);
				$weapp_config_info = $weapp_config_info['data']['value'];
				$item['qrcode'] = !empty($weapp_config_info['weapp_code']) ? $weapp_config_info['weapp_code'] : "";
				$item['url'] = $data['weapp_url'];
			} elseif ($k == 'aliapp') {
				//支付宝小程序配置信息
				$aliapp_model = new AliApp();
				$aliapp_config_info = $aliapp_model->getAliAppConfigInfo($this->siteId);
				$aliapp_config_info = $aliapp_config_info['data']['value'];
				$item['qrcode'] = !empty($aliapp_config_info['aliapp_code']) ? $aliapp_config_info['aliapp_code'] : "";
				$item['url'] = $data['aliapp_url'];
			} elseif ($k == 'baiduapp') {
				//百度小程序配置信息
				$baiduapp_model = new BaiduApp();
				$baiduapp_config_info = $baiduapp_model->getBaiduAppConfigInfo($this->siteId);
				$baiduapp_config_info = $baiduapp_config_info['data']['value'];
				$item['qrcode'] = !empty($baiduapp_config_info['baiduapp_code']) ? $baiduapp_config_info['baiduapp_code'] : "";
				$item['url'] = $data['baiduapp_url'];
			}
			$list[] = $item;
		}
		
		$site_model = new Site();
		$site_info_result = $site_model->getSiteInfo([ "site_id" => $this->siteId ]);
		$site_info = $site_info_result["data"];
		
		$icon = $site_info['icon'];//默认分享图片
		
		if (empty($site_info['icon'])) {
			$site_info['icon'] = __ROOT__ . '/addon/app/' . $site_info['addon_app'] . '/icon.png';
			$icon = $site_info['icon'];
		} else {
			$site_info['icon'] = img($site_info['icon']);
		}
		
		$this->assign("icon", $icon);
		
		$this->assign('site_info', $site_info);
		
		$name = 'PROMOTE_' . $data['name'];
		
		$site_config_info = $site_model->getSiteConfigInfo([ 'site_id' => $this->siteId, 'name' => $name ]);
		$site_config_info = $site_config_info ['data'];
		$value = [];
		if (!empty($site_config_info['value'])) {
			$value = json_decode($site_config_info['value'], true);
		}
		
		$value['share_image'] = '';
		
		if (empty($value['share_title'])) {
			$value['share_title'] = $data['title'];
		}
		
		$this->assign("value", $value);
		$this->assign("data", $data);
		$this->assign("list", $list);
		return $this->fetch('diy/promote');
	}
	
	public function editPromote()
	{
		if (IS_AJAX) {
			
			$name = input('name', '');
			$title = input('title', '');
			$share_title = input('share_title', '');
			$share_desc = input('share_desc', '');
			$share_image = input('share_image', '');
			
			$site_model = new Site();
			
			$name = 'PROMOTE_' . $name;
			$value = json_encode([ 'share_title' => $share_title, 'share_desc' => $share_desc, 'share_image' => $share_image ]);
			$data = [
				'name' => $name,
				'site_id' => $this->siteId,
				'value' => $value,
				'title' => '推广链接_' . $title,
				'remark' => '推广链接_' . $title,
				'update_time' => time()
			];
			$res = $site_model->editSiteConfig($data, [ 'site_id' => $this->siteId, 'name' => $name ]);
			return $res;
			
		}
	}
}