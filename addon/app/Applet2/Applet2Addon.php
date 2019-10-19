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
namespace addon\app\Applet2;

use addon\app\Applet2\common\model\Applet as AppletModel;
use addon\app\Applet2\sitehome\controller\Index;
use addon\system\AliApp\common\model\AliApp;
use addon\system\BaiduApp\common\model\BaiduApp;
use addon\system\WeApp\common\model\WeApp;
use addon\system\Wechat\common\model\Wechat;
use app\common\controller\BaseAddon;
use app\common\model\Addon;
use app\common\model\Auth as AuthModel;
use app\common\model\DiyView;
use app\common\model\Login;
use app\common\model\Member;


/**
 * 微信公众号
 */
class Applet2Addon extends BaseAddon
{
	
	public $info = array(
		'name' => 'Applet2',
		'title' => '多平台小程序（新版）',
		'description' => '多平台小程序（新版）',
		'status' => 1,
		'author' => '',
		'version' => '1.0',
		'visble' => 1,
		'type' => 'ADDON_APP',
		'content' => '',
		//预置插件，多个用英文逗号分开
		'preset_addon' => 'DiyView,Email,File,FileQiniu,Message,OAuthLogin,OAuthLoginQQ,OAuthLoginWechat,Pay,PayAlipay,PayWechat,Sms,SmsAliyun,Wechat,WeApp,AliApp,BaiduApp',
		'support_addon' => '',
		'support_app_type' => 'wap,weapp,aliapp,baiduapp'
	);
	
	public $menu = [];
	public $styleClass = 'app\common\controller\Applet2SiteHomeStyle';  //应用样式类
	public $config;
	
	public function __construct()
	{
		parent::__construct();
		$config_array = $this->getConfig();
		$this->menu = $config_array['menu'];
		$this->config = $this->config_info;
	}
	
	/**
	 * 安装
	 */
	public function install()
	{
		return success();
	}
	
	/**
	 * 卸载
	 */
	public function uninstall()
	{
		return success();
	}
	
	/**
	 * 初始化站点数据，在添加站点的时候用
	 *
	 * @param integer $site_id
	 * @return boolean
	 */
	public function addToSite($site_id)
	{
		$member = new Member();
		$member->addDefaultMemberLevel($site_id);
		$data = array(
			"value" => json_encode([ 'type' => 1 ]),
			"site_id" => $site_id
		);
		$member->setMemberGroupConfig($data);
		
		$login_model = new Login();
		$value = '{"is_allow_register":"1","register_type_common":"1","random_name_prefix":"","name_keyword":"","pwd_length":"6","is_automatic":"1"}';
		$data = [
			'site_id' => $site_id,
			'value' => $value,
			'title' => '注册与访问设置',
			'remark' => '注册与访问设置',
		];
		$res = $login_model->setRegisterConfig($data);
		return success();
	}
	
	/**
	 * 删除站点数据--删除站点时调用
	 *
	 * @param integer $site_id
	 * @return boolean
	 */
	public function delFromSite($site_id)
	{
		return success();
	}
	
	/**
	 * 复制站点数据--复制站点时调用
	 *
	 * @param integer $site_id
	 * @param integer $new_site_id
	 * @return boolean
	 */
	public function copyToSite($site_id, $new_site_id)
	{
		return success();
	}
	
	/**
	 * 应用首页
	 * @param array $param
	 * @return string
	 */
	public function appHomeIndex($param = [])
	{
		$addon_app = $param['addon_app'];
		if ($addon_app == $this->info['name']) {
			$index = new Index();
			return $index->index();
		}
	}
	
