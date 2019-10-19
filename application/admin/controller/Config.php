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

namespace app\admin\controller;

use app\common\controller\BaseAdmin;
use app\common\model\Config as ConfigModel;
use app\common\model\Notice as NoticeModel;
use think\Request;
use util\api\WebClient;

/**
 * 系统配置类
 */
class Config extends BaseAdmin
{
	public $app_key = '';
	public $app_secret = '';
	public $domain = '';
	
	public function index()
	{
		$this->getSystemConfig();
		return $this->fetch('config/index');
	}
	
	/**
	 * 站点设置
	 */
	public function siteconfig()
	{
		if (IS_AJAX) {
			$app_debug = input('develop_status', "");
			$log_type = input('log_status', "");
			if (!empty($app_debug)) {
				$pat[0] = "app_debug";
				if ($app_debug == "false") {
					$rep[0] = false;
				} else {
					$rep[0] = true;
				}
			}
			if (!empty($app_debug)) {
				$pat[0] = "app_debug";
				if ($app_debug == "false") {
					$rep[0] = false;
				} else {
					$rep[0] = true;
				}
			}
			if (!empty($log_type)) {
				$pat[0] = "type";
				if ($log_type == "false") {
					$rep[0] = "test";
				} else {
					$rep[0] = "File";
				}
			}
			if (is_array($pat) and is_array($rep)) {
				for ($i = 0; $i < count($pat); $i++) {
					$pats[ $i ] = '/\'' . $pat[ $i ] . '\'(.*?),/';
					if (!empty($app_debug)) {
						if ($rep[0] == false) {
							$reps[ $i ] = "'" . $pat[ $i ] . "'" . "=>" . 'false' . ",";
						} else {
							$reps[ $i ] = "'" . $pat[ $i ] . "'" . "=>" . 'true' . ",";
						}
					} else if (!empty($log_type)) {
						if ($rep[0] == "test") {
							$pats[ $i ] = "'" . $pat[ $i ] . "'" . "=> 'File',";
							$reps[ $i ] = "'" . $pat[ $i ] . "'" . "=> " . "'" . $rep[ $i ] . "',";
						} else {
							$pats[ $i ] = "'" . $pat[ $i ] . "'" . "=> 'test',";
							$reps[ $i ] = "'" . $pat[ $i ] . "'" . "=> " . "'" . $rep[ $i ] . "',";
						}
					}
				}
				$fileurl = APP_PATH . "config.php";
				$string = file_get_contents($fileurl); //加载配置文件
				
				if (!empty($app_debug)) {
					$string = preg_replace($pats, $reps, $string); // 正则查找然后替换
				} else if (!empty($log_type)) {
					$string = str_replace($pats, $reps, $string);
				}
				file_put_contents($fileurl, $string); // 写入配置文件 */
				return success();
			} else {
				return error();
			}
		}
		
		$config_model = new ConfigModel();
		$config_info = $config_model->getConfigInfo([ 'name' => 'SYSTEM_SITE_CONFIG' ]);
		$system_site_config = empty($config_info['data']['value']) ? [] : json_decode($config_info['data']['value'], true);
		$this->assign('system_site_config', $system_site_config);
		
		$this->assign('develop_status', config()["app_debug"]);//调试状态
		$this->assign('log_status', config()["log"]['type']);//日志状态
		return $this->fetch('config/site_config');
	}
	
	/**
	 * 修改站点设置信息
	 */
	public function setSystemConfig()
	{
		if (IS_AJAX) {
			$close_site_status = input('close_site_status', 0);
			$reasons_for_closing = input('reasons_for_closing', '');
			$title = input('title', '');//网站标题
			$keywords = input('keywords', '');//网站关键词
			$description = input('description', '');//网站描述
			$icp = input('icp', '');
			$police_icp_location = input('police_icp_location', '');
			$police_icp_code = input('police_icp_code', '');
			$json_array = [];
			$json_array['reasons_for_closing'] = $reasons_for_closing;
			$json_array['title'] = $title;
			$json_array['keywords'] = $keywords;
			$json_array['description'] = $description;
			$json_array['icp'] = $icp;
			$json_array['police_icp_location'] = $police_icp_location;
			$json_array['police_icp_code'] = $police_icp_code;
			$json_array['close_site_status'] = $close_site_status;
			$json_data = json_encode($json_array);
			$config_model = new ConfigModel();
			$data = array(
				"value" => $json_data,
				"name" => "SYSTEM_SITE_CONFIG",
				"remark" => "站点设置"
			);
			$result = $config_model->setConfig($data);
			if ($result === false) {
				return error($result);
			}
			return success($result);
		}
	}
	
