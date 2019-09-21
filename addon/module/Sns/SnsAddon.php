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
namespace addon\module\Sns;

use addon\system\DiyView\common\model\BottomNav;
use app\common\controller\BaseAddon;

/**
 * 分类信息
 */
class SnsAddon extends BaseAddon
{
	public $replace;
	
	public $info = array(
		'name' => 'Sns',
		'title' => '分类信息',
		'description' => '分类信息',
		'status' => 1,
		'author' => '',
		'version' => '1.0',
		'visble' => 1,
		'type' => 'ADDON_MODULE',
		'category' => 'OTHER',
		'content' => '',
		//预置插件，多个用英文逗号分开
		'preset_addon' => '',
		'support_addon' => 'NcApplet',
		'support_app_type' => 'wap,weapp'
	);
	public $config = [];
	
	public $upload_path = __UPLOAD__;
	
	public function __construct()
	{
		parent::__construct();
		$this->config = $this->config_info;
		$this->replace = [
		];
	}
	
	/**
	 * 安装
	 */
	public function install()
	{
		$this->executeSql('install');
		return success();
	}
	
	/**
	 * 卸载
	 */
	public function uninstall()
	{
		$this->executeSql('uninstall');
		return success();
	}
	
	/**
	 * 初始化站点数据，在添加站点的时候用
	 * @param integer $site_id
	 * @return boolean
	 */
	public function addToSite($site_id)
	{
		$bottom_nav = new BottomNav();
		$value = '{"type":1,"fontSize":14,"textColor":"#333333","textHoverColor":"#419916","backgroundColor":"#ffffff","list":[{"imgUrl":"addon/module/Sns/sitehome/view/public/img/bottom_nav/home.png","imgHoverUrl":"addon/module/Sns/sitehome/view/public/img/bottom_nav/home_selected.png","title":"首页","link":{"addon_name":"Sns","addon_title":"分类信息","name":"SNS_INDEX","title":"首页","design_url":"","h5_url":"Sns://wap/index/index","web_url":"","weapp_url":"","aliapp_url":"","baiduapp_url":"","type":0,"icon":"addon/module/Sns/sitehome/view/public/img/menu_icon/sns_index.png","addon_icon":"/addon/module/Sns/icon.png","selected":false}},{"imgUrl":"addon/module/Sns/sitehome/view/public/img/bottom_nav/category.png","imgHoverUrl":"addon/module/Sns/sitehome/view/public/img/bottom_nav/category_selected.png","title":"栏目","link":{"addon_name":"Sns","addon_title":"分类信息","name":"SNS_CATEGORY","title":"栏目","design_url":"","h5_url":"Sns://wap/category/index","web_url":"","weapp_url":"","aliapp_url":"","baiduapp_url":"","type":0}},{"imgUrl":"addon/module/Sns/sitehome/view/public/img/bottom_nav/publish.png","imgHoverUrl":"addon/module/Sns/sitehome/view/public/img/bottom_nav/publish_selected.png","title":"发布","link":{"addon_name":"Sns","addon_title":"分类信息","name":"SNS_INFO_ADD","title":"发布信息","design_url":"","h5_url":"Sns://wap/member/publish","web_url":"","weapp_url":"","aliapp_url":"","baiduapp_url":"","type":0,"icon":"addon/module/Sns/sitehome/view/public/img/menu_icon/sns_index.png","addon_icon":"/addon/module/Sns/icon.png","selected":false}},{"imgUrl":"addon/module/Sns/sitehome/view/public/img/bottom_nav/history.png","imgHoverUrl":"addon/module/Sns/sitehome/view/public/img/bottom_nav/history_selected.png","title":"历史","link":{"addon_name":"Sns","addon_title":"分类信息","name":"SNS_HISTORY","title":"浏览历史","design_url":"","h5_url":"Sns://wap/member/history","web_url":"","weapp_url":"","aliapp_url":"","baiduapp_url":"","type":0,"icon":"addon/module/Sns/sitehome/view/public/img/menu_icon/sns_index.png","addon_icon":"/addon/module/Sns/icon.png","selected":false}},{"imgUrl":"addon/module/Sns/sitehome/view/public/img/bottom_nav/member.png","imgHoverUrl":"addon/module/Sns/sitehome/view/public/img/bottom_nav/member_selected.png","title":"我的","link":{"addon_name":"Sns","addon_title":"分类信息","name":"SNS_MEMBER","title":"我的","design_url":"","h5_url":"Sns://wap/member/index","web_url":"","weapp_url":"","aliapp_url":"","baiduapp_url":"","type":0,"icon":"addon/module/Sns/sitehome/view/public/img/menu_icon/sns_category.png","addon_icon":"/addon/module/Sns/icon.png","selected":false}}]}';
		$bottom_nav->setBottomNavConfig($value, $site_id,$this->info['name']);
		return success();
	}
	
	
	/**
	 * 删除站点数据--删除站点时调用
	 * @param integer $site_id
	 * @return boolean
	 */
	public function delFromSite($site_id)
	{
		success();
	}
	
	/**
	 * 复制站点数据--复制站点时调用
	 * @param integer $site_id
	 * @param integer $new_site_id
	 * @return boolean
	 */
	public function copyToSite($site_id, $new_site_id)
	{
		success();
	}
	
}