	/**
	 * 应用菜单
	 * @param array $param
	 * @return string
	 */
	public function appMenu($param = [])
	{
		$baseSiteHome = $param['this'];
		if (!$baseSiteHome->siteInfo['addon_app'] == $this->info['name']) {
			return '';
		}
		
		if (!empty($baseSiteHome->siteInfo['icon'])) {
			$icon = $baseSiteHome->siteInfo['icon'];
		} else {
			$icon = __ROOT__ . '/addon/app/' . $baseSiteHome->siteInfo['addon_app'] . '/icon.png';
		}
		
		$baseSiteHome->assign("icon", $icon);
		
		//站点支持的端口
		$app_support_port = getSupportPort($this->info['support_app_type']);
		foreach ($app_support_port as $k => $v) {
			if ($k == 'wap') {
				//微信公众号配置信息
				$wechat_model = new Wechat();
				$wechat_info_config_info = $wechat_model->getWechatInfoConfig($baseSiteHome->siteId);
				$wechat_info_config_info = $wechat_info_config_info['data']["value"];
				$app_support_port[ $k ]['qrcode'] = !empty($wechat_info_config_info['wechat_code']) ? $wechat_info_config_info['wechat_code'] : "";
			} elseif ($k == 'weapp') {
				//微信小程序配置信息
				$weapp_model = new WeApp();
				$weapp_config_info = $weapp_model->getWeAppConfigInfo($baseSiteHome->siteId);
				$weapp_config_info = $weapp_config_info['data']['value'];
				$app_support_port[ $k ]['qrcode'] = !empty($weapp_config_info['weapp_code']) ? $weapp_config_info['weapp_code'] : "";
			} elseif ($k == 'aliapp') {
				//支付宝小程序配置信息
				$aliapp_model = new AliApp();
				$aliapp_config_info = $aliapp_model->getAliAppConfigInfo($baseSiteHome->siteId);
				$aliapp_config_info = $aliapp_config_info['data']['value'];
				$app_support_port[ $k ]['qrcode'] = !empty($aliapp_config_info['aliapp_code']) ? $aliapp_config_info['aliapp_code'] : "";
			} elseif ($k == 'baiduapp') {
				//百度小程序配置信息
				$baiduapp_model = new BaiduApp();
				$baiduapp_config_info = $baiduapp_model->getBaiduAppConfigInfo($baseSiteHome->siteId);
				$baiduapp_config_info = $baiduapp_config_info['data']['value'];
				$app_support_port[ $k ]['qrcode'] = !empty($baiduapp_config_info['baiduapp_code']) ? $baiduapp_config_info['baiduapp_code'] : "";
			}
		}
		
		$baseSiteHome->assign("app_support_port", $app_support_port);
		
		//加载自定义设计页面
		$show_type = 'H5';
		$diy_view = new DiyView();
		$addon_diy_view_list = $diy_view->getSiteDiyViewTempList($baseSiteHome->siteId, $show_type);
		$baseSiteHome->assign("addon_diy_view_list", $addon_diy_view_list);
		
		//根据当前地址查询菜单标识
		$addon = ADDON_NAME ? ADDON_NAME . '://' : '';
		$url = strtolower($addon . URL_MODULE);
		$baseSiteHome->assign("current_url", $url);
		
		$addon_name = input("addon_name", "");//插件名称
		
		$name = input("name", "");//自定义模板标识
		
		$addon_model = new Addon();
		$addons_list = $addon_model->getSiteAddonModuleList($baseSiteHome->siteId);
		$addons_list = $addons_list['data'];
		
		if (empty($addon_name)) {
			$addon_name = $this->getCurrentAddonName($baseSiteHome->siteId, $url, $addons_list);
		}
		$baseSiteHome->assign("addon_name", $addon_name);
		
		$auth_model = new AuthModel();
		$main_condition = [
			'site_id' => $baseSiteHome->siteId,
			'port' => 'SITEHOME',
		];
		if (!empty($addon_name) && $addon_name != $baseSiteHome->siteInfo['addon_app']) {
			
			$business_addon_name = [];
			foreach ($addons_list as $k => $v) {
				$business_addon_name[] = $v['name'];
			}
			//排除业务插件菜单
			$main_condition['module'] = [ 'not in', implode(",", $business_addon_name) ];
		}
		
		if ($baseSiteHome->groupInfo['is_system'] == 1) {
			$list = $auth_model->getSiteMenuList($main_condition, 'name,title,url,icon,icon_selected,is_blank,is_menu,menu_pid', 'sort asc');
		} else {
			$main_condition['name'] = [
				'in',
				$baseSiteHome->groupInfo['array']
			];
			$list = $auth_model->getSiteMenuList($main_condition, 'name,title,url,icon,icon_selected,is_blank,is_menu,menu_pid', 'sort asc');
		}
		
		$list = $list['data'];

//		我的应用
		$app_menu = [
			[
				'name' => 'MY_APP_ROOT',
				'title' => '应用',
				'url' => 'applet2://sitehome/app/moreapp',
				'menu_pid' => '',
				'sort' => 1,
				'is_menu' => 1,
				'icon' => '',
				'icon_selected' => 'application/sitehome/view/public/img/menu_icon/menu_website_selected_01.png',
				'child_list' => [],
				'is_blank' => 0
			]
		];
		
		if ($url == "applet2://sitehome/app/index" || (!empty($addon_name) && $addon_name != $baseSiteHome->siteInfo['addon_app'])) {
			
			//插件应用详情
			$addon_info = $addon_model->getAddonInfo([ 'name' => $addon_name ], "icon,title,support_app_type");
			$addon_info = $addon_info['data'];
			if (empty($addon_info)) {
				$this->error("非法操作");
			}
			$addon_info["support_app_type"] = getSupportPort($addon_info["support_app_type"]);
			$baseSiteHome->assign("app_addon_info", $addon_info);
			
			//业务应用单页
			$business_app_menu = [ [
				'name' => 'BUSINESS_APP_INDEX',
				'title' => '应用首页',
				"url" => addon_url('applet2://sitehome/app/index', [ 'addon_name' => $addon_name ]),
				'icon' => 'addon/app/Applet2/sitehome/view/public/img/permission_settings.png',
				'icon_selected' => '',
				'child_list' => [],
				'menu_pid' => 'MY_APP_ROOT',
				'is_menu' => 1,
				'is_blank' => 0
			] ];
			
			//首页
			$list = array_merge($list, $business_app_menu);
			
			$auth_model = new AuthModel();

			//插件应用相关的业务菜单
			$addon_menu = $this->getAddonMenu($baseSiteHome->siteId, $addon_name);

			//应用业务菜单
			$list = array_merge($list, $addon_menu);
			
			$setting_menu = [ [
				'name' => 'SETTING_APP',
				'title' => '功能设置',
				"url" => addon_url('sitehome/manager/modulegroup', [ 'addon_name' => $addon_name ]),
				'child_list' => [],
				'menu_pid' => 'MY_APP_ROOT',
				'is_menu' => 1,
				'is_blank' => 0
			] ];

			//查询插件的自定义模板
			$module_group_menu = [
				[
					'name' => 'SITEHOME_MANAGER_MODULE_GROUP',
					'title' => '权限设置',
					'child_list' => [],
					'menu_pid' => 'SETTING_APP',
					'url' => addon_url('sitehome/manager/modulegroup', [ 'addon_name' => $addon_name ]),
					'icon' => 'addon/app/Applet2/sitehome/view/public/img/permission_settings.png',
					'is_menu' => 1,
					'is_blank' => 0
				],
				[
					'name' => 'SITEHOME_MANAGER_MODULE_MESSAGE',
					'title' => '消息设置',
					'child_list' => [],
					'menu_pid' => 'SETTING_APP',
					'url' => addon_url('message://sitehome/index/modulemessagetype', [ 'addon_name' => $addon_name ]),
					'icon' => 'addon/app/Applet2/sitehome/view/public/img/info_settings.png',
					'is_menu' => 1,
					'is_blank' => 0
				],
			];
			
			//查询插件的自定义模板
			$diy_menu = [ [
				'name' => 'DIYVIEW_MODULE_ROOT',
				'title' => '页面装修',
				'url' => addon_url('diyview://sitehome/design/featureforapplet', [ 'addon_name' => $addon_name ]),
				'menu_pid' => 'MY_APP_ROOT',
				'is_blank' => 0,
				'is_menu' => 1,
				'child_list' => [],
			] ];
			$feature_menu = [ [
				'name' => 'ADDON_APP_FEATURE',
				'title' => '微页面',
				'url' => addon_url('diyview://sitehome/design/featureforapplet', [ 'addon_name' => $addon_name ]),
				'menu_pid' => 'DIYVIEW_MODULE_ROOT',
				'is_blank' => 0,
				'icon' => 'addon/app/Applet2/sitehome/view/public/img/renovation.png',
				'is_menu' => 1,
				'child_list' => [],
			] ];
			
			$app_page_link_menu = [ [
				'name' => 'APP_PAGE_LINK',
				'title' => '页面链接',
				"url" => addon_url('applet2://sitehome/app/quickentry', [ 'addon_name' => $addon_name ]),
				'icon' => 'addon/app/Applet2/sitehome/view/public/img/app_quick_entry.png',
				'child_list' => [],
				'menu_pid' => 'DIYVIEW_MODULE_ROOT',
				'is_menu' => 1,
				'is_blank' => 0
			] ];
			
			$feature_edit_menu = [ [
				'name' => 'ADDON_APP_FEATURE_EDIT',
				'title' => '微页面编辑',
				'url' => addon_url('diyview://sitehome/design/editforapplet', [ 'addon_name' => $addon_name ]),
				'menu_pid' => 'ADDON_APP_FEATURE',
				'is_blank' => 0,
				'is_menu' => 0,
				'child_list' => [],
			] ];
			
			$diy_edit_menu = [ [
				'name' => 'NC_DIYVIEW_SYSTEM_PAGE',
				'title' => '自定义面编辑',
				'url' => addon_url('diyview://sitehome/design/diyview', [ 'addon_name' => $addon_name ]),
				'menu_pid' => 'ADDON_APP_FEATURE',
				'is_blank' => 0,
				'is_menu' => 0,
				'child_list' => [],
			] ];
			
			foreach ($addon_diy_view_list as $k => $v) {
				if ($v['addon_info']['name'] == $addon_name) {
					if (!empty($v['view_list'])) {
						foreach ($v['view_list'] as $ck => $cv) {
							$c_name = strtolower($cv['name']);
							if ($c_name == $name) {
								$diy_edit_menu[0]['title'] = $cv['title'];
							}
						}
					}
					
				}
			}
			
			//判断是否有装修页面权限
			if ($auth_model->checkModuleAuth($addon_name, $baseSiteHome->groupInfo, "diyview_page_array")) {
				$list = array_merge($list, $diy_menu, $feature_menu, $feature_edit_menu, $diy_edit_menu, $app_page_link_menu);
				
				$this->adjustMenuPosition($list, [ "name" => "ADDON_APP_BOTTOM_NAV_DESIGN" ], [ 'menu_pid' => "DIYVIEW_MODULE_ROOT" ]);
			}

			//判断是否有应用管理页面权限
			if ($auth_model->checkModuleAuth($addon_name, $baseSiteHome->groupInfo, "auth_page_array")) {
				$list = array_merge($list, $module_group_menu, $setting_menu);
			}
			
		}
		
		//应用菜单下添加页面链接菜单
//		$list = array_merge($this->getAppQuickEntry($baseSiteHome->siteId), $app_menu, $list);
		$list = array_merge($app_menu, $list);
		
		//调整菜单上下级位置
		
		//粉丝管理移到会员管理菜单下
		$this->adjustMenuPosition($list, [ "name" => "NC_WECHAT_FANS" ], [ 'menu_pid' => "MEMBER_WECHAT_FANS", 'is_menu' => 1 ]);
		
		//粉丝标签管理移到粉丝管理菜单下
		$this->adjustMenuPosition($list, [ "name" => "NC_WECHAT_FANS_TAG" ], [ "menu_pid" => "MEMBER_WECHAT_FANS", 'is_menu' => 1 ]);
		
		//将消息管理、支付管理、支付记录、短信记录、第三方登录、邮件等菜单合并到设置-->功能设置
		$this->adjustMenuPosition($list, [ "name" => [ "||", "NC_MSG_TPL_INDEX", "NC_PAY_CONFIG", "NC_PAY_LIST", "NC_SMS_CONFIG", "NC_OAUTHLOGIN_LIST", "NC_EMAIL_CONFIG" ] ], [ "menu_pid" => "APPLET_CONFIG" ]);
		
		//显示消息管理菜单
		$this->adjustMenuPosition($list, [ "name" => "NC_MSG_TPL_INDEX" ], [ "is_menu" => 1 ]);
		
		//将编辑邮件消息模板菜单移到消息管理菜单下
		$this->adjustMenuPosition($list, [ "name" => "NC_EMAIL_EDIT" ], [ "menu_pid" => 'NC_MSG_TPL_INDEX' ]);
		
		//短信管理改名短信记录
		$this->adjustMenuPosition($list, [ "name" => "NC_SMS_CONFIG" ], [ "title" => "短信记录" ]);
		
		//将图片、音频、视频菜单移到文件管理下
		$this->adjustMenuPosition($list, [ "name" => "NC_FILE_IMAGE" ], [ "menu_pid" => "NC_FILE_INDEX" ]);
		$this->adjustMenuPosition($list, [ "name" => "NC_FILE_AUDIO" ], [ "menu_pid" => "NC_FILE_INDEX" ]);
		$this->adjustMenuPosition($list, [ "name" => "NC_FILE_VIDEO" ], [ "menu_pid" => "NC_FILE_INDEX" ]);
		
		foreach ($list as $k => $v) {
			//移除插件菜单，除更多应用外
			if ($v['menu_pid'] == 'ADDON_ROOT' || $v['name'] == 'NC_FILE_IMAGE_INDEX' || $v['name'] == 'ADDON_ROOT') {
				unset($list[ $k ]);
			}
			
			//移除部分自定义菜单
			if ($v['menu_pid'] == 'DIYVIEW_ROOT' && (($v['is_menu'] == 0 && $v['level'] < 4) || $v['name'] == 'NC_DIYVIEW_H5_TEMPLATE')) {
				unset($list[ $k ]);
			}
		}
		
		$list = array_values($list);
		
		$menu = list_to_tree($list, 'name', 'menu_pid', 'child_list', '');
		
		$this->sort($menu, 'INDEX_ROOT', 0);
		$this->sort($menu, 'ADDON_APP_FEATURE', 0);
		$this->sort($menu, 'APP_PAGE_LINK', 1);
		
		foreach ($menu as $k => $v) {
			if (!empty($v['child_list'])) {
				foreach ($v['child_list'] as $ck => $cv) {
					//赋值；设置-->功能设置的链接地址
					if ($cv['name'] == "APPLET_CONFIG") {
						$menu[ $k ]['child_list'][ $ck ]['url'] = $cv['child_list'][0]['url'];
					}
				}
			}
			
		}
		
		//当前选中的菜单
		$current_menu = $this->getCurrentMenu($baseSiteHome->siteId, $url, $list, $name, $addon_name, $baseSiteHome->siteInfo['addon_app']);
		
		$baseSiteHome->assign("current_menu", $current_menu);
		
		//面包屑
		$bread_crumb = $this->getBreadCrumb($baseSiteHome->siteId, $url, $list, $current_menu, $addon_name, $baseSiteHome->siteInfo['addon_app']);
		
		$baseSiteHome->assign("bread_crumb", $bread_crumb);
		
		$baseSiteHome->assign("list", $list);
		$baseSiteHome->assign("menu", $menu);
		
		return '';
	}
	
