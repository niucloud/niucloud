<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
// 去除警告错误
error_reporting(E_ALL ^ E_NOTICE);

use util\QRcode as QRcode;
use app\common\model\Config;
use util\api\SignClient;
use app\common\model\Site;
use app\common\model\Addon;
use think\Cookie;
use think\Session;
use think\Cache;

/*****************************************************基础函数*********************************************************/
/**
 * 把返回的数据集转换成Tree
 *
 * @param array $list
 *            要转换的数据集
 * @param string $pid
 *            parent标记字段
 * @param string $level
 *            level标记字段
 * @return array
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{
	// 创建Tree
	$tree = [];
	if (!is_array($list)) :
		return false;
	
	endif;
	// 创建基于主键的数组引用
	$refer = [];
	foreach ($list as $key => $data) {
		$refer[ $data[ $pk ] ] = &$list[ $key ];
	}
	foreach ($list as $key => $data) {
		// 判断是否存在parent
		$parentId = $data[ $pid ];
		if ($root == $parentId) {
			$tree[] = &$list[ $key ];
		} else if (isset($refer[ $parentId ])) {
			is_object($refer[ $parentId ]) && $refer[ $parentId ] = $refer[ $parentId ]->toArray();
			$parent = &$refer[ $parentId ];
			$parent[ $child ][] = &$list[ $key ];
		}
	}
	return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 *
 * @param array $tree
 *            原来的树
 * @param string $child
 *            孩子节点的键
 * @param string $order
 *            排序显示的键，一般是主键 升序排列
 * @param array $list
 *            过渡用的中间数组，
 * @return array 返回排过序的列表数组
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array())
{
	if (is_array($tree)) {
		foreach ($tree as $key => $value) {
			$reffer = $value;
			if (isset($reffer[ $child ])) {
				unset($reffer[ $child ]);
				tree_to_list($value[ $child ], $child, $order, $list);
			}
			$list[] = $reffer;
		}
		$list = list_sort_by($list, $order, $sortby = 'asc');
	}
	return $list;
}

/**
 * 对查询结果集进行排序
 *
 * @access public
 * @param array $list
 *            查询结果
 * @param string $field
 *            排序的字段名
 * @param array $sortby
 *            排序类型
 *            asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
	if (is_array($list)) {
		$refer = $resultSet = array();
		foreach ($list as $i => $data)
			$refer[ $i ] = &$data[ $field ];
		switch ($sortby) {
			case 'asc': // 正向排序
				asort($refer);
				break;
			case 'desc': // 逆向排序
				arsort($refer);
				break;
			case 'nat': // 自然排序
				natcasesort($refer);
				break;
		}
		foreach ($refer as $key => $val)
			$resultSet[] = &$list[ $key ];
		return $resultSet;
	}
	return false;
}

/**
 * 对象转化为数组
 * @param object $obj
 */
function object_to_array($obj)
{
	if (is_object($obj)) {
		$obj = (array) $obj;
	}
	if (is_array($obj)) {
		foreach ($obj as $key => $value) {
			$obj[ $key ] = object_to_array($value);
		}
	}
	return $obj;
}

/**
 * 系统加密方法
 *
 * @param string $data
 *            要加密的字符串
 * @param string $key
 *            加密密钥
 * @param int $expire
 *            过期时间 单位 秒
 * @return string
 */
function encrypt($data, $key = '', $expire = 0)
{
	$key = md5(empty ($key) ? 'niucloud' : $key);
	
	$data = base64_encode($data);
	$x = 0;
	$len = strlen($data);
	$l = strlen($key);
	$char = '';
	
	for ($i = 0; $i < $len; $i++) {
		if ($x == $l)
			$x = 0;
		$char .= substr($key, $x, 1);
		$x++;
	}
	
	$str = sprintf('%010d', $expire ? $expire + time() : 0);
	
	for ($i = 0; $i < $len; $i++) {
		$str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
	}
	return str_replace(array(
		'+',
		'/',
		'='
	), array(
		'-',
		'_',
		''
	), base64_encode($str));
}

/**
 * 系统解密方法
 *
 * @param string $data
 *            要解密的字符串 （必须是encrypt方法加密的字符串）
 * @param string $key
 *            加密密钥
 * @return string
 */
