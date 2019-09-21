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

use app\common\model\Addon;
use think\View;
use traits\controller\Jump;

\think\Loader::import('controller/Jump', TRAIT_PATH, EXT);

/**
 * 初始化钩子
 */
abstract class BaseAddon
{
	use Jump;
	//视图实例对象
	protected $view = null;
	//插件信息
	protected $info = '';
	//错误信息
	protected $error = '';
	//插件路径
	protected $addon_path = '';
	//插件配置文件路径
	protected $config_file = '';
	//插件配置文件
	public $config_info = [];
	
	public function __construct()
	{
		$view_replace_str = config("view_replace_str");
		$this->view = new View([], $view_replace_str);
		$this->addon_path = $this->getAddonPath();
		
		if (is_file($this->addon_path . 'config.php')) {
			
			$this->config_file = $this->addon_path . 'config.php';
			$this->config_info = $this->getConfig();
		}
	}
	
	/**
	 * 获取插件的绝对路径
	 */
	final protected function getAddonPath()
	{
		$class = get_class($this);
		$arr = explode('\\', $class);
		return ADDON_PATH . $arr[1] . DS . $arr[2] . DS;
	}
	
	/**
	 * 检测插件信息
	 */
	final public function checkInfo()
	{
		$info_check_keys = [ 'name', 'title', 'description', 'status', 'author', 'version' ];
		foreach ($info_check_keys as $value) {
			if (!array_key_exists($value, $this->info))
				return false;
		}
		$addon_model = new Addon();
		$addons_info = $addon_model->getAddonInfo([ 'name' => $this->info['name'] ]);
		if ($addons_info['data']) {
			return false;
		}
		return true;
	}
	
	/**
	 * 执行sql文件
	 */
	final protected function executeSql($sql_name)
	{
		$sql_string = file_get_contents($this->addon_path . 'data/' . $sql_name . '.sql');
		if ($sql_string) {
			$sql = explode(";\n", str_replace("\r", "\n", $sql_string));
			foreach ($sql as $value) {
				$value = trim($value);
				if (!empty($value)) {
					\think\Db::execute($value);
				}
			}
		}
	}
	
	/**
	 * 模板变量赋值
	 * @access protected
	 * @param mixed $name 要显示的模板变量
	 * @param mixed $value 变量的值
	 * @return Action
	 */
	final protected function assign($name, $value = '')
	{
		$this->view->assign($name, $value);
		return $this;
	}
	
	/**
	 * 用于显示模板的方法
	 */
	final protected function fetch($templateFile = CONTROLLER_NAME, $assign = [], $replace = [])
	{
		if (!is_file($templateFile)) {
			$template_array = explode('/', $templateFile);
			$model = '';
			if (count($template_array) == 3) {
				$model = array_shift($template_array) . '/';
				$templateFile = implode($template_array, '/');
			}
			$templateFile = $this->addon_path . $model . 'view/' . $templateFile . '.' . config('template.view_suffix');
			if (!is_file($templateFile)) {
				var_dump("模板不存在:$templateFile");
//				throw new \Exception("模板不存在:$templateFile");
			}
		}
		return $this->view->fetch($templateFile, $assign, $replace);
	}
	
	/**
	 * 读取配置文件
	 */
	final protected function getConfig()
	{
		
		$config_array = include $this->config_file;
		
		return $config_array;
	}
	
	/**
	 * 创建站点时，初始化系统自定义模板数据
	 * 创建时间：2018年9月25日14:59:50
	 */
	final public function initSiteDiyViewData($site_id)
	{
		if (isset($this->config_info['diy'])) {
			$diy_view = $this->config_info['diy'];
			if (isset($diy_view['view'])) {
				$site_diy_view_data = [];
				foreach ($diy_view['view'] as $k => $v) {
					$site_diy_view_item = [
						'site_id' => $site_id,
						'name' => $v['name'],
						'addon_name' => $this->info['name'],
						'title' => $v['title'],
						'value' => $v['value'],
						'type' => "DEFAULT",
						'create_time' => time(),
						'show_type' => $v['type'],
						'icon' => isset($v['icon']) ? $v['icon'] : '',
					];
					$site_diy_view_data [] = $site_diy_view_item;
				}
				if (!empty($site_diy_view_data)) {
					//添加系统预先加载自定义模板
					model("nc_site_diy_view")->addList($site_diy_view_data);
				}
			}
		}
	}
	
	/**
	 * 必须实现安装方法
	 * @return ['code' => 0|1, 'message' => '', 'data' => []]
	 */
	abstract public function install();
	
	/**
	 * 必须实现卸载方法
	 * @return ['code' => 0|1, 'message' => '', 'data' => []]
	 */
	abstract public function uninstall();
	
	/**
	 * 必须实现 添加模块基础数据到站点
	 * @return ['code' => 0|1, 'message' => '', 'data' => []]
	 */
	abstract public function addToSite($site_id);
	
	/**
	 * 必须实现 从站点删除模块基础数据方法
	 * @return ['code' => 0|1, 'message' => '', 'data' => []]
	 */
	abstract public function delFromSite($site_id);
	
	/**
	 * 复制站点数据--复制站点时调用
	 * @param integer $site_id
	 * @param integer $new_site_id
	 * @return ['code' => 0|1, 'message' => '', 'data' => []]
	 */
	abstract public function copyToSite($site_id, $new_site_id);
}