	/**
	 * 获取应用快捷入口
	 * @param $site_id
	 * @param $group_info
	 * @return array
	 */
	private function getAppQuickEntry($site_id)
	{
		$app_child_menu = [];
		$addon_model = new Addon();
		$wechat_model = new AppletModel();
		$app_quick_entry = $wechat_model->getAppletQuickEntryConfig($site_id);
		
		if (!empty($app_quick_entry['data']['value'])) {
			
			foreach ($app_quick_entry['data']['value'] as $k => $v) {
				$info = $addon_model->getAddonInfo([ 'name' => $v['addon_name'] ], 'name,title,icon');
				$info = $info['data'];
				if (!empty($info)) {
					$app_child_menu[] = [
						"name" => $info['name'],
						"title" => $info['title'],
						'icon' => $info['icon'],
						"menu_pid" => 'MY_APP_ROOT',
						"url" => addon_url('applet2://sitehome/app/index', [ 'addon_name' => $info['name'] ]),
						'child_list' => [],
						'is_blank' => 1
					];
				}
				
			}
			
		}
		
		return $app_child_menu;
	}
	
	/**
	 * 调整菜单位置
	 */
	private function adjustMenuPosition(&$menu, $condition, $value)
	{
		
		$fields = [];
		
		foreach ($condition as $k => $v) {
			$item = [
				'field' => $k,
				'judge' => '==',
				'value' => ''
			];
			
			if (is_array($v)) {
				$judge = array_shift($v);
				$item['judge'] = $judge;
				$item['value'] = $v;
			} else {
				$item['value'] = $v;
			}
			
			$fields[] = $item;
			
		}
		
		foreach ($menu as $k => $v) {
			
			$count = 0;//记录匹配条件
			foreach ($fields as $fk => $fv) {
				$field = $fv['field'];
				if ($fv['judge'] == "==") {
					if ($v[ $field ] == $fv['value']) {
						$count++;
					}
				} elseif ($fv['judge'] == "!=") {
					if (!in_array($v[ $field ], $fv['value'])) {
						$count++;
					}
					
				} elseif ($fv['judge'] == "||") {
					if (in_array($v[ $field ], $fv['value'])) {
						$count++;
					}
				}
				
			}
			
			if ($count == count($fields)) {
				foreach ($value as $value_k => $value_v) {
					$menu[ $k ][ $value_k ] = $value_v;
				}
			}
			
		}
		
	}
	