function decrypt($data, $key = '')
{
	$key = md5(empty ($key) ? 'niucloud' : $key);
	$data = str_replace(array(
		'-',
		'_'
	), array(
		'+',
		'/'
	), $data);
	$mod4 = strlen($data) % 4;
	if ($mod4) {
		$data .= substr('====', $mod4);
	}
	$data = base64_decode($data);
	$expire = substr($data, 0, 10);
	$data = substr($data, 10);
	
	if ($expire > 0 && $expire < time()) {
		return '';
	}
	$x = 0;
	$len = strlen($data);
	$l = strlen($key);
	$char = $str = '';
	
	for ($i = 0; $i < $len; $i++) {
		if ($x == $l)
			$x = 0;
		$char .= substr($key, $x, 1);
		$x++;
	}
	
	for ($i = 0; $i < $len; $i++) {
		if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
			$str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
		} else {
			$str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
		}
	}
	return base64_decode($str);
}

/**
 * 数据签名认证
 */
function data_auth_sign($data)
{
	// 数据类型检测
	if (!is_array($data)) {
		$data = (array) $data;
	}
	ksort($data); // 排序
	$code = http_build_query($data); // url编码并生成query字符串
	$sign = sha1($code); // 生成签名
	return $sign;
}

/**
 * 重写md5加密方式
 *
 * @param unknown $str
 * @return string
 */
function data_md5($str)
{
	return '' === $str ? '' : md5(md5($str) . 'NiuCloud');
}

/**
 * 时间戳转时间
 */
function time_to_date($time_stamp, $format = 'Y-m-d H:i:s')
{
	if ($time_stamp > 0) {
		$time = date($format, $time_stamp);
	} else {
		$time = "";
	}
	return $time;
}

/**
 * 时间转时间戳
 */
function date_to_time($date)
{
	$time_stamp = strtotime($date);
	return $time_stamp;
}

/**
 * 获取唯一随机字符串
 * 创建时间：2018年8月7日15:54:16
 */
function unique_random($len = 10)
{
	$str = 'qwertyuiopasdfghjklzxcvbnm';
	str_shuffle($str);
	$res = 'nc_' . substr(str_shuffle($str), 0, $len) . date('is');
	return $res;
}

/**
 * 生成随机数
 * @param int $length
 * @return string
 */
function random_keys($length)
{
	$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
	$key = '';
	for ($i = 0; $i < $length; $i++) {
		$key .= $pattern{mt_rand(0, 35)};    //生成php随机数
	}
	return $key;
}


/**
 * 发送HTTP请求方法，目前只支持CURL发送请求
 *
 * @param string $url
 *            请求URL
 * @param array $params
 *            请求参数
 * @param string $method
 *            请求方法GET/POST
 * @return array $data 响应数据
 */
function http($url, $timeout = 30, $header = array())
{
	if (!function_exists('curl_init')) {
		throw new Exception('server not install curl');
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	if (!empty($header)) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}
	$data = curl_exec($ch);
	list ($header, $data) = explode("\r\n\r\n", $data);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if ($http_code == 301 || $http_code == 302) {
		$matches = array();
		preg_match('/Location:(.*?)\n/', $header, $matches);
		$url = trim(array_pop($matches));
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$data = curl_exec($ch);
	}
	
	if ($data == false) {
		curl_close($ch);
	}
	@curl_close($ch);
	return $data;
}


/**
 * curl get请求
 * @param unknown $url
 * @return mixed
 */
function curl_get($url)
{
	$curl = curl_init();
	
	curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	
	$result = curl_exec($curl);
	curl_close($curl);
	
	return $result;
}

/**
 * 导出Excel
 * 创建时间：2018年9月28日17:08:31 xxs
 * @param $title    文件名
 * @param $cell_name    列表
 * @param $data 数据源
 * @throws PHPExcel_Exception
 * @throws PHPExcel_Reader_Exception
 * @throws PHPExcel_Writer_Exception
 */
function export_excel($title, $cell_name, $data)
{
	vendor("php_excel_classes.PHPExcel");
	$objPHPExcel = new \PHPExcel();
	$xlsTitle = iconv('utf-8', 'gb2312', $title); // 文件名称
	$fileName = $title . date('_YmdHis'); // or $xlsTitle 文件名称可根据自己情况设定
	$cellNum = count($cell_name);
	$dataNum = count($data);
	$cellName = array( 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ' );
	//设置标题
	for ($i = 0; $i < $cellNum; $i++) {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[ $i ] . '1', $cell_name[ $i ][1]);
	}
	for ($i = 0; $i < $dataNum; $i++) {
		for ($j = 0; $j < $cellNum; $j++) {
			$objPHPExcel->getActiveSheet(0)->setCellValue($cellName[ $j ] . ($i + 2), " " . $data[ $i ][ $cell_name[ $j ][0] ]);
		}
	}
	$objPHPExcel->setActiveSheetIndex(0);
	ob_end_clean();//清除缓冲区,避免乱码
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	
}

