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

namespace app\install\controller;

use app\common\model\Addon;
use app\common\model\Site;
use app\common\model\User;
use think\Controller;
use app\common\model\Auth;

class Index extends Controller
{
    public $replace;
    public $lock_file;

    public function __construct()
    {
        parent::__construct();
        $this->replace = [
            'INSTALL_CSS' => __ROOT__ . '/application/install/view/public/css',
            'INSTALL_IMG' => __ROOT__ . '/application/install/view/public/img',
            'INSTALL_JS' => __ROOT__ . '/application/install/view/public/js',
        ];
        $this->lock_file = './install.lock';//锁定文件
        if(file_exists($this->lock_file)){
            $this->error("不能重复安装");
        }
    }

    /**
     *安装
     */
    public function index()
    {
        $step = input("step", 1);
        $root_url = __ROOT__;
        $this->assign("root_url", $root_url);
        if ($step == 1) {
            return $this->fetch('index/step-1', [], $this->replace);
        } else if ($step == 2) {

            $phpv = phpversion();
            $os = PHP_OS;
            $gd = gd_info();
            $server = $_SERVER['SERVER_SOFTWARE'];

            $host = (empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_HOST'] : $_SERVER['REMOTE_ADDR']);
            $name = $_SERVER['SERVER_NAME'];

            $verison = version_compare(PHP_VERSION, '5.6.0') == -1 ? false : true;
            $pdo = extension_loaded('pdo') && extension_loaded('pdo_mysql');
            $curl = extension_loaded('curl') && function_exists('curl_init');
            $openssl = extension_loaded('openssl');
            $gd = extension_loaded('gd');
            $fileinfo = extension_loaded('fileinfo');
            $root_path = str_replace("\\",DS, dirname(dirname(dirname(dirname(__FILE__)))));
            $root_path = str_replace("/",DS, $root_path);
            $dirs = array(
                array("path" => $root_path, "path_name" => "/", "name" => "整目录"),
                array("path" => $root_path.DS."public", "path_name" => "public","name" => "public"),
                array("path" => $root_path.DS.'runtime', "path_name" => "runtime", "name" => "runtime"),
                array("path" => $root_path.DS.'application/install', "path_name" => "application/install", "name" => "安装目录"),
            );

            $this->assign("phpv", $phpv);
            $this->assign("os", $os);
            $this->assign("gd", $gd);
            $this->assign("server", $server);
            $this->assign("host", $host);
            $this->assign("name", $name);
            $this->assign("verison", $verison);
            $this->assign("pdo", $pdo);
            $this->assign("curl", $curl);
            $this->assign("openssl", $openssl);
            $this->assign("gd", $gd);
            $this->assign("fileinfo", $fileinfo);
            $this->assign("dirs", $dirs);
            $this->assign("root", __ROOT__);
            if($verison && $pdo && $curl && $openssl && $gd && $fileinfo ){
                $continue = true;
            }else{
                $continue = false;
            }
            $this->assign("continue", $continue);
            return $this->fetch('index/step-2', [], $this->replace);
        } else if ($step == 3) {
            return $this->fetch('index/step-3', [], $this->replace);
        } else if ($step == 4) {
            $source_file = "./application/install/source/database.php";//源配置文件

            $target_dir = "./application";
            $target_file = "database.php";

            $file_name = "./application/install/source/niucloud.sql";//数据文件

            $dbport = input("dbport", "3306");
            $dbhost = input("dbhost", "localhost");
            $dbuser = input("dbuser", "root");
            $dbpwd = input("dbpwd", "root");
            $dbname = input("dbname", "niucloud");

            $admin_name = "admin";
            $admin_pwd = input('admin_pwd', "123456");
            $admin_pwd_confirm = input('admin_pwd_confirm', "123456");
            $site_name = input('site_name', "");
            if ($dbhost == '' || $dbuser == '') {
                die("<script>alert('数据库链接配置信息丢失!');history.go(-1);</script>");
            }
            //可写测试
            $write_result = is_write($target_dir);
            if ($write_result["code"] != 0) {
                //判断是否有可写的权限，linux操作系统要注意这一点，windows不必注意。
                die("<script>alert('配置文件不可写，权限不够!');history.go(-1);</script>");
            }
            //数据库连接测试
            $conn = @mysqli_connect($dbhost, $dbuser, $dbpwd);
            if (!$conn) {
                die("<script>alert('连接数据库失败！请返回上一页检查连接参数!');history.go(-1);</script>");
            }
            //管理员账号合法性验证
            if ($admin_name == '' || $admin_pwd == '') {
                die("<script>alert('管理员用户名与密码不能为空!');history.go(-1);</script>");

            }
            //管理员密码两次输入不一致
            if ($admin_pwd != $admin_pwd_confirm) {
                die("<script>alert('两次密码输入不一致!');history.go(-1);</script>");
            }
            //站点名称是否为空
            if (empty($site_name)) {
                die("<script>alert('站点名称是否为空！');history.go(-1);</script>");
            }
            //数据库可写和是否存在测试
            $empty_db = mysqli_select_db($conn, $dbname);
            if (!$empty_db) {
                //如果数据库不存在，我们就进行创建。
                $dbsql = "CREATE DATABASE `$dbname`";
                $db_create = mysqli_query($conn, $dbsql);
                if (!$db_create) {
                    die("<script>alert('创建数据库失败，请确认是否有足够的权限!');history.go(-1);</script>");
                }
            } else {
                die("<script>alert('数据库已存在!');history.go(-1);</script>");
            }
            //链接数据库
            @mysqli_select_db($conn, $dbname);

            //修改配置文件
            $fp = fopen($source_file, "r");
            $configStr = fread($fp, filesize($source_file));
            fclose($fp);

            $configStr = str_replace('model_hostname', $dbhost, $configStr);
            $configStr = str_replace('model_database', $dbname, $configStr);
            $configStr = str_replace("model_username", $dbuser, $configStr);
            $configStr = str_replace("model_password", $dbpwd, $configStr);
            $configStr = str_replace("model_port", $dbport, $configStr);

            $fp = fopen($target_dir . DS . $target_file, "w") or die("<script>alert('写入配置失败，请检查{$target_dir}/{$target_file}是否可写入！');history.go(-1);</script>");
            fwrite($fp, $configStr);
            fclose($fp);

            //导入SQL并执行。
            $get_sql_data = file_get_contents($file_name);

            @mysqli_query($conn, "SET NAMES utf8");
            //提取create
            preg_match_all("/Create table .*\(.*\).*\;/iUs", $get_sql_data, $create_sql_arr);
            $create_sql_arr = $create_sql_arr[0];

            foreach ($create_sql_arr as $create_sql_item) {
                @mysqli_query($conn, $create_sql_item);
            }
            //提取insert
            preg_match_all("/INSERT INTO .*\(.*\)\;/iUs", $get_sql_data, $insert_sql_arr);
            $insert_sql_arr = $insert_sql_arr[0];

            //插入数据
            foreach ($insert_sql_arr as $insert_sql_item) {
                @mysqli_query($conn, $insert_sql_item);
            }
            @mysqli_close($conn);
            $database_config = include $target_dir . DS . $target_file;
            config("database", $database_config);

            //安装插件
            $addon_model = new Addon();
            $module_list = $addon_model->installAllAddon();
            $auth_model = new Auth();
            $group_data = array(
                "site_id" => 0,
                "group_name" => "超级管理员",
                "is_system" => 1,
                "status" => 1
            );
            $auth_result = $auth_model->addGroup($group_data);
            if ($auth_result["code"] != 0)
                die("<script>alert('管理员权限组添加失败！');history.go(-1);</script>");


            //新建管理员
            $user_model = new User();
            $user_data = array(
                "username" => $admin_name,
                "password" => $admin_pwd,
                "is_admin" => 1
            );
            $res = $user_model->addUser($user_data, $auth_result["data"]);

            if ($res["code"] != 0)
                die("<script>alert('管理员添加失败！');history.go(-1);</script>");


            //默认安装第一个应用站点
            if (!empty($module_list["data"])) {
                //创建站点
                $site_model = new Site();
                $site_data = array(
                    "uid" => 1,
                    "addon_app" => $module_list["data"][0]["name"],
                    "site_name" => $site_name,
                    'create_time' => time()
                );
                $site_result = $site_model->addSite($site_data);
            }
            if ($site_result["code"]) {
                $site_id = $site_result["data"];
            }
            $this->assign("site_id", $site_id);

            $fp = fopen($this->lock_file, "w") or die("写入失败，请检查目录" . dirname(dirname(__FILE__)) . "是否可写入！'");
            fwrite($fp, '已安装');
            fclose($fp);

            return $this->fetch('index/step-4', [], $this->replace);
        }
    }

    /**
     * 测试数据库
     */
    public function testDb($dbhost = '', $dbport = '', $dbuser = '', $dbpwd = '', $dbname = '')
    {
        $dbport = input("dbport", "");
        $dbhost = input("dbhost", "");
        $dbuser = input("dbuser", "");
        $dbpwd = input("dbpwd", "");
        $dbname = input("dbname", "");
        try {
//            $pdo = new PDO("mysql:host={$dbhost};port={$dbport}", $dbuser, $dbpwd, array(
//                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
//                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
//            ));

            if ($dbport != "" && $dbhost != "") {
                $dbhost = $dbport != '3306' ? $dbhost . ':' . $dbport : $dbhost;
            }

            if ($dbhost == '' || $dbuser == '')
                return error("");

            $conn = @mysqli_connect($dbhost, $dbuser, $dbpwd);
            if ($conn) {
                if (empty($dbname)) {
                    return success("<font color=#0d73f9>数据库连接成功</font>");
                } else {
                    $info = @mysqli_select_db($conn, $dbname) ? "<font color=#0d73f9>数据库存在，系统将覆盖数据库</font>" : "<font color=#0d73f9>数据库不存在,系统将自动创建</font>";
                    return success($info);
                }
            } else {
                return success("<font color=#0d73f9>数据库连接失败！</font>");
            }
            @mysqli_close($conn);
        } catch (\Exception $e) {
            return error($e->getMessage());
        }
    }

    //清空目录
    public function clean_dir($path)
    {
        if (!is_dir($path)) {
            if (is_file($path)) {
                unlink($path);
            }
            return;
        }
        $p = opendir($path);
        while ($f = readdir($p)) {
            if ($f == "." || $f == "..") continue;
            $this->clean_dir($path . $f);
        }
        @rmdir($path);
        return;
    }

    /**
     * 可写测试
     * @param $file
     * @return int
     */
    function testWrite($file)
    {
        if (is_dir($file)) {
            $dir = $file;
            if ($fp = @fopen("$dir/test.txt", 'w')) {
                @fclose($fp);
                @unlink("$dir/test.txt");
                $writeable = 1;
            } else {
                $writeable = 0;
            }
        } else {
            if ($fp = @fopen($file, 'a+')) {
                @fclose($fp);
                $writeable = 1;
            } else {
                $writeable = 0;
            }
        }
        if ($writeable == 1)
            return success($writeable);

        return error($writeable);
    }
}