	/**
	 * 菜单排序
	 * @param $menu
	 * @param $name
	 * @param $index
	 */
	private function sort(&$menu, $name, $index)
	{
		foreach ($menu as $k => $v) {
			
			//一级
			if ($v['name'] == $name && $index < count($menu)) {
				$menu[ $k ] = $menu[ $index ];
				$menu[ $index ] = $v;
				break;
			} elseif (!empty($v['child_list'])) {
				
				foreach ($v['child_list'] as $second_k => $second_v) {
					
					//二级
					if ($second_v['name'] == $name && $index < count($v['child_list'])) {
						$menu[ $k ]['child_list'][ $second_k ] = $menu[ $k ]['child_list'][ $index ];
						$menu[ $k ]['child_list'][ $index ] = $second_v;
						break;
					} elseif (!empty($second_v['child_list'])) {
						
						foreach ($second_v['child_list'] as $third_k => $third_v) {
							
							//三级
							if ($third_v['name'] == $name && $index < count($second_v['child_list'])) {
								$menu[ $k ]['child_list'][ $second_k ]['child_list'][ $third_k ] = $menu[ $k ]['child_list'][ $second_k ]['child_list'][ $index ];
								$menu[ $k ]['child_list'][ $second_k ]['child_list'][ $index ] = $third_v;
								break;
							}
						}
					}
					
				}
			}
		}
		
		$menu = array_values($menu);
	}
	