	/**
	 * 公告列表
	 */
	public function notice()
	{
		$notice_model = new NoticeModel();
		if (IS_AJAX) {
			$page_index = input('page', 1);
			$page_size = input('limit', PAGE_LIST_ROWS);
			$condition = [];
			$notice_category_id = input('select_notice_category_id', "");
			if ($notice_category_id !== "") {
				$condition['notice_category_id'] = $notice_category_id;
			}
			$select_create_time = input('select_create_time', "");
			if ($select_create_time !== "") {
				$end_time = time();
				if ($select_create_time == 3) {
					$start_time = mktime(0, 0, 0, date('m'), date('d') - 3, date('Y'));
					$end_time = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
				} else if ($select_create_time == 7) {
					$start_time = mktime(0, 0, 0, date('m'), date('d') - 7, date('Y'));
				} else if ($select_create_time == 30) {
					$start_time = mktime(0, 0, 0, date('m'), date('d') - 30, date('Y'));
				} else if ($select_create_time == 90) {
					$start_time = mktime(0, 0, 0, date('m'), date('d') - 90, date('Y'));
				}
				$condition["create_time"] = [ "between", [ $start_time, $end_time ] ];
			}
			$order = input('order', "");
			if (empty($order)) {
				$order = "sort desc";
			}
			
			$title = input('title', "");
			if ($title !== "") {
				$condition['title'] = [ 'like', '%' . $title . '%' ];
			}
			
			$list = $notice_model->getNoticePageList($condition, $page_index, $page_size, $order);
			return $list;
		}
		$list = $notice_model->getNoticeCategoryList();
		$this->assign('category_list', $list['data']);
		return $this->fetch('config/notice');
	}
	
	/**
	 * 公告列表单页
	 */
	public function noticeList()
	{
		if (IS_AJAX) {
			$page_index = input('page', 1);
			$page_size = input('limit', PAGE_LIST_ROWS);
			$notice_model = new NoticeModel();
			$list = $notice_model->getNoticePageList([ 'is_display' => 1 ], $page_index, $page_size, "create_time desc");
			return $list;
		}
		return $this->fetch('config/notice_list');
	}
	
	/**
	 * 预览公告
	 */
	public function previewNotice()
	{
		$notice_model = new NoticeModel();
		$notice_id = input('notice_id', 0);
		$condition = [
			'notice_id' => $notice_id
		];
		$info = $notice_model->getNoticeInfo($condition);
		$this->assign('info', $info['data']);
		
		return $this->fetch('config/preview_notice');
	}
	
	/**
	 * 公告类型列表
	 */
	public function noticeCategoryList()
	{
		if (IS_AJAX) {
			$page_index = input('page', 1);
			$page_size = input('limit', PAGE_LIST_ROWS);
			$condition = [];
			
			$notice_category_id = input('notice_category_id', "");
			if ($notice_category_id !== "") {
				$condition['notice_category_id'] = $notice_category_id;
			}
			
			$title = input('title', "");
			if ($title !== "") {
				$condition['title'] = [ 'like', '%' . $title . '%' ];
			}
			$notice_model = new NoticeModel();
			$list = $notice_model->getNoticeCategorPageList($condition, $page_index, $page_size);
			return $list;
		}
		return $this->fetch('config/notice_category_list');
	}
	
	/**
	 * 删除公告
	 */
	public function deleteNotice()
	{
		if (IS_AJAX) {
			$notice_id = input('notice_id', 0);
			$notice_model = new NoticeModel();
			$res = $notice_model->deleteNotice($notice_id);
			return $res;
		}
	}
	
	/**
	 * 删除公告分类
	 */
	public function deleteNoticeCategory()
	{
		if (IS_AJAX) {
			$notice_category_id = input('notice_category_id', 0);
			$notice_model = new NoticeModel();
			$res = $notice_model->deleteNoticeCategory($notice_category_id);
			return $res;
		}
	}
	
