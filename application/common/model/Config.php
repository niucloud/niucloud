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

namespace app\common\model;

use think\Cache;
use think\Db;
use think\Config as ThinkConfig;

/**
 * 系统配置类
 */
class Config
{
	
	/**
	 * 添加系统配置
	 * @param array $data
	 * @return multitype:string mixed
	 */
	public function addConfig($data)
	{
		$res = model('nc_config')->add($data);
		if ($res === false) {
			return error($res, 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 修改系统配置
	 * @param array $data
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function editConfig($data, $condition)
	{
		$name = isset($data['name']) ? $data['name'] : '';
		if ($name === '') {
			return error('', '缺少必须参数name');
		}
		Cache::tag("config")->set($name, '');
		$res = model('nc_config')->update($data, $condition);
		if ($res === false) {
			return error($res, 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 删除系统配置
	 * @param array $condition
	 * @return multitype:string mixed
	 */
	public function deleteConfig($condition)
	{
		$name = isset($condition['name']) ? $condition['name'] : '';
		if ($name === '') {
			return error('', '缺少必须参数name');
		}
		Cache::tag("config")->set($name, '');
		$res = model('nc_config')->delete($condition);
		if ($res === false) {
			return error($res, 'UNKNOW_ERROR');
		}
		return success($res);
	}
	
	/**
	 * 设置系统配置
	 * @param array $data
	 */
	public function setConfig($data)
	{
		$name = isset($data['name']) ? $data['name'] : '';
		if (empty($name)) {
			return false;
		}
		$config_model = model('nc_config');
		$condition = [ 'name' => $name ];
		$info = $config_model->getInfo($condition, '*');
		Cache::tag("config")->set($name, "");
		return empty($info) ? $config_model->add($data) : $config_model->update($data, $condition);
	}
	
	/**
	 * 获取系统配置信息
	 * @param array $condition
	 * @param string $filed
	 */
	public function getConfigInfo($condition, $field = '*')
	{
		$name = isset($condition['name']) ? $condition['name'] : '';
		if ($name === '') {
			return error('', '缺少必须参数name');
		}
		$cache = Cache::tag("config")->get($name);
		if (!empty($cache)) {
			return success($cache);
		}
		$info = model('nc_config')->getInfo($condition, $field);
		Cache::tag("config")->set($name, $info);
		return success($info);
	}
	
	/**
	 * 获取数据库大小
	 * @param array $data
	 * @return multitype:string mixed
	 */
	public function getDatabaseSize()
	{
		$database = ThinkConfig::get('database');
		if (empty($database["database"]))
			return success(0);
		
		$database_name = $database["database"];
		$sql = "select concat(round(sum(DATA_LENGTH/1024/1024),2),'M')
         db_size from information_schema.tables
         where table_schema='{$database_name}'";
		$result = Db::query($sql);
		return success($result[0]['db_size']);
	}
	
}