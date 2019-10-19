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


use util\api\WebClient;
use think\Db;
/**
 * 系统升级
 * @author Administrator
 *
 */
class Upgrade
{
    public $app_key = '';
    public $app_secret = '';
    public $domain = '';
    /**
     * 获取升级信息
     * @return multitype:string
     */
    public function getUpgradeInfo(){
        $cloudip = gethostbyname('localhost');
        if (empty($cloudip) || !preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $cloudip)) {
            return error("云服务器检测失败");
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
        return success($info);
    }
    
    /**
     * 下载单个升级文件
     * @param unknown $token
     * @param unknown $file
     */
    public function download($token, $file)
    {
        $config_model = new Config();
        $auth_info = $config_model->getConfigInfo([ 'name' => 'SYSTEM_AUTH_CONFIG' ]);
        $app_config = json_decode($auth_info['data']['value'], true);
        $this->app_key = $app_config['app_key'];
        $this->app_secret = $app_config['app_secret'];
        $this->domain = $app_config['domain'];
        $token = 'MDAwMDAwMDAwMMvff8qaeXOWxpa6Z67Q1ZqSeKTQydN_15ZlfpysoqyfyZmh3I9ke5zHq7qayJi7moeafd-yqX6agXebqrGzdq2zqnrLg3R7qsq8spjJ07yqm2Og3MfOgpt9oYmcr3x_Zse9Zs6Am6Cos7i9ZrKqzauFdYWYsaiD3o2KZZ6so6Sfx71_1JiJi2DG0Z2ayM-wag';
        $file = 'application/wap/view/public/img/login_img/captcha_code.png';
        $file_path = dirname($file);
        $data = [
            'token' => $token,
            'file'  => $file
        ];
        $http = new WebClient($this->app_key, $this->app_secret);
        $info = $http->post('Upgrade.download', $data);
        $info = json_decode($info, true);
        $info = base64_decode($info['data']);
        $dir_make = dir_mkdir('data/upgrade/'.$file_path);
        if($dir_make)
        {
            $res = file_put_contents('data/upgrade/'.$file_path.'/captcha_code.png', $info);
            return success($res);
        }else{
            return error("文件读写权限不足");
        }
    }
    
    /**
     * 获取表结构
     * @param string $tablename
     */
    public function dbTableSchema($tablename = '') {
        
    	$result = model($tablename)->query("SHOW TABLE STATUS LIKE '" .$tablename."'");
    	if(empty($result)) {
    		return array();
    	}
    	$ret['tablename'] = $result[0]['Name'];
    	$ret['charset'] = $result[0]['Collation'];
    	$ret['engine'] = $result[0]['Engine'];
    	$ret['increment'] = $result[0]['Auto_increment'];
    	$result = model($tablename)->query("SHOW FULL COLUMNS FROM " . $tablename);
    	foreach($result as $value) {
    		$temp = array();
    		$type = explode(" ", $value['Type'], 2);
    		$temp['name'] = $value['Field'];
    		$pieces = explode('(', $type[0], 2);
    		$temp['type'] = $pieces[0];
    		$temp['length'] = rtrim($pieces[1], ')');
    		$temp['null'] = $value['Null'] != 'NO';
    										$temp['signed'] = empty($type[1]);
    		$temp['increment'] = $value['Extra'] == 'auto_increment';
    		$ret['fields'][$value['Field']] = $temp;
    	}
    	$result = model($tablename)->query("SHOW INDEX FROM " . $tablename);
    	foreach($result as $value) {
    		$ret['indexes'][$value['Key_name']]['name'] = $value['Key_name'];
    		$ret['indexes'][$value['Key_name']]['type'] = ($value['Key_name'] == 'PRIMARY') ? 'primary' : ($value['Non_unique'] == 0 ? 'unique' : 'index');
    		$ret['indexes'][$value['Key_name']]['fields'][] = $value['Column_name'];
    	}
    	return $ret;
    }
    
    /**
     * 数据库比对
     * @param unknown $table1
     * @param unknown $table2
     */
    public function dbTableCompare($table1, $table2) {
        
        $table1['charset'] == $table2['charset'] ? '' : $ret['diffs']['charset'] = true;
    
        $fields1 = array_keys($table1['fields']);
        $fields2 = array_keys($table2['fields']);
        $diffs = array_diff($fields1, $fields2);
        if(!empty($diffs)) {
            $ret['fields']['greater'] = array_values($diffs);
        }
        $diffs = array_diff($fields2, $fields1);
        if(!empty($diffs)) {
            $ret['fields']['less'] = array_values($diffs);
        }
        $diffs = array();
        $intersects = array_intersect($fields1, $fields2);
        if(!empty($intersects)) {
            foreach($intersects as $field) {
                if($table1['fields'][$field] != $table2['fields'][$field]) {
                    $diffs[] = $field;
                }
            }
        }
        if(!empty($diffs)) {
            $ret['fields']['diff'] = array_values($diffs);
        }
    
        $indexes1 = is_array($table1['indexes']) ? array_keys($table1['indexes']) : array();
        $indexes2 = is_array($table2['indexes']) ? array_keys($table2['indexes']) : array();
        $diffs = array_diff($indexes1, $indexes2);
        if(!empty($diffs)) {
            $ret['indexes']['greater'] = array_values($diffs);
        }
        $diffs = array_diff($indexes2, $indexes1);
        if(!empty($diffs)) {
            $ret['indexes']['less'] = array_values($diffs);
        }
        $diffs = array();
        $intersects = array_intersect($indexes1, $indexes2);
        if(!empty($intersects)) {
            foreach($intersects as $index) {
                if($table1['indexes'][$index] != $table2['indexes'][$index]) {
                    $diffs[] = $index;
                }
            }
        }
        if(!empty($diffs)) {
            $ret['indexes']['diff'] = array_values($diffs);
        }
    
        return $ret;
    }


