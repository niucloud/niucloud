<?php

namespace util\database;

class upgradeDb
{
    protected $path;
    public function __construct($path){
        $this->path = $path;
    }
    public function exportDatabase($tables){
        $config = array(
            'path' => $this->path,
            'part' => 20971520,
            'compress' => 0,
            'level' => 9
        );
        $file = array(
            'name' => date('Ymd-His', time()),
            'part' => 1
        );
        
        $database = new Database($file, $config);
        $start = 0;
        foreach ($tables as $id => $value){
            $start = $database->backup($tables[$id], $start);
            if (false === $start) {
                return error('', '备份数据库出错');
                break;
            }
        }
        return success($start);
    }
    
    /**
     * * 备份数据
     */
    public function exportDatabase1()
    {
        // $tables = request()->post('tables','');
        // $id = request()->post('id','');
        // $start = request()->post('start','');
        $tables = isset($_POST["tables"]) ? $_POST["tables"] : array();
        $id = isset($_POST["id"]) ? $_POST["id"] : '';
        $start = isset($_POST["start"]) ? $_POST["start"] : '';
        if (! empty($tables) && is_array($tables)) { // 初始化
            // 读取备份配置
            $config = array(
                'path' => $this->backup_path . DIRECTORY_SEPARATOR,
                'part' => 20971520,
                'compress' => 1,
                'level' => 9
            );
            // 检查是否有正在执行的任务
            $lock = "{$config['path']}backup.lock";
            if (is_file($lock)) {
                return showMessage('检测到有一个备份任务正在执行，请稍后再试！', - 1);
            } else {
                $mode = intval('0777', 8);
                if (! file_exists($config['path']) || ! is_dir($config['path']))
                    mkdir($config['path'], $mode, true); // 创建锁文件
    
                file_put_contents($lock, date('Ymd-His', time()));
            }
            // 自动创建备份文件夹
            // 检查备份目录是否可写
            is_writeable($config['path']) || exit('backup_not_exist_success');
            session('backup_config', $config);
            // 生成备份文件信息
            $file = array(
                'name' => date('Ymd-His', time()),
                'part' => 1
            );
    
            session('backup_file', $file);
    
            // 缓存要备份的表
            session('backup_tables', $tables);
    
            $database = new database($file, $config);
            if (false !== $database->create()) {
                $tab = array(
                    'id' => 0,
                    'start' => 0
                );
                $data = array();
                $data['status'] = 1;
                $data['message'] = '初始化成功';
                $data['tables'] = $tables;
                $data['tab'] = array(
                    'id' => 0,
                    'start' => 0
                );
                return $data;
            } else {
                return showMessage('初始化失败，备份文件创建失败！', - 1);
            }
        } elseif (is_numeric($id) && is_numeric($start)) { // 备份数据
            $tables = session('backup_tables');
            // 备份指定表
            $database = new database(session('backup_file'), session('backup_config'));
            $start = $database->backup($tables[$id], $start);
            if (false === $start) { // 出错
                return showMessage('备份出错！');
            } elseif (0 === $start) { // 下一表
                if (isset($tables[++ $id])) {
                    $tab = array(
                        'id' => $id,
                        'table' => $tables[$id],
                        'start' => 0
                    );
                    $data = array();
                    $data['rate'] = 100;
                    $data['status'] = 1;
                    $data['message'] = '备份完成！';
                    $data['tab'] = $tab;
                    return $data;
                } else { // 备份完成，清空缓存
                    unlink($this->backup_path . DIRECTORY_SEPARATOR . 'backup.lock');
                    session('backup_tables', null);
                    session('backup_file', null);
                    session('backup_config', null);
                    return showMessage('操作成功!', 0);
                }
            } else {
                $tab = array(
                    'id' => $id,
                    'table' => $tables[$id],
                    'start' => $start[0]
                );
                $rate = floor(100 * ($start[0] / $start[1]));
                $data = array();
                $data['status'] = 1;
                $data['rate'] = $rate;
                $data['message'] = "正在备份...({$rate}%)";
                $data['tab'] = $tab;
                return $data;
            }
        } else { // 出错
            return showMessage('参数有误!');
        }
    }
    
    /**
     * 还原数据库
     *
     * @author
     *
     */
    public function importData()
    {
        $time = request()->post('time', '');
        $part = request()->post('part', 0);
        $start = request()->post('start', 0);
    
        if (is_numeric($time) && (is_null($part) || empty($part)) && (is_null($start) || empty($start))) { // 初始化
            // 获取备份文件信息
            $name = date('Ymd-His', $time) . '-*.sql*';
            $path = realpath($this->backup_path) . DIRECTORY_SEPARATOR . $name;
            $files = glob($path);
            $list = array();
            foreach ($files as $name) {
                $basename = basename($name);
                $match = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
                $gz = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
                $list[$match[6]] = array(
                    $match[6],
                    $name,
                    $gz
                );
            }
            ksort($list);
            // 检测文件正确性
            $last = end($list);
            if (count($list) === $last[0]) {
                session('backup_list', $list); // 缓存备份列表
                return showMessage("初始化完成", 1, array(
                    'part' => 1,
                    'start' => 0
                ));
            } else {
                return showMessage("备份文件可能已经损坏，请检查！");
            }
        } elseif (is_numeric($part) && is_numeric($start)) {
            $list = session('backup_list');
            $db = new database($list[$part], array(
                'path' => realpath($this->backup_path) . DIRECTORY_SEPARATOR,
                'compress' => $list[$part][2]
            ));
    
            $start = $db->import($start);
            if ($start === false) {
                return showMessage("还原数据出错！");
            } elseif ($start === 0) { // 下一卷
                if (isset($list[++ $part])) {
                    $data = array(
                        'part' => $part,
                        'start' => 0
                    );
                    return showMessage("正在还原...#{$part}", 0, $data);
                } else {
                    session('backup_list', null);
                    return showMessage("还原完成！");
                }
            } else {
                $data = array(
                    'part' => $part,
                    'start' => $start[0]
                );
                if ($start[1]) {
                    $rate = floor(100 * ($start[0] / $start[1]));
                    return showMessage("正在还原...#{$part} ({$rate}%)", 1);
                } else {
                    $data['gz'] = 1;
                    return showMessage("正在还原...#{$part}", 1, $data);
                }
            }
        } else {
            return showMessage("参数有误");
        }
    }
    
    
}