	/**
	 * 编辑公告分类
	 */
	public function editCategoryTitle()
	{
		if (IS_AJAX) {
			$title = input('edittitle', "");
			$sort = input('editsort', 0);
			$notice_category_id = input('notice_category_id', 0);
			$date['title'] = $title;
			$date['sort'] = $sort;
			$condition['notice_category_id'] = $notice_category_id;
			$notice_model = new NoticeModel();
			$res = $notice_model->editCategoryTitle($date, $condition);
			return $res;
		}
	}
	
	/**
	 * 添加公告分类
	 */
	public function addNoticeCategory()
	{
		if (IS_AJAX) {
			$title = input('title', '');
			$sort = input('sort', 0);
			$notice_model = new NoticeModel();
			$data = array(
				"title" => $title,
				"sort" => $sort
			);
			$res = $notice_model->addNoticeCategory($data);
			return $res;
		}
	}
	
	/**
	 * 公告是否显示
	 */
	public function setIsDisplay()
	{
		$notice_id = input('notice_id', 0);
		$status = input('is_display', 0);
		$notice_model = new NoticeModel();
		$res = $notice_model->modifyNoticeIsDisplay($notice_id, $status);
		return $res;
	}
	
	/**
	 * 公告是否推荐   is_recommend
	 */
	public function setIsRecommend()
	{
		$notice_id = input('notice_id', 0);
		$status = input('is_recommend', 0);
		$notice_model = new NoticeModel();
		$res = $notice_model->modifyNoticeIsRecommend($notice_id, $status);
		return $res;
	}
	
	/**
	 * 添加公告
	 */
	public function addNotice()
	{
		$notice_model = new NoticeModel();
		if (IS_AJAX) {
			$data = input('data/a', '');//备案信息
			$data['is_display'] = empty($data['is_display']) ? 0 : 1;
			$data['is_recommend'] = empty($data['is_recommend']) ? 0 : 1;
			$data['create_time'] = time();
			$data['uid'] = UID;
			$res = $notice_model->addNotice($data);
			return $res;
		} else {
			$list = $notice_model->getNoticeCategoryList();
			$this->assign('category_list', $list['data']);
			return $this->fetch('config/edit_notice');
		}
	}
	
	/**
	 * 编辑公告
	 */
	public function editNotice()
	{
		$notice_model = new NoticeModel();
		if (IS_AJAX) {
			$data = input('data/a', '');//备案信息
			$notice_id = input('notice_id', 0);
			$data['is_display'] = empty($data['is_display']) ? 0 : 1;
			$data['is_recommend'] = empty($data['is_recommend']) ? 0 : 1;
			$data['update_time'] = time();
			$res = $notice_model->editNotice($data, [ 'notice_id' => $notice_id ]);
			return $res;
		} else {
			$notice_id = input('notice_id', 0);
			
			$info = $notice_model->getNoticeInfo([ 'notice_id' => $notice_id ]);
			$this->assign('info', $info['data']);
			
			$list = $notice_model->getNoticeCategoryList();
			$this->assign('category_list', $list['data']);
			
			return $this->fetch('config/edit_notice');
		}
	}
	
	/**
	 * 微信开放平台配置
	 */
	public function wechat()
	{
		$config_model = new ConfigModel();
		if (IS_AJAX) {
			$name = input('name', '');
			$data = [
				'value' => input('val'),
				'name' => "WECHAT_PLATFORM_CONFIG",
				'type' => 0,
				'title' => "微信开放平台",
				'remark' => "微信开放平台配置",
				'status' => 1,
			];
			if ($name) {
				$data['update_time'] = time();
				$res = $config_model->editConfig($data, [ 'name' => $name ]);
			} else {
				$data['create_time'] = time();
				$res = $config_model->addConfig($data);
			}
			return $res;
		}
		$wechat_info = $config_model->getConfigInfo([ 'name' => 'WECHAT_PLATFORM_CONFIG' ]);
		$wechat_info['data']['value'] = json_decode($wechat_info['data']['value'], true);
		$this->assign('wechat_info', $wechat_info['data']);
		$host = request()->host();
		$root = request()->root();
		$data_url = array(
			'host' => $host,
			'auth_msg_url' => $host . '/wechat/wap/config/index',
			'msg_url' => $host . '/wechat/wap/config/getPlatformMessage/$APPID$/',
			'open_url' => $host
		);
		$this->assign("data_url", $data_url);
		return $this->fetch('config/wechat');
	}
	