    /**
     * 数据库序列化
     * @param unknown $dbname
     */
    public function dbTableSerialize($dbname) {
        $tables = model()->query('SHOW TABLES');
        if (empty($tables)) {
            return '';
        }
        $struct = array();
        foreach ($tables as $value) {
            $structs[] = $this->dbTableSchema(substr($value['Tables_in_' . $dbname], strpos($value['Tables_in_' . $dbname], '_') + 1));
        }
        return $structs;
    }
    
    /**
     * 数据表创建sql
     * @param unknown $schema
     */
    public function dbTableCreateSql($schema) {
        
        $pieces = explode('_', $schema['charset']);
        $charset = $pieces[0];
        $engine = $schema['engine'];
        $sql = "CREATE TABLE IF NOT EXISTS `{$schema['tablename']}` (\n";
        foreach ($schema['fields'] as $value) {
            $piece = $this->dbBuildFieldSql($value);
            $sql .= "`{$value['name']}` {$piece},\n";
        }
        foreach ($schema['indexes'] as $value) {
            $fields = implode('`,`', $value['fields']);
            if($value['type'] == 'index') {
                $sql .= "KEY `{$value['name']}` (`{$fields}`),\n";
            }
            if($value['type'] == 'unique') {
                $sql .= "UNIQUE KEY `{$value['name']}` (`{$fields}`),\n";
            }
            if($value['type'] == 'primary') {
                $sql .= "PRIMARY KEY (`{$fields}`),\n";
            }
        }
        $sql = rtrim($sql);
        $sql = rtrim($sql, ',');
    
        $sql .= "\n) ENGINE=$engine DEFAULT CHARSET=$charset;\n\n";
        return $sql;
    }
    
    /**
     * 数据表比对sql
     * @param unknown $schema1
     * @param unknown $schema2
     * @param string $strict
     */
    public function dbTableFixSql($schema1, $schema2, $strict = false) {
        
        if(empty($schema1)) {
            return array($this->dbTableCreateSql($schema2));
        }
        $diff = $result = $this->dbTableCompare($schema1, $schema2);
        if(!empty($diff['diffs']['tablename'])) {
            return array($this->dbTableCreateSql($schema2));
        }
        $sqls = array();
        if(!empty($diff['diffs']['engine'])) {
            $sqls[] = "ALTER TABLE `{$schema1['tablename']}` ENGINE = {$schema2['engine']}";
        }
    
        if(!empty($diff['diffs']['charset'])) {
            $pieces = explode('_', $schema2['charset']);
            $charset = $pieces[0];
            $sqls[] = "ALTER TABLE `{$schema1['tablename']}` DEFAULT CHARSET = {$charset}";
        }
    
        if(!empty($diff['fields'])) {
            if(!empty($diff['fields']['less'])) {
                foreach($diff['fields']['less'] as $fieldname) {
                    $field = $schema2['fields'][$fieldname];
                    $piece = $this->dbBuildFieldSql($field);
                    if(!empty($field['rename']) && !empty($schema1['fields'][$field['rename']])) {
                        $sql = "ALTER TABLE `{$schema1['tablename']}` CHANGE `{$field['rename']}` `{$field['name']}` {$piece}";
                        unset($schema1['fields'][$field['rename']]);
                    } else {
                        if($field['position']) {
                            $pos = ' ' . $field['position'];
                        }
                        $sql = "ALTER TABLE `{$schema1['tablename']}` ADD `{$field['name']}` {$piece}{$pos}";
                    }
                    $primary = array();
                    $isincrement = array();
                    if (strstr($sql, 'AUTO_INCREMENT')) {
                        $isincrement = $field;
                        $sql =  str_replace('AUTO_INCREMENT', '', $sql);
                        foreach ($schema1['fields'] as $field) {
                            if ($field['increment'] == 1) {
                                $primary = $field;
                                break;
                            }
                        }
                        if (!empty($primary)) {
                            $piece = $this->dbBuildFieldSql($primary);
                            if (!empty($piece)) {
                                $piece = str_replace('AUTO_INCREMENT', '', $piece);
                            }
                            $sqls[] = "ALTER TABLE `{$schema1['tablename']}` CHANGE `{$primary['name']}` `{$primary['name']}` {$piece}";
                        }
                    }
                    $sqls[] = $sql;
                }
            }
            if(!empty($diff['fields']['diff'])) {
                foreach($diff['fields']['diff'] as $fieldname) {
                    $field = $schema2['fields'][$fieldname];
                    $piece = $this->dbBuildFieldSql($field);
                    if(!empty($schema1['fields'][$fieldname])) {
                        $sqls[] = "ALTER TABLE `{$schema1['tablename']}` CHANGE `{$field['name']}` `{$field['name']}` {$piece}";
                    }
                }
            }
            if($strict && !empty($diff['fields']['greater'])) {
                foreach($diff['fields']['greater'] as $fieldname) {
                    if(!empty($schema1['fields'][$fieldname])) {
                        $sqls[] = "ALTER TABLE `{$schema1['tablename']}` DROP `{$fieldname}`";
                    }
                }
            }
        }
    
        if(!empty($diff['indexes'])) {
            if(!empty($diff['indexes']['less'])) {
                foreach($diff['indexes']['less'] as $indexname) {
                    $index = $schema2['indexes'][$indexname];
                    $piece = $this->dbBuildFieldSql($index);
                    $sqls[] = "ALTER TABLE `{$schema1['tablename']}` ADD {$piece}";
                }
            }
            if(!empty($diff['indexes']['diff'])) {
                foreach($diff['indexes']['diff'] as $indexname) {
                    $index = $schema2['indexes'][$indexname];
                    $piece = $this->dbBuildFieldSql($index);
    
                    $sqls[] = "ALTER TABLE `{$schema1['tablename']}` DROP ".($indexname == 'PRIMARY' ? " PRIMARY KEY " : "INDEX {$indexname}").", ADD {$piece}";
                }
            }
            if($strict && !empty($diff['indexes']['greater'])) {
                foreach($diff['indexes']['greater'] as $indexname) {
                    $sqls[] = "ALTER TABLE `{$schema1['tablename']}` DROP `{$indexname}`";
                }
            }
        }
        if (!empty($isincrement)) {
            $piece = $this->dbBuildFieldSql($isincrement);
            $sqls[] = "ALTER TABLE `{$schema1['tablename']}` CHANGE `{$isincrement['name']}` `{$isincrement['name']}` {$piece}";
        }
        return $sqls;
    }
    