/**
 * 替换数组元素
 * @param array $array 数组
 * @param array $replace 替换元素['key' => 'value', 'key' => 'value']
 */
function replace_array_element($array, $replace)
{
	foreach ($replace as $k => $v) {
		if ($v == "unset" || $v == "") {
			unset($array[ $k ]);
		} else {
			$array[ $k ] = $v;
		}
	}
	
	return $array;
}


/**
 * 过滤特殊符号
 * 创建时间：2018年1月30日15:39:32
 * @param unknown $string
 * @return mixed
 */
function ihtmlspecialchars($string)
{
	if (is_array($string)) {
		foreach ($string as $key => $val) {
			$string[ $key ] = ihtmlspecialchars($val);
		}
	} else {
		$string = preg_replace('/&amp;((#(d{3,5}|x[a-fa-f0-9]{4})|[a-za-z][a-z0-9]{2,5});)/', '&\1',
			str_replace(array( '&', '"', '<', '>' ), array( '&amp;', '&quot;', '&lt;', '&gt;' ), $string));
	}
	return $string;
}

/**
 * 用户名、邮箱、手机号掩饰
 * @param unknown $str
 */
function hide_str($str)
{
	if (strpos($str, '@')) {
		$email_array = explode("@", $str);
		$prevfix = (strlen($email_array[0]) < 4) ? "" : substr($str, 0, 3);
		$count = 0;
		$str = preg_replace('/([\d\w+_-]{0,100})@/', '*****@', $str, -1, $count);
		$res = $prevfix . $str;
	} else {
		$pattern = '/^(13[0-9]|14[579]|15[0-3,5-9]|16[6]|17[0135678]|18[0-9]|19[89])\d{8}$/';
		if (preg_match($pattern, $str)) {
			$res = substr_replace($str, '****', 3, 4);
		} else {
			if (preg_match("/[\x{4e00}-\x{9fa5}]+/u", $str)) {
				$len = mb_strlen($str, 'UTF-8');
				if ($len >= 3) {
					$res = mb_substr($str, 0, 1, 'UTF-8') . '******' . mb_substr($str, -1, 1, 'UTF-8');
				} elseif ($len == 2) {
					$res = mb_substr($str, 0, 1, 'UTF-8') . '******';
				}
			} else {
				$len = strlen($str);
				if ($len >= 3) {
					$res = substr($str, 0, 1) . '******' . substr($str, -1);
				} elseif ($len == 2) {
					$res = substr($str, 0, 1) . '******';
				}
			}
		}
	}
	return $res;
}

/********************************************* 插件,站点相关函数 ************************************************************************************
 *
 * /**
 * 获取插件的类名
 *
 * @param string $name 当前类名的命名空间
 * @return  string
 */
function get_addon_class($name)
{
	$addon_model = new Addon();
	$addons = $addon_model->getAddons();
	$addon_class_array = $addons['addon_class'];
	if (isset($addon_class_array[ $name ])) {
		return $addon_class_array[ $name ];
	}
	$class = "addon\\system\\{$name}\\{$name}Addon";
	if (!class_exists($class)) {
		$class = "addon\\app\\{$name}\\{$name}Addon";
		if (!class_exists($class)) {
			$class = "addon\\module\\{$name}\\{$name}Addon";
		}
	}
	return $class;
}

/**
 * 插件显示内容里生成访问插件的url
 *
 * @param string $url
 *            url
 * @param array $param
 *            参数
 *            格式：addon_url('HelloWorld://sitehome/Game/index', [])
 */
function addon_url($url, $param = array())
{
	if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
		return $url;
	}
	$parse_url = parse_url($url);
	$addon = isset($parse_url['scheme']) ? $parse_url['scheme'] : '';
	$controller = isset($parse_url['host']) ? $parse_url['host'] : '';
	$action = trim($parse_url['path'], '/');
	/* 解析URL带的参数 */
	if (isset($parse_url['query'])) {
		parse_str($parse_url['query'], $query);
		$param = array_merge($query, $param);
	}
	/* 	if (($controller == 'web' || $controller == 'wap') && (strtolower(request()->siteAddon()) == $addon)) {
			$url = $controller . '/' . $action;
		} else {
			$url = $addon . '/' . $controller . '/' . $action;
		} */
	$url = $addon . '/' . $controller . '/' . $action;
	return url($url, $param);
}


/**
 * 给链接后添加get参数
 * @param $url
 * @param $params
 */