	public function version()
	{
		$config_model = new ConfigModel();
		if (IS_AJAX) {
			$name = input('name', '');
			$val = input('val', '');
			$data = [
				'value' => $val,
				'name' => "SYSTEM_AUTH_CONFIG",
				'type' => 0,
				'title' => "系统授权",
				'remark' => "系统授权配置",
				'status' => 1,
			];
			
			//检测是否一致
			//             $value = json_decode($val, true);
			//             $AppId = $value['app_key'];
			//             $Appsecret = $value['app_secret'];
			//             $res = $config_model->getIsSame($AppId, $AppId);
			
			if ($name) {
				$data['update_time'] = time();
				$res = $config_model->editConfig($data, [ 'name' => $name ]);
			} else {
				$data['create_time'] = time();
				$res = $config_model->addConfig($data);
			}
			return $res;
		}
		$auth_info = $config_model->getConfigInfo([ 'name' => 'SYSTEM_AUTH_CONFIG' ]);
		$auth_info['data']['value'] = json_decode($auth_info['data']['value'], true);
		$this->assign('auth_info', $auth_info['data']);
		
		$request = Request::instance();
		$domain = $request->domain();
		$this->assign('domain', $domain);
		return $this->fetch('config/version');
	}
	
	/**
	 * 获取授权列表
	 */
	public function getAuthList()
	{
		
		$config_model = new ConfigModel();
		$auth_info = $config_model->getConfigInfo([ 'name' => 'SYSTEM_AUTH_CONFIG' ]);
		$app_config = json_decode($auth_info['data']['value'], true);
		$app_key = $app_config['app_key'];
		$app_secret = $app_config['app_secret'];
		$domain = $app_config['domain'];
		$http = new WebClient($app_key, $app_secret);
		$list = $http->post('Auth.authList', [ 'page' => 1, 'limit' => 1000, 'domain' => $domain ]);
		return $list;
	}
	
	/**
	 * 获取系统信息
	 */
	public function getSystemConfig()
	{
		$system_config['os'] = php_uname(); // 服务器操作系统
		$system_config['server_software'] = $_SERVER['SERVER_SOFTWARE']; // 服务器环境
		$system_config['upload_max_filesize'] = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknow'; // 文件上传限制
		$system_config['gd_version'] = gd_info()['GD Version']; // GD（图形处理）版本
		$system_config['max_execution_time'] = ini_get("max_execution_time") . "秒"; // 最大执行时间
		$system_config['port'] = $_SERVER['SERVER_PORT']; // 端口
		$system_config['dns'] = $_SERVER['HTTP_HOST']; // 服务器域名
		$system_config['php_version'] = PHP_VERSION; // php版本
		$system_config['sockets'] = extension_loaded('sockets'); //是否支付sockets
		$system_config['openssl'] = extension_loaded('openssl'); //是否支付openssl
		$system_config['curl'] = function_exists('curl_init'); // 是否支持curl功能
		/*$system_config['upload_dir_jurisdiction'] = check_dir_iswritable(ROOT_PATH . "upload/"); // upload目录读写权限
		 $system_config['runtime_dir_jurisdiction'] = check_dir_iswritable(ROOT_PATH . "runtime/"); // runtime目录读写权限  */
		$system_config['fileinfo'] = extension_loaded('fileinfo'); //是否开启fileinfo
		$system_config['sql_version'] = mysqli_get_server_info(mysqli_connect(config("database")['hostname'], config("database")['username'], config("database")['password'], config("database")['database'])); //sql 版本 */
		$config = new ConfigModel();
		
		$system_config['db_size'] = $config->getDatabaseSize()['data']; //sql使用内存
		$request = Request::instance();
		
		$system_config['annex_root_catalogue'] = $request->root(true); //root
		$this->assign("system_config", $system_config);
		$this->assign('niu_version', SYS_VERSION_NO);
		$this->assign('niu_version_name', SYS_VERSION_NAME);
	}
	
	/**
	 * 附件尺寸
	 */
	public function annexSize()
	{
		$path = __UPLOAD__;
		$size = getDirSize($path);
		if (empty($size)) {
			$size = '';
		} else {
			$size = sizeConversion($size);
		}
		return success($size);
	}
	
	/**
	 * 短信管理
	 */
	public function sms()
	{
		return $this->fetch('config/sms');
	}
	
}