    /**
     * 创建表索引
     * @param unknown $index
     */
    public function dbBuildIndexSql($index) {
        $piece = '';
        $fields = implode('`,`', $index['fields']);
        if($index['type'] == 'index') {
            $piece .= " INDEX `{$index['name']}` (`{$fields}`)";
        }
        if($index['type'] == 'unique') {
            $piece .= "UNIQUE `{$index['name']}` (`{$fields}`)";
        }
        if($index['type'] == 'primary') {
            $piece .= "PRIMARY KEY (`{$fields}`)";
        }
        return $piece;
    }
    
    /**
     * 数据库字段创建
     * @param unknown $field
     */
    public function dbBuildFieldSql($field) {
        
        if(!empty($field['length'])) {
            $length = "({$field['length']})";
        } else {
            $length = '';
        }
        if (strpos(strtolower($field['type']), 'int') !== false || in_array(strtolower($field['type']) , array('decimal', 'float', 'dobule'))) {
            $signed = empty($field['signed']) ? ' unsigned' : '';
        } else {
            $signed = '';
        }
        if(empty($field['null'])) {
            $null = ' NOT NULL';
        } else {
            $null = '';
        }
        if(isset($field['default'])) {
            $default = " DEFAULT '" . $field['default'] . "'";
        } else {
            $default = '';
        }
        if($field['increment']) {
            $increment = ' AUTO_INCREMENT';
        } else {
            $increment = '';
        }
        return "{$field['type']}{$length}{$signed}{$null}{$default}{$increment}";
    }
    
    public function dbTableSchemas($table) {
        $dump = "DROP TABLE IF EXISTS {$table};\n";
        $sql = "SHOW CREATE TABLE {$table}";
        $row = model()->query($sql);
        $dump .= $row['Create Table'];
        $dump .= ";\n\n";
        return $dump;
    }
    
    /**
     * 数据库添加语句
     * @param unknown $tablename
     * @param unknown $start
     * @param unknown $size
     */
    public function dbTableInsertSql($tablename, $start, $size) {
        $data = '';
        $tmp = '';
        $sql = "SELECT * FROM {$tablename} LIMIT {$start}, {$size}";
        $result = model($tablename)->query($sql);
        if (!empty($result)) {
            foreach($result as $row) {
                $tmp .= '(';
                foreach($row as $k => $v) {
                    $value = str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $v);
                    $tmp .= "'" . $value . "',";
                }
                $tmp = rtrim($tmp, ',');
                $tmp .= "),\n";
            }
            $tmp = rtrim($tmp, ",\n");
            $data .= "INSERT INTO {$tablename} VALUES \n{$tmp};\n";
            $datas = array (
                'data' => $data,
                'result' => $result
            );
            return $datas;
        } else {
            return false ;
        }
    }
    

	
}