	/**
	 * 根据当前路径查询插件名称
	 * @param $site_id
	 * @param $url
	 * @param  $addons_list
	 */
	private function getCurrentAddonName($site_id, $url, $addons_list)
	{
		$addon_name = '';
		$auth_model = new AuthModel();
		//查询当前链接属于哪个插件
		$info = $auth_model->getSiteMenuInfo([
			'site_id' => $site_id,
			'url' => $url,
		], 'module');
		
		foreach ($addons_list as $k => $v) {
			if ($v['name'] == $info['data']['module']) {
				$addon_name = $v['name'];
			}
		}
		return $addon_name;
	}
	
	/**
	 * 查询当前插件下的应用菜单
	 * @param $site_id
	 * @param $addon_name
	 */
	private function getAddonMenu($site_id, $addon_name)
	{
//		菜单查了两次
		$auth_model = new AuthModel();
		$list = $auth_model->getSiteMenuList([
			'site_id' => $site_id,
			'module' => $addon_name,
		], 'menu_id,name,title,url,icon,is_blank,level,is_menu,menu_pid', 'sort asc');
		$list = $list['data'];
		if (!empty($list)) {
			//将顶级父级设置为空
			foreach ($list as $k => $v) {
				if ($v['level'] == 2) {
					$list[ $k ]['menu_pid'] = 'MY_APP_ROOT';
				}
			}
		}
		return $list;
		
	}
	