function append_url_params($url, $params)
{
	if (!empty($params)) {
		$url_params = "";
		foreach ($params as $k => $v) {
			$url_params .= "&" . $k . "=" . $v;
		}
		if (!strstr($url, '?')) {
			$url_params = preg_replace('/&/', '?', $url_params, 1);
		}
		$url = $url . $url_params;
	}
	return $url;
	
}

/**
 * 解析url的插件，模块，控制器，方法
 * @param unknown $url
 */
function url_action($url)
{
	if (empty($url)) {
		return [
			'addon' => '',
			'model' => 'index',
			'controller' => 'index',
			'action' => 'index'
		];
	}
	if (!strstr($url, '://')) {
		$url_array = explode('/', $url);
		return [
			'addon' => '',
			'model' => $url_array[0],
			'controller' => $url_array[1],
			'action' => $url_array[2]
		];
	} else {
		
		$url_addon_array = explode('://', $url);
		$addon = $url_addon_array[0];
		$url_array = explode('/', $url_addon_array[1]);
		return [
			'addon' => $addon,
			'model' => $url_array[0],
			'controller' => $url_array[1],
			'action' => $url_array[2]
		];
	}
	
}

/**
 * 初始化插件在某站点下的数据
 *
 * @param integer $site_id
 * @param string $addon_name
 */
function init_addon($site_id, $addon_name)
{
	$class = get_addon_class($addon_name);
	if (class_exists($class)) {
		$class_obj = new $class();
		$res = $class_obj->addToSite($site_id);
		$class_obj->initSiteDiyViewData($site_id);
		return $res;
	}
	return success();
}

/**
 * 删除插件在某站点下的数据
 *
 * @param integer $site_id
 * @param string $addon_name
 * @return boolean
 */
function del_addon($site_id, $addon_name)
{
	$class = get_addon_class($addon_name);
	if (class_exists($class)) {
		$class_obj = new $class();
		$bool = $class_obj->delFromSite($site_id);
		if (!$bool) {
			return false;
		}
	}
	return true;
}

/**
 * 复制插件在某站点下的数据到 新的站点中
 *
 * @param integer $site_id
 * @param integer $new_site_id
 * @param string $addon_name
 * @return boolean
 */
function copy_addon($site_id, $new_site_id, $addon_name)
{
	$class = get_addon_class($addon_name);
	if (class_exists($class)) {
		$class_obj = new $class();
		$bool = $class_obj->copyToSite($site_id, $new_site_id);
		if (!$bool) {
			return false;
		}
	}
	return true;
}

/**
 * 生成站点默认key
 */
function get_site_app_key()
{
	return 'nc' . substr(md5(time() . rand(1111, 9999)), 8, 16);
}

/**
 * 生成站点默认秘钥
 */
function get_site_app_secret()
{
	return md5(time() . rand(1111, 9999));
}

/***************************************************niucloud系统函数***************************************************/
/**
 * 处理插件钩子
 *
 * @param string $hook
 *            钩子名称
 * @param mixed $params
 *            传入参数
 * @param mixed $extra
 *            额外参数
 * @param bool $once
 *            只获取一个有效返回值
 * @return void
 */
function hook($hook, $params = [], $extra = null, $once = false)
{
	$res = \think\Hook::listen($hook, $params, $extra, $once);
	if (is_array($res)) {
		$res = array_filter($res);
		sort($res);
	}
	return $res;
	
}

/**
 * 错误返回值函数
 * @param string $data
 * @param string $const
 * @param unknown $vars
 * @return multitype:string mixed
 */
function error($data = null, $const = 'FAIL', $vars = [])
{
	return [
		'code' => defined($const) ? constant($const) : constant('UNKNOW_ERROR'),
		'message' => lang($const, $vars) ? : $const,
		'data' => $data
	];
}

/**
 * 正确返回值函数
 * @param string $data
 * @param string $const
 * @return multitype:string mixed
 */
function success($data = null, $const = 'SUCCESS')
{
	return [
		'code' => defined($const) ? constant($const) : constant('UNKNOW_ERROR'),
		'message' => lang($const) ? : $const,
		'data' => $data
	];
}

/**
 * 实例化Model
 *
 * @param string $name
 *            Model名称
 */
function model($table = '')
{
	return new \app\common\model\Model($table);
}

/**
 * 获取图片的真实路径
 *
 * @param string $path
 *            图片初始路径
 * @param string $type
 *            类型 BIG MID SMALL THUMB
 * @return string 图片的真实路径
 */
