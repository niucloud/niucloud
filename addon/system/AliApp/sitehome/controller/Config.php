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
namespace addon\system\AliApp\sitehome\controller;

use addon\system\AliApp\common\model\AliApp as AliAppModel;
use app\common\model\Site;
use app\common\model\Addon;
use app\common\model\DiyView;

/**
 * 支付宝小程序基础配置
 */
class Config extends Base
{
	/**
	 * 功能设置
	 */
	public function setting()
	{
		return $this->fetch('config/setting', [], $this->replace);
	}
	
	/**
	 * 访问统计
	 * @return mixed
	 */
	public function accessStatistics()
	{
		return $this->fetch('config/access_statistics', [], $this->replace);
		
	}
	
	/**
	 * 版本管理
	 */
	public function version()
	{
		
		return $this->fetch('config/version', [], $this->replace);
	}
	
	/**
	 * 小程序管理
	 */
	public function config()
	{
        $aliapp_model = new AliAppModel();
        if (IS_AJAX) {

            $aliapp_name = input('aliapp_name', '');
            $aliapp_code = input('aliapp_code', '');
            $appid = input('appid', '');
            $json_data = array(
                "appid" => $appid,
                "aliapp_name" => $aliapp_name,
                "aliapp_code" => $aliapp_code,
            );
            $data = array(
                "site_id" => SITE_ID,
                "value" => json_encode($json_data)
            );
            $res = $aliapp_model->setAliAppConfig($data);
            return $res;
        } else {
            $config_info_result = $aliapp_model->getAliAppConfigInfo(SITE_ID);
            $config_info = $config_info_result['data']['value'];
            $this->assign("config_info", $config_info);
            return $this->fetch('config/config', [], $this->replace);
        }
	}
	
	/**
	 * 打包测试
	 */
	public function test()
	{

		$site_model = new Site();
		$site_info = $site_model->getSiteInfo([ 'site_id' => $this->siteId ]);
		$site_addon_modules = $site_info['data']['addon_modules'];
		$site_addon_module_array = explode(',', $site_addon_modules);
		$addon_model = new Addon();
		$addons = $addon_model->getAddons();

		$class_name = get_addon_class($site_info['data']['addon_app']);
		$config_file = new $class_name();

		if (!isset($config_file->config['default_weapp'])) {

			echo '默认页面配置不存在';
			exit();
		}

		$addon_path_array = $addons['addon_path'];
		foreach ($site_addon_module_array as $k => $v) {
			if (!empty($v)) {
				$addon_path = $addon_path_array[ $v ];

				if (is_dir($addon_path . 'weapp/')) {
					dir_copy($addon_path . 'weapp/', 'attachment/' . $this->siteId . '/weapp/');
				}
			}
		}
		//变量替换
		$file = file_get_contents('attachment/' . $this->siteId . '/weapp/app.js');
		$file = str_replace('{{url}}', \think\Request::instance()->root(true) . '/', $file);
		$file = str_replace('{{site_id}}', 's' . $this->siteId, $file);
		$file = str_replace('{{app_key}}', $site_info['data']['app_key'], $file);
		$file = str_replace('{{site_title}}', $site_info['data']['site_name'], $file);
		file_put_contents('attachment/' . $this->siteId . '/weapp/app.js', $file);

		//读取配置app.json
		$pages_arr = array( $config_file->config['default_weapp'] ); //赋值默认页面
		$dir = 'attachment/' . $this->siteId . '/weapp/pages';
		$files = dir_scan($dir);

		foreach ($files as $key => $item) {

			if (!empty($item[0])) {
				$page_url = "pages/$key/" . explode('.', $item[0])[0];
				if ($page_url != $config_file->config['default_weapp']) {
					$pages_arr[] = $page_url;
				}

			} else {
				foreach ($item as $children_key => $children_item) {
					$page_url = "pages/$key/$children_key/" . explode('.', $children_item[0])[0];
					if ($page_url != $config_file->config['default_weapp']) {
						$pages_arr[] = $page_url;
					}
				}
			}

		}
		$app_data = array(
			'pages' => $pages_arr,
			'window' => array(
				'backgroundTextStyle' => 'light',
				'navigationBarBackgroundColor' => '#fff',
				'navigationBarTitleText' => 'WeChat',
				'navigationBarTextStyle' => 'black'
			),
			'sitemapLocation' => 'sitemap.json'
		);

		$app_data = json_encode($app_data, JSON_UNESCAPED_SLASHES);
		file_put_contents('attachment/' . $this->siteId . '/aliapp/app.json', $app_data);

		//读取配置diyview.json
		$dir = 'attachment/' . $this->siteId . '/aliapp/component';
		$files = dir_scan($dir);

		$diyview_data = array();
		foreach ($files as $key => $item) {
			$component_key = "component-" . str_replace('_', '-', $key);
			$diyview_data['usingComponents'][ $component_key ] = "../../component/$key/" . explode('.', $item[0])[0];
		}

		$diyview_data = json_encode($diyview_data, JSON_UNESCAPED_SLASHES);
		file_put_contents('attachment/' . $this->siteId . '/aliapp/pages/diyview/diyview.json', $diyview_data);

		//给diyview.wxml写数据
		$diy_view = new DiyView();
		$util_list = $diy_view->getDiyViewUtilList();

		$diy_view_xml = '';
		foreach ($util_list['data'] as $item) {
			$type = $item['name'];
			$title = $item['title'];
			$component_label = str_replace('_', '-', strtolower($type));
			$diy_view_xml .= <<<EOT
        \r<!-- $title -->
        <block wx:if="{{item.type == '$type'}}">
        <component-$component_label config="{{item}}" index="{{index}}"></component-$component_label>
        </block>\r
EOT;
		}
		$file = file_get_contents('attachment/' . $this->siteId . '/aliapp/pages/diyview/diyview.wxml');
		$file = str_replace('{{diy_view_xml}}', $diy_view_xml, $file);
		file_put_contents('attachment/' . $this->siteId . '/weapp/pages/diyview/diyview.wxml', $file);

		//压缩zip
		$file_zip_name = "attachment/$this->siteId/niucloud_$this->siteId.zip";
		$path = "attachment/$this->siteId/weapp";
		zip_dir($path, $file_zip_name, 'weapp');

		//文件强制下载
		dir_readfile($file_zip_name);
		unlink($file_zip_name);
		echo "整理完成";
	}
	
}