	/**
	 * 获取面包屑
	 * @param $site_id
	 * @param $url
	 * @param $menu
	 * @param $name
	 * @param $addon_name
	 */
	private function getBreadCrumb($site_id, $url, $menu, $current_menu, $addon_name, $addon_app)
	{
		
		$auth_model = new AuthModel();
		if (!empty($url)) {
			$info = $auth_model->getFirstSiteMenu([
				'site_id' => $site_id,
				'url' => $url,
			], 'name', 'level desc');
		}
		
		$info = $info['data'];
		
		//插件应用中的微页面编辑菜单
		if (strpos($url, 'diyview://sitehome/design/editforapplet') !== false && !empty($addon_name) && $addon_name != $addon_app) {
			$info['name'] = 'ADDON_APP_FEATURE_EDIT';
		}

//		$flag = true;//标记是否需要显示面包屑，默认不显示，false 不显示，true 显示
		
		$current_bread_crumb = $this->breadCrumbRecursive($menu, $info['name']);
		$count = count($current_bread_crumb);
		if (!empty($current_bread_crumb)) {
			if ($count == 4) {
				//不需要显示一、二级
				unset($current_bread_crumb[0]);
				unset($current_bread_crumb[1]);
			} elseif ($count == 3) {
				//不需要显示一级
				unset($current_bread_crumb[0]);
			}
			$current_bread_crumb = array_values($current_bread_crumb);

//			if (!empty($current_menu[1]['child_list'])) {
//
//				foreach ($current_menu[1]['child_list'] as $k => $v) {
//					if ($v['name'] == $current_bread_crumb[ count($current_bread_crumb) - 1 ]['name']) {
//						$flag = false;
//					}
//				}
//			}
		}
		
		//菜单深度低于2级的过滤
//		if (!$flag || $count < 2) {
//			$current_bread_crumb = [];
//		}
		
		return $current_bread_crumb;
	}
	