function img($path, $type = '')
{
	if (stristr($path, "http://") === false && stristr($path, "https://") === false) {
		$start = strripos($path, '.');
		$type = $type ? '_' . $type : '';
		$first = explode("/", $path);
		if (is_numeric($first[0])) {
			$true_path = __ROOT__ . '/attachment/' . substr_replace($path, $type, $start, 0);
		} else {
			$true_path = __ROOT__ . '/' . substr_replace($path, $type, $start, 0);
		}
	} else {
		$true_path = $path;
	}
	return $true_path;
}

/**
 * 获取标准二维码格式
 *
 * @param string $url
 * @param string $path
 * @param string $ext
 */
function qrcode($url, $path, $qrcode_name)
{
	if (!is_dir($path)) {
		$mode = intval('0777', 8);
		mkdir($path, $mode, true);
		chmod($path, $mode);
	}
	$path = $path . '/' . $qrcode_name . '.png';
	if (file_exists($path)) {
		unlink($path);
	}
	QRcode::png($url, $path, '', 4, 1);
	return $path;
}

/**
 * 获取授权信息 返回当前系统配置的app_key 和 app_secret
 * @return multitype:string |multitype:string unknown mixed
 */
function get_auth()
{
	$data = [
		'app_key' => '',
		'app_secret' => '',
	];
	$config_model = new Config();
	$auth_info = $config_model->getConfigInfo([ 'name' => 'SYSTEM_AUTH_CONFIG' ]);
	if (empty($auth_info)) {
		return $data;
	}
	$app_config = json_decode($auth_info['data']['value'], true);
	$data['app_key'] = $app_config['app_key'];
	$data['app_secret'] = $app_config['app_secret'];
	return $data;
}

/**
 * 判断 文件/目录 是否可写（取代系统自带的 is_writeable 函数）
 *
 * @param string $file 文件/目录
 * @return boolean
 */
function is_write($file)
{
	if (is_dir($file)) {
		$dir = $file;
		if ($fp = @fopen("$dir/test.txt", 'w')) {
			@fclose($fp);
			@unlink("$dir/test.txt");
			$writeable = true;
		} else {
			$writeable = false;
		}
	} else {
		if ($fp = @fopen($file, 'a+')) {
			@fclose($fp);
			$writeable = true;
		} else {
			$writeable = false;
		}
	}
	
	return $writeable;
}


/**
 * 前端页面api请求(通过api接口实现)
 * @param string $method
 * @param array $params
 * @return mixed
 */
function api($method, $params = [])
{
	$site_id = request()->siteid();
	if ($site_id != 0) {
		$site_model = new Site();
		$site_info = $site_model->getSiteInfo([
			'site_id' => $site_id
		]);
		
		if (empty($method)) {
			return error('', 'PARAMETER_ERROR');
		}
		
		if (!empty($site_info['data'])) {
			$params['site_id'] = $site_info['data']['site_id'];
			if (strpos(API_URL, request()->domain()) !== false) {
				//本地访问
				$data = get_api_data($method, $params);
				return $data;
				
			}
			
			if (!empty($site_info['data']['app_key'])) {
				$client = new SignClient($site_info['data']['app_key'], $site_info['data']['app_secret']);
				$data = $client->post($method, $params);
				return $data;
			}
		} else {
			return error();
		}
	}
}

/**
 * 获取Api类
 *
 * @param string $method
 */
function get_api_data($method, $params)
{
	$method_array = explode('.', $method);
	if ($method_array[0] == 'System') {
		$class_name = 'app\\api\\controller\\' . $method_array[1];
		if (!class_exists($class_name)) {
			return error();
		}
		$api_model = new $class_name($params);
	} else {
		
		$class_name = "addon\\system\\{$method_array[0]}\\api\\controller\\" . $method_array[1];
		if (!class_exists($class_name)) {
			$class_name = "addon\\module\\{$method_array[0]}\\api\\controller\\" . $method_array[1];
			if (!class_exists($class_name)) {
				$class_name = "addon\\app\\{$method_array[0]}\\api\\controller\\" . $method_array[1];
			}
		}
		
		if (!class_exists($class_name)) {
			return error();
		}
		$api_model = new $class_name($params);
	}
	$function = $method_array[2];
	$data = $api_model->$function($params);
	return $data;
}

/**
 * 判断当前是否是微信浏览器
 */
function is_weixin()
{
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
		return 1;
	}
	return 0;
}

/**
 * 获取系统const变量
 */
function get_const()
{
	$const = include(APP_PATH . 'extra/const.php');
	return $const;
}

/**
 * 文件夹文件拷贝
 *
 * @param string $src 来源文件夹
 * @param string $dst 目的地文件夹
 * @return bool
 */
