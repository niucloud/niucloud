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
use app\common\model\Config;
use think\Session;
use util\database\Database;
use think\Request;
use util\api\WebClient;
use think\console\command\optimize\Autoload;
use think\console\command\optimize\Schema;
use think\console\Input;
use think\console\Output;
use app\common\model\Visit;
use app\common\model\Upgrade;
use app\common\model\Addon;
use util\database\DbCompare;
use think\Db;

/**
 * 系统  控制器
 */
class System extends BaseAdmin
{
	public $app_key = '';
	public $app_secret = '';
	public $domain = '';
	
	public function auth()
	{
		$config_model = new Config();
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
		$this->assign('sys_version_no', SYS_VERSION_NO);
		return $this->fetch();
	}
	/**
	 * 检测升级
	 */
	public function upgrade(){
	    $cloudip = gethostbyname('localhost');
	    if (empty($cloudip) || !preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $cloudip)) {
	        $this->error("云服务器检测失败");
	    }
	    $config_model = new Config();
	    $auth_info = $config_model->getConfigInfo([ 'name' => 'SYSTEM_AUTH_CONFIG' ]);
	    $app_config = json_decode($auth_info['data']['value'], true);
	    $this->app_key = $app_config['app_key'];
	    $this->app_secret = $app_config['app_secret'];
	    $this->domain = $app_config['domain'];
	    $http = new WebClient($this->app_key, $this->app_secret);
	    $data = [
	        'domain' => $this->domain,
	        'sys_version' => SYS_VERSION,
	        'sys_version_no' => SYS_VERSION_NO,
	        'sys_release' => SYS_RELEASE
	    ];
	    $info = $http->post('Upgrade.getUpgradeInfo', $data);
	    $info = json_decode($info, true);
	    //文件重置
	    foreach ($info['data']['files'] as $k => $v)
	    {
	        if(file_exists('data/upgrade/'.$info['data']['sys_version'].'/'.$info['data']['sys_release'].'/release/'.$v))
	        {
	            unset($info['data']['files'][$k]);
	        }
	    }
	    if(!empty($info['data']['files']))
	    {
	        sort($info['data']['files']);
	    }
	    if(file_exists('data/upgrade/'.$info['data']['sys_version'].'/'.$info['data']['sys_release'].'/database.sql'))
	    {
	        unlink('data/upgrade/'.$info['data']['sys_version'].'/'.$info['data']['sys_release'].'/database.sql');
	    }
	    //生成升级sql文件
	    $dir_make = dir_mkdir('data/upgrade/'.$info['data']['sys_version'].'/'.$info['data']['sys_release']);
	    if($dir_make)
	    {
	            $res = file_put_contents('data/upgrade/'.$info['data']['sys_version'].'/'.$info['data']['sys_release'].'/db_upgrade.sql', $info['data']['sqls']);

	    }else{
	        $this->error("文件读写权限不足");
	    }
	    Session::set("version_update", $info['data']);
		$session = Session::get("version_update");
	    if(!empty($session))
	    {
	        
            $session['script_count'] = count($session['scripts']);
            $session['file_count'] = count($session['files']);
	        $this->assign('version', $session);
	        return $this->fetch("system/upgrade");
	    }else{
	        $this->error("云服务器检测失败");
	    }
	}
	
	/**
	 * 下载系统文件
	 */
	public function download(){
	    
	    $index = input("index", 0);
	    $session = Session::get("version_update");
	    if(!empty($session))
	    {
	        $config_model = new Config();
	        $auth_info = $config_model->getConfigInfo([ 'name' => 'SYSTEM_AUTH_CONFIG' ]);
	        $app_config = json_decode($auth_info['data']['value'], true);
	        $this->app_key = $app_config['app_key'];
	        $this->app_secret = $app_config['app_secret'];
	        $this->domain = $app_config['domain'];
	        $file = isset($session['files'][$index]) ? $session['files'][$index] : '';
	        if(empty($file))
	        {
	            return error("升级文件不存在");
	        }
	        $data = [
	            'token' => $session['token'],
	            'file'  => $file
	        ];
	        $file_path = dirname($file);
	        $http = new WebClient($this->app_key, $this->app_secret);
	        $info = $http->post('Upgrade.download', $data);  
	        $info = json_decode($info, true);
	        $info = base64_decode($info['data']);
	        $dir_make = dir_mkdir('data/upgrade/'.$session['sys_version'].'/'.$session['sys_release'].'/release/'.$file_path);
	        if($dir_make)
	        {
	            if(!empty($info))
	            {
	                $res = file_put_contents('data/upgrade/'.$session['sys_version'].'/'.$session['sys_release'].'/release/'.$file, $info);
	                if($res)
	                {
	                    return success($file);
	                }else{
	                    return error($file);
	                }
	            }else{
	                return error("升级文件不存在");
	            }
	           
	        }else{
	            return error("文件读写权限不足");
	        }

	    }else{
	        return error("升级权限不足");
	    }
	}
	
	/**
	 * 文件下载完毕，执行升级过程
	 */
	public function execute(){
	    $session = Session::get("version_update");
	    if(!empty($session))
	    {
	        //检测文件完整性
	        foreach ($session['files'] as $k => $v)
	        {
	            if(!file_exists('data/upgrade/'.$session['sys_version'].'/'.$session['sys_release'].'/release/'.$v))
	            {
	                $this->error("检测文件不完整");
	            }
	        }
	        dir_mkdir('data/backup/'.$session['sys_version'].'/'.$session['sys_release'].'/release');
	        //文件备份
	        dir_copy('data/upgrade/'.$session['sys_version'].'/'.$session['sys_release'].'/release', 'data/backup/'.$session['sys_version'].'/'.$session['sys_release'].'/release');
	        //数据库备份
	        $config = array(
	            'path' => 'data/backup/'.$session['sys_version'].'/'.$session['sys_release'].'/',
	            'part' => 20971520,
	            'compress' => 0,
	            'level' => 9
	        );
	        $file = array(
	            'name' => 'backup',
	            'part' => 1
	        );
	        $database = new Database($file, $config);
	        $tables = $database->getTableList();
	        foreach ($tables['data']['list'] as $k => $v)
	        {
	            $database->backup($v['Name'], 0);
	        }
	        //文件替换
	        dir_copy('data/upgrade/'.$session['sys_version'].'/'.$session['sys_release'].'/release', './');
	        //执行数据库
	        $sql = file_get_contents('data/upgrade/'.$session['sys_version'].'/'.$session['sys_release'].'/db_upgrade.sql');
	        $sql_arr = parseSql($sql);
	        foreach($sql_arr as $k => $v){
	            if(!empty($v))
	                Db::execute($v);
	        }
	        
	        exit();
	        
	    }
	}
	/**
	 * 获取授权列表
	 */
	public function getAuthList()
	{
		
		$config_model = new Config();
		$auth_info = $config_model->getConfigInfo([ 'name' => 'SYSTEM_AUTH_CONFIG' ]);
		$app_config = json_decode($auth_info['data']['value'], true);
		$this->app_key = $app_config['app_key'];
		$this->app_secret = $app_config['app_secret'];
		$this->domain = $app_config['domain'];
		$http = new WebClient($this->app_key, $this->app_secret);
		$list = $http->post('Auth.authList', [ 'page' => 1, 'limit' => 1000, 'domain' => $this->domain ]);
		return $list;
	}
	
	/**
	 * 缓存管理
	 */
	public function cache()
	{
		return $this->fetch('system/cache');
	}
	
	/**
	 * 表结构缓存(关闭调试模式)
	 */
	public function tableCache()
	{
		if (IS_AJAX) {
			ini_set('max_execution_time', 120);
			$schema = new Schema();
			$input = new Input('optimize:schema');
			$output = new Output();
			$res = $schema->doExecute($input, $output);
			if ($res == 'succeed') {
				return success();
			} else {
				return error();
			}
		}
	}
	
	/**
	 * 命名空间缓存
	 */
	public function classCache()
	{
		if (IS_AJAX) {
			ini_set('max_execution_time', 120);
			$autoload = new Autoload();
			$input = new Input('optimize:autoload');
			$output = new Output();
			$res = $autoload->doExecute($input, $output);
			if ($res == 'succeed') {
				return success();
			} else {
				return error();
			}
		}
	}
	
	/**
	 * 数据库管理
	 * @return mixed
	 */
	public function database()
	{
		if (IS_AJAX) {
			$database = new Database();
			$data = $database->getTableList();
			$data['data']['count'] = !empty($data['data']['list']) ? count($data['data']['list']) : 0;
			return $data;
		}
		return $this->fetch('system/database');
	}
	
	/**
	 * 修复表
	 */
	public function repairTable()
	{
		if (IS_AJAX) {
			$tables = input('table', '');
			$database = new Database();
			$res = $database->repair(explode(',', $tables));
			return $res;
		}
	}
	
	/**
	 * 表备份
	 */
	public function backupTable()
	{
		if (IS_AJAX) {
			$tables = input('table', '');
			$id = input('id', '');
			$begin = input('start', '');

			if (!empty($tables)) {
                $table_list = explode(',', $tables);
					$config = array(
						'path' => ROOT_PATH . DS . 'attachment' . DS . 'db_backup' . DS,
						'part' => 20971520,
						'compress' => 1,
						'level' => 9
					);
					// 检查是否有正在执行的任务
					$lock = "{$config['path']}backup.lock";
					if (is_file($lock)) {
						return error('', "检测到有一个备份任务正在执行，请稍后再试！");
					} else {
						$mode = intval('0777', 8);
						if (!file_exists($config['path']) || !is_dir($config['path']))
							mkdir($config['path'], $mode, true); // 创建锁文件
						file_put_contents($lock, date('Ymd-His', time()));
					}
					// 自动创建备份文件夹
					// 检查备份目录是否可写
					is_writeable($config['path']) || exit('backup_not_exist_success');
					Session::set('backup_config', $config);
					// 生成备份文件信息
					$file = array(
						'name' => date('Ymd-His', time()),
						'part' => 1
					);
                    Session::set('backup_file', $file);
					// 缓存要备份的表
                    Session::set('backup_tables', $table_list);
					
					$database = new database($file, $config);
					if (false !== $database->create()) {
                        $data = array();
                        $data['status'] = 1;
                        $data['tables'] = $tables;
                        $data['tab'] = array(
                            'id' => 0,
                            'start' => 0
                        );
                        $data['message'] = '初始化成功！';
						return success($data);
					} else {
						return error('','初始化失败，备份文件创建失败！');
					}
            } elseif(is_numeric($id) && is_numeric($begin)){
					$tables = Session::get('backup_tables');
                    $database = new database(Session::get('backup_file'), Session::get('backup_config'));
                    $start = $database->backup($tables[ $id ], $begin);
                    if($start === false)
                        return error([], "备份出错!");

                    if($start == 0){
                        //判断表当前表是否存在
                        if(isset($tables[ ++$id ])){
                            $tab = array(
                                'id' => $id,
                                'table' => $tables[ $id ],
                                'start' => 0
                            );
                            $data = array();
                            $data['rate'] = 100;
                            $data['status'] = 1;
                            $data['message'] = "表{$tables[ $id ]}备份完成！";
                            $data['tab'] = $tab;
                        }else{
                            $config = Session::get('backup_config');
                            unlink($config['path'] . 'backup.lock');
                            Session::set('backup_tables', null);
                            Session::set('backup_file', null);
                            Session::set('backup_config', null);
                            $data = [
                                'id' => $id,
                                "status" => 2,
                                'count' => count($tables),
                                'message' => '备份成功！'
                            ];
                        }
                        return success($data);
                    }else{
                        //同表继续备份
                        $tab = array(
                            'id' => $id,
                            'table' => $tables[ $id ],
                            'start' => $start[0]
                        );
                        $rate = floor(100 * ($start[0] / $start[1]));
                        $data = array();
                        $data['status'] = 1;
                        $data['rate'] = $rate;
                        $data['tab'] = $tab;
                        $data["message"] = "正在备份表{$tables[ $id ]}...进度({$rate}%)";
                        return success($data);
                    }
			}else{
			    return error('', "参数有误!");
            }
		}
	}
	
	/**
	 * 备份列表
	 */
	public function backupList()
	{
		if (IS_AJAX) {
			$backup_path = ROOT_PATH . DS . 'attachment' . DS . 'Db_backup' . DS;
			$res = error();
			$res['data'] = [
				'count' => 0,
				'list' => []
			];
			if (is_dir($backup_path)) {
				$list = [];
				$flag = \FilesystemIterator::KEY_AS_FILENAME;
				$glob = new \FilesystemIterator($backup_path, $flag);
				
				foreach ($glob as $name => $file) {
					if ($file->getExtension() == 'gz') {
						$info = [
							'name' => $file->getFileName(),
							'size' => $file->getSize(),
							'create_time' => $file->getMTime(),
							'ext' => $file->getExtension(),
							'path' => $file->getRealPath()
						];
						array_push($list, $info);
					}
				}
				$res = success();
				$res['data'] = [
					'count' => !empty($list) ? count($list) : 0,
					'list' => $list
				];
			}
			return $res;
		} else {
			return $this->fetch('system/backup_list');
		}
	}
	
	/**
	 * 恢复备份
	 */
	public function recoveryBackup()
	{
		if (IS_AJAX) {
			$name = input('name', '');
			$part = request()->post('part', 0);
			$start = request()->post('start', 0);
			$backup_path = ROOT_PATH . DS . 'attachment' . DS . 'Db_backup' . DS . $name;
			if (file_exists($backup_path)) {
				
				if ((is_null($part) || empty($part)) && (is_null($start) || empty($start))) {
					$files = glob($backup_path);
					$list = array();
					foreach ($files as $name) {
						$basename = basename($name);
						$match = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
						$match[6] = isset($match[6]) ? $match[6] : 1;
						$gz = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
						$list[ $match[6] ] = array(
							$match[6],
							
							
							$name,
							$gz
						);
					}
					ksort($list);
					// 检测文件正确性
					$last = end($list);
					session('backup_list', $list); // 缓存备份列表
					return success([ 'part' => 1, 'start' => 0, 'message' => '初始化完成' ]);
				} else {
					$list = session('backup_list');
					$db = new database($list[ $part ], array(
						'path' => realpath($this->backup_path) . DIRECTORY_SEPARATOR,
						'compress' => 1
					));
					
					$start = $db->import($start);
					if ($start === false) {
						return error([ 'message' => '还原数据出错！' ]);
					} elseif ($start === 0) { // 下一卷
						if (isset($list[ ++$part ])) {
							$data = array(
								'part' => $part,
								'start' => 0
							);
							return success($data);
						} else {
							session('backup_list', null);
							return success([ 'message' => '还原完成！' ]);
						}
					} else {
						$data = array(
							'part' => $part,
							'start' => $start[0]
						);
						if ($start[1]) {
							$rate = floor(100 * ($start[0] / $start[1]));
							return success($data);
						} else {
							$data['gz'] = 1;
							return success($data);
						}
					}
				}
			} else {
				return error([ 'message' => '备份文件不存在或者已经损坏，请检查！' ]);
			}
		}
	}
	
	/**
	 * 删除备份
	 */
	public function delBackup()
	{
		if (IS_AJAX) {
			$name = input('name', '');
			$backup_path = ROOT_PATH . DS . 'attachment' . DS . 'Db_backup' . DS . $name;
			if (file_exists($backup_path)) {
				$res = unlink($backup_path);
				if ($res) return success();
				else return error();
			}
		}
	}
	
	
	/**
	 * 首页统计
	 */
	public function visitIndex(){
	    return $this->fetch('system/visit_index');
	}

    /**
     * 访问统计图表数据
     */
	public function getVisitStatisticsData(){
	    $type = input("type", "ALL");
        $date_type = input("date_type", "daterange");//日期选择类型
        $daterange = input("daterange", "");
        $visit_model = new Visit();
        if(!empty($daterange)){
            $daterange_array = explode(" - ", $daterange);
            $start_date = date_format(date_create($daterange_array[0]), "Ymd");
            $end_date = date_format(date_create($daterange_array[1]), "Ymd");
        }else{
            return success([]);
        }
        $condition = array(
            "type" => $type,
            "date_type" => $date_type,
            "date_range" => ["start_date" => $start_date, "end_date" => $end_date]
        );
        $data = $visit_model->getVisitStatisticsData($condition);
        return $data;
    }
    
    /**
     * 快速创建插件
     */
    public function build(){
        $addon = new Addon();
        //$addon->build($data);
        return $this->fetch('system/build');
        
    }
}