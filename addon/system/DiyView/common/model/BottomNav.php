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

namespace addon\system\DiyView\common\model;


/**
 * 底部导航
 * @author Administrator
 *
 */
class BottomNav
{
	
	/**
	 * 获取底部导航配置
	 * @param int $site_id
	 * @param string $addon_name
	 */
	public function getBottomNavConfig($site_id, $addon_name)
	{
		$site_config = model('nc_site_config');
		$addon_name = strtoupper($addon_name);
		$name = 'DIY_VIEW_BOTTOM_NAV_CONFIG_' . $addon_name;
		$config = $site_config->getInfo([ 'name' => $name, 'site_id' => $site_id ], "*");
		if (empty($config)) {
			$data["site_id"] = $site_id;
			$data["name"] = $name;
			$data["title"] = "自定义底部导航_" . $addon_name;
			$data["value"] = "";
			$data["type"] = 1;
			$data["remark"] = "自定义底部导航_" . $addon_name;
			//上传配置
			$res = $site_config->add($data);
			$config = $site_config->getInfo([ 'name' => $name, 'site_id' => $site_id ], "*");
		}
		
		return success($config);
	}
	
	/**
	 * 设置底部导航配置
	 * @param $value
	 * @param $site_id
	 * @param $addon_name
	 * @return \multitype
	 */
	public function setBottomNavConfig($value, $site_id, $addon_name)
	{
		$site_config = model('nc_site_config');
		$data = array();
		$data["value"] = $value;
		
		$addon_name = strtoupper($addon_name);
		$name = 'DIY_VIEW_BOTTOM_NAV_CONFIG_' . $addon_name;
		$count = $site_config->getCount([ 'name' => $name, 'site_id' => $site_id ]);
		if ($count == 0) {
			$data["site_id"] = $site_id;
			$data["name"] = $name;
			$data["title"] = "自定义底部导航_" . $addon_name;
			$data["value"] = $value;
			$data["type"] = 1;
			$data["remark"] = "自定义底部导航_" . $addon_name;
			$res = $site_config->add($data);
		} else {
			$res = $site_config->update($data, [ 'name' => $name, 'site_id' => $site_id ]);
		}
		if ($res === false) {
			return error($res, 'UNKNOW_ERROR');
		}
		return success($res);
		
	}
}