function dir_copy($src = '', $dst = '')
{
	if (empty($src) || empty($dst)) {
		return false;
	}
	$dir = opendir($src);
	dir_mkdir($dst);
	while (false !== ($file = readdir($dir))) {
		if (($file != '.') && ($file != '..')) {
			if (is_dir($src . '/' . $file)) {
				dir_copy($src . '/' . $file, $dst . '/' . $file);
			} else {
				copy($src . '/' . $file, $dst . '/' . $file);
			}
		}
	}
	closedir($dir);
	
	return true;
}

/**
 * 创建文件夹
 *
 * @param string $path 文件夹路径
 * @param int $mode 访问权限
 * @param bool $recursive 是否递归创建
 * @return bool
 */
function dir_mkdir($path = '', $mode = 0777, $recursive = true)
{
	clearstatcache();
	if (!is_dir($path)) {
		mkdir($path, $mode, $recursive);
		return chmod($path, $mode);
	}
	
	return true;
}

/**
 * 将读取到的目录以数组的形式展现出来
 * @return array
 * opendir() 函数打开一个目录句柄，可由 closedir()，readdir() 和 rewinddir() 使用。
 * is_dir() 函数检查指定的文件是否是目录。
 * readdir() 函数返回由 opendir() 打开的目录句柄中的条目。
 * @param array $files 所有的文件条目的存放数组
 * @param string $file 返回的文件条目
 * @param string $dir 文件的路径
 * @param resource $handle 打开的文件目录句柄
 */
function dir_scan($dir)
{
	//定义一个数组
	$files = array();
	//检测是否存在文件
	if (is_dir($dir)) {
		//打开目录
		if ($handle = opendir($dir)) {
			//返回当前文件的条目
			while (($file = readdir($handle)) !== false) {
				//去除特殊目录
				if ($file != "." && $file != "..") {
					//判断子目录是否还存在子目录
					if (is_dir($dir . "/" . $file)) {
						//递归调用本函数，再次获取目录
						$files[ $file ] = dir_scan($dir . "/" . $file);
					} else {
						//获取目录数组
						$files[] = $file;
					}
				}
			}
			//关闭文件夹
			closedir($handle);
			//返回文件夹数组
			return $files;
		}
	}
}

/**
 * 读取文件单文件压缩 zipdir方法调用
 * @param unknown $dir
 * @param unknown $zip
 */
function add_file_toZip($dir, $zip, $newdir = '')
{
	$handler = opendir($dir); //打开当前文件夹由$dir指定
	$filename = readdir($handler);
	
	while (($filename = readdir($handler)) !== false) {
		
		if ($filename != "." && $filename != "..") {//文件夹文件名字为'.'和‘..'，不要对他们进行操作
			if (is_dir($dir . '/' . $filename)) {// 如果读取的某个对象是文件夹，则递归
				add_file_toZip($dir . "/" . $filename, $zip, $newdir);
			} else { //将文件加入zip对象
				
				$new_dir_sep = substr($dir, strpos($dir, $newdir));
				$zip->addFile($dir . "/" . $filename, $new_dir_sep . '/' . $filename);
			}
		}
	}
	@closedir($dir);
}

/**
 * 压缩文件夹
 * @param unknown $dir
 * @param unknown $zipfile
 */
function zip_dir($dir, $zipfile, $newdir = '')
{
	
	$zip = new ZipArchive();
	if ($zip->open($zipfile, ZipArchive::CREATE) === TRUE) {
		add_file_toZip($dir, $zip, $newdir); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
		
		$zip->close(); //关闭处理的zip文件
	}
}

/**
 * 文件强制下载
 * @param unknown $dir
 */
function dir_readfile($dir)
{
	
	if (file_exists($dir)) {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . basename($dir));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($dir));
		ob_clean();
		flush();
		readfile($dir);
	}
}

/**
 * 将端口转化为数组
 * @param string $port
 */
function getSupportPort($port)
{
	$array = [];
	if (!empty($port)) {
		$temp_array = explode(",", $port);
		$port_arr = config("support_app_type");
		foreach ($temp_array as $k => $v) {
			//如果当前端口存在
			if (!empty($port_arr[ $v ])) {
				$array[ $v ] = $port_arr[ $v ];
			}
		}
	}
	return $array;
	
}

/**
 * 删除指定目录下的文件和文件夹
 * @param unknown $dirpath
 * @return boolean
 */
function del_dir($dirpath)
{
	$dh = opendir($dirpath);
	while (($file = readdir($dh)) !== false) {
		if ($file != "." && $file != "..") {
			$fullpath = $dirpath . "/" . $file;
			if (!is_dir($fullpath)) {
				unlink($fullpath);
			} else {
				del_dir($fullpath);
				rmdir($fullpath);
			}
		}
	}
	closedir($dh);
	$isEmpty = true;
	$dh = opendir($dirpath);
	while (($file = readdir($dh)) !== false) {
		if ($file != "." && $file != "..") {
			$isEmpty = false;
			break;
		}
	}
	return $isEmpty;
}