	/**
	 * 面包屑递归
	 * @param $menu
	 * @param $name
	 * @return array
	 */
	private function breadCrumbRecursive($menu, $name)
	{
		$res = [];
		foreach ($menu as $k => $v) {
			if ($v['name'] == $name) {
				$v['selected'] = true;
				if ($v['menu_pid'] != "") {
					$res = $this->breadCrumbRecursive($menu, $v['menu_pid']);
				}
				$res[] = $v;
			}
		}
		
		return $res;
		
	}
	
	/**
	 * 获取当前的菜单
	 * @param $site_id
	 * @param $url
	 * @param $menu
	 * @param string $menu_pid
	 * @return array
	 */
	private function getCurrentMenu($site_id, $url, $menu, $name, $addon_name, $addon_app)
	{
		
		$auth_model = new AuthModel();
		if (!empty($url)) {
			$info = $auth_model->getFirstSiteMenu([
				'site_id' => $site_id,
				'url' => $url,
			], 'name', 'level desc');
		}
		
		$info = $info['data'];
		
		//以下页面没有菜单，无法选择，需要特殊处理
		
		//首页
		if (strpos($url, 'sitehome/index/index') !== false) {
			$info['name'] = "INDEX_ROOT";
		}
		
		//更多应用
		if (strpos($url, 'applet2://sitehome/app/moreapp') !== false) {
			$info['name'] = "MY_APP_ROOT";
		}
		
		//插件应用首页
		if (strpos($url, 'applet2://sitehome/app/index') !== false) {
			$info['name'] = "BUSINESS_APP_INDEX";
		}
		
		//插件页面链接
		if (strpos($url, 'applet2://sitehome/app/quickentry') !== false) {
			$info['name'] = "APP_PAGE_LINK";
		}
		
		//插件应用中权限设置菜单
		if (strpos($url, 'sitehome/manager/modulegroup') !== false) {
			$info['name'] = "SITEHOME_MANAGER_MODULE_GROUP";
		}
		
		if (strpos($url, 'sitehome/index/modulemessagetype') !== false) {
			$info['name'] = "SITEHOME_MANAGER_MODULE_MESSAGE";
		}
		
		//插件应用中的自定义页面
		if (strpos($url, 'diyview://sitehome/design/diyview') !== false && !empty($name) && !empty($addon_name) && $addon_name != $addon_app) {
			$info['name'] = 'ADDON_APP_FEATURE';
		}
		
		//插件应用中的微页面菜单
		if (strpos($url, 'diyview://sitehome/design/featureforapplet') !== false && !empty($addon_name) && $addon_name != $addon_app) {
			$info['name'] = 'ADDON_APP_FEATURE';
		}
		
		//插件应用中的微页面编辑菜单
		if (strpos($url, 'diyview://sitehome/design/editforapplet') !== false && !empty($addon_name) && $addon_name != $addon_app) {
			$info['name'] = 'ADDON_APP_FEATURE_EDIT';
		}
		
		$current_menu = $this->getCurrentMenuTree($menu, $info['name']);
		if (!empty($current_menu)) {
			
			if (count($current_menu) == 2) {
				
				$third_menu = (!empty($current_menu[1]['child_list'])) ? $current_menu[1]['child_list'][0] : [];
				$current_menu[1]['child_list'] = [];//清空子级菜单
				//设置同级菜单
				foreach ($menu as $mk => $mv) {
					if ($current_menu[1]['name'] == $mv['menu_pid']) {
						
						if ($mv['is_menu'] == 0) {
							continue;
						}
						if ($third_menu['name'] == $mv['name']) {
							$mv['selected'] = true;
							$mv['child_list'] = !empty($third_menu['child_list']) ? $third_menu['child_list'] : [];
						} else {
							$mv['selected'] = false;
						}
						$current_menu[1]['child_list'][] = $mv;
					}
				}
			}
			
		}
		return $current_menu;
	}
	
	/**
	 * 获取当前选中菜单的树结构
	 * @param $menu
	 * @param $name
	 * @return array
	 */
	private function getCurrentMenuTree($menu, $name)
	{
		$res = [];
		foreach ($menu as $k => $v) {
			if ($v['name'] == $name) {
				$v['selected'] = true;
				if ($v['menu_pid'] != "") {
					$res = $this->getCurrentMenuTree($menu, $v['menu_pid']);
				}
				if (count($res) == 2 && !empty($res[1]['child_list'])) {
					$res[ count($res) - 1 ]['child_list'][0]['child_list'] = $v;//四级
				} elseif (count($res) == 2) {
					$res[ count($res) - 1 ]['child_list'][] = $v;//三级
				} else {
					$res[] = $v;
				}
				
			}
		}
		
		return $res;
		
	}
	
}