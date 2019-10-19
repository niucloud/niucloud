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
namespace addon\app\Applet2\common\model;


/**
 * 小程序
 *
 * @author Administrator
 *
 */
class Applet
{
	
	/**
	 * 设置微信公众号业务应用快捷入口配置
	 *
	 * @param int $site_id
	 * @param array $data_array
	 */
	public function setAppletQuickEntryConfig($site_id, $data_array)
	{
		$get_config = model('nc_site_config')->getInfo([
			'name' => 'NC_APPLET_QUICK_ENTRY_CONFIG',
			'site_id' => $site_id
		]);
		if (empty($get_config)) {
			// 查询该数据是否存在
			$data = array(
				'site_id' => $site_id,
				'name' => 'NC_APPLET_QUICK_ENTRY_CONFIG',
				'type' => 1,
				'title' => '微信公众号应用快捷入口配置',
				'status' => 1,
				'value' => json_encode($data_array),
				'remark' => '设置微信公众号业务应用快捷入口配置',
				'create_time' => time()
			);
			$res = model('nc_site_config')->add($data);
		} else {
			
			$data = array(
				'update_time' => time(),
				'status' => 1,
				'value' => json_encode($data_array)
			);
			$res = model('nc_site_config')->update($data, [
				'name' => 'NC_APPLET_QUICK_ENTRY_CONFIG',
				'site_id' => $site_id
			]);
		}
		return $res === false ? error('', 'UNKNOW_ERROR') : success($res);
	}
	
	/**
	 * 获取微信公众号业务应用快捷入口配置
	 *
	 * @param int $site_id
	 */
	public function getAppletQuickEntryConfig($site_id)
	{
		$get_config = model('nc_site_config')->getInfo([
			'name' => 'NC_APPLET_QUICK_ENTRY_CONFIG',
			'site_id' => $site_id
		]);
		
		if (empty($get_config) || $get_config == null) {
			// 返回默认值
			$get_config['value'] = array();
		} else {
			$get_config['value'] = json_decode($get_config['value'], true);
		}
		return success($get_config);
	}
	
}