/**查询本地php.exe位置(环境启动方式)
 * @return mixed|string
 */
function php_exe_real_path()
{
	if (substr(strtolower(PHP_OS), 0, 3) == 'win') {
		
		$ini = ini_get_all();
		
		$path = $ini['extension_dir']['local_value'];
		
		$php_path = str_replace('\\', '/', $path);
		
		$php_path = str_replace(array( '/ext/', '/ext' ), array( '/', '/' ), $php_path);
		
		$real_path = $php_path . 'php.exe';
		
	} else {
		
		$real_path = PHP_BINDIR . '/php';
		
	}
	
	if (strpos($real_path, 'ephp.exe') !== FALSE) {
		
		$real_path = str_replace('ephp.exe', 'php.exe', $real_path);
		
	}
	$php_path = $real_path;
	return $php_path;
}


/**
 * 根据年份计算生肖
 * @param unknown $year
 */
function get_zodiac($year)
{
	$animals = array(
		'鼠', '牛', '虎', '兔', '龙', '蛇', '马', '羊', '猴', '鸡', '狗', '猪'
	);
	$key = ($year - 1900) % 12;
	return $animals[ $key ];
}

/**
 * 计算.星座
 * @param int $month 月份
 * @param int $day 日期
 * @return str
 */
function get_constellation($month, $day)
{
	$constellations = array(
		'水瓶座', '双鱼座', '白羊座', '金牛座', '双子座', '巨蟹座',
		'狮子座', '处女座', '天秤座', '天蝎座', '射手座', '摩羯座'
	);
	if ($day <= 22) {
		if (1 != $month) {
			$constellation = $constellations[ $month - 2 ];
		} else {
			$constellation = $constellations[11];
		}
	} else {
		$constellation = $constellations[ $month - 1 ];
	}
	return $constellation;
}


/**
 * 获取授权信息 返回当前系统配置的app_key 和 app_secret
 * @return multitype:string |multitype:string unknown mixed
 */
function getAuth()
{
	$data = [
		'app_key' => '',
		'app_secret' => '',
	];
	$config_model = new Config();
	$auth_info = $config_model->getConfigInfo([ 'name' => 'SYSTEM_AUTH_CONFIG' ]);
	if (empty($auth_info)) {
		return $data;
	}
	$app_config = json_decode($auth_info['data']['value'], true);
	$data['app_key'] = $app_config['app_key'];
	$data['app_secret'] = $app_config['app_secret'];
	return $data;
}

/**
 * 数组键名转化为数字
 * @param $data
 */
function arr_key_to_int($data, $clild_name)
{
	$temp_data = array_values($data);
	foreach ($temp_data as $k => $v) {
		if (!empty($v[ $clild_name ])) {
			$temp_data[ $k ][ $clild_name ] = arr_key_to_int($v[ $clild_name ], $clild_name);
		}
	}
	return $temp_data;
}

/**
 * 文件尺寸大小
 * @param unknown $dir
 * @return number
 */
function getDirSize($dir_path)
{
	$size = 0;
	if (is_dir($dir_path)) {
		$handle = opendir($dir_path);
		while (false !== ($entry = readdir($handle))) {
			if ($entry != '.' && $entry != '..') {
				if (is_dir("{$dir_path}/{$entry}")) {
					$size += getDirSize("{$dir_path}/{$entry}");
				} else {
					$size += filesize("{$dir_path}/{$entry}");
				}
			}
		}
		closedir($handle);
	}
	return $size;
}


/**
 * 文件尺寸大小换算
 * @param unknown $size
 * @return string
 */
function sizeConversion($size_num)
{
	
	switch ($size_num) {
		case $size_num >= 1073741824:
			$size_str = round($size_num / 1073741824 * 100) / 100 . ' GB';
			break;
		case $size_num >= 1048576:
			$size_str = round($size_num / 1048576 * 100) / 100 . ' MB';
			break;
		case $size_num >= 1024:
			$size_str = round($size_num / 1024 * 100) / 100 . ' KB';
			break;
		default:
			$size_str = $size_num . ' Bytes';
			break;
	}
	
	return $size_str;
}

/**
 * 获取当前ip
 */
function getip()
{
	static $ip = '';
	$ip = $_SERVER['REMOTE_ADDR'];
	if (isset($_SERVER['HTTP_CDN_SRC_IP'])) {
		$ip = $_SERVER['HTTP_CDN_SRC_IP'];
	} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
		foreach ($matches[0] AS $xip) {
			if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
				$ip = $xip;
				break;
			}
		}
	}
	if (preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $ip)) {
		return $ip;
	} else {
		return '127.0.0.1';
	}
}

/**
 * 以天为单位 计算间隔内的日期数组
 * @param $srart_time
 * @param $end_time
 * @return array
 */
function periodGroup($srart_time, $end_time, $format = 'Ymd')
{
	$type_time = 3600 * 24;
	$data = [];
	for ($i = $srart_time; $i <= $end_time; $i += $type_time) {
		$data[] = date($format, $i);
	}
	return $data;
}

/**
 * 数组删除另一个数组
 * @param $arr
 * @param $del_arr
 * @return mixed
 */
function arrDelArr($arr, $del_arr)
{
	foreach ($arr as $k => $v) {
		if (in_array($v, $del_arr)) {
			unset($arr[ $k ]);
		}
	}
	sort($arr);
	return $arr;
}

/**
 * 判断 文件/目录 是否可写（取代系统自带的 is_writeable 函数）
 *
 * @param string $file 文件/目录
 * @return boolean
 */
function testWrite($file)
{
	if (is_dir($file)) {
		$dir = $file;
		if ($fp = @fopen("$dir/test.txt", 'w')) {
			@fclose($fp);
			@unlink("$dir/test.txt");
			$writeable = true;
		} else {
			$writeable = false;
		}
	} else {
		if ($fp = @fopen($file, 'a+')) {
			@fclose($fp);
			$writeable = true;
		} else {
			$writeable = false;
		}
	}
	
	return $writeable;
}

/**
 * 检测登录(应用于h5网页检测登录)
 * @param unknown $url
 */
function check_auth($url = '')
{
	$access_token = Session::get("access_token_" . request()->siteid());
	if (empty($access_token)) {
		if (!empty($url)) {
			Session::set("redirect_login_url", $url);
		}
		//尚未登录(直接跳转)
		return error(url('wap/login/login'));
	}
	$member_info = cache("member_info_" . request()->siteid() . $access_token);
	if (empty($member_info)) {
		$member_info = api("System.Member.memberInfo", [ 'access_token' => $access_token ]);
		if ($member_info['code'] == 0) {
			$member_info = $member_info['data'];
			cache("member_info_" . request()->siteid() . $access_token, $member_info);
		}
	}
	$member_info['access_token'] = $access_token;
	return success($member_info);
}

/**
 * 分割sql语句
 * @param  string $content sql内容
 * @param  bool $string 如果为真，则只返回一条sql语句，默认以数组形式返回
 * @param  array $replace 替换前缀，如：['my_' => 'me_']，表示将表前缀my_替换成me_
 * @return array|string 除去注释之后的sql语句数组或一条语句
 */
function parseSql($content = '', $string = false, $replace = [])
{
    // 纯sql内容
    $pure_sql = [];
    // 被替换的前缀
    $from = '';
    // 要替换的前缀
    $to = '';
    // 替换表前缀
    if (!empty($replace)) {
        $to   = current($replace);
        $from = current(array_flip($replace));
    }
    if ($content != '') {
        // 多行注释标记
        $comment = false;
        // 按行分割，兼容多个平台
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $content = explode("\n", trim($content));
        // 循环处理每一行
        foreach ($content as $key => $line) {
            // 跳过空行
            if ($line == '') {
                continue;
            }
            // 跳过以#或者--开头的单行注释
            if (preg_match("/^(#|--)/", $line)) {
                continue;
            }
            // 跳过以/**/包裹起来的单行注释
            if (preg_match("/^\/\*(.*?)\*\//", $line)) {
                continue;
            }
            // 多行注释开始
            if (substr($line, 0, 2) == '/*') {
                $comment = true;
                continue;
            }
            // 多行注释结束
            if (substr($line, -2) == '*/') {
                $comment = false;
                continue;
            }
            // 多行注释没有结束，继续跳过
            if ($comment) {
                continue;
            }
            // 替换表前缀
            if ($from != '') {
                $line = str_replace('`'.$from, '`'.$to, $line);
            }
            // sql语句
            $pure_sql[] = $line;
        }
        // 只返回一条语句
        if ($string) {
            return implode($pure_sql, "");
        }
        // 以数组形式返回sql语句
        $pure_sql = implode($pure_sql, "\n");
        $pure_sql = explode(";\n", $pure_sql);
    }
    return $pure_sql;
}