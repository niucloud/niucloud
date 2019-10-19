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

use think\Db;
use think\Validate;

/**
 * 模型基类
 */
class Model
{
    
    // 查询对象
    private static $query_obj = null;
    //验证规则
    protected $rule = [];
    //验证信息
    protected $message = [];
    //验证场景
    protected $scene = [];
    //错误信息
    protected $error;
    
    public function __construct($table = '') {
        if ($table) {
            $this->table = $table;
        }
    }
    
    /**
     * 获取列表数据
     * @param array $where
     * @param string $field
     * @param string $order
     * @param number $page
     * @param array $join
     * @param string $group
     * @param string $limit
     * @param string $data
     * @return mixed
     */
    final public function getList($where = [], $field = true, $order = '', $alias = 'a', $join = [], $group = '', $limit = null)
    {
        self::$query_obj = Db::name($this->table)->where($where)->order($order);
        
        if(!empty($join)){
            self::$query_obj->alias($alias);
            self::$query_obj = self::$query_obj->join($join);
        }
        
        if(!empty($group)){
            self::$query_obj = self::$query_obj->group($group);
        }
        
        if(!empty($limit)){
            self::$query_obj = self::$query_obj->limit($limit);
        }
        
        $result = self::$query_obj->field($field)->select();
        
        self::$query_obj->removeOption();
        return $result;
    }
    
    final public function all(){
        return Db::name($this->table)->select();
    }
    
    /**
     * 获取分页列表数据
     * @param unknown $where
     * @param string $field
     * @param string $order
     * @param number $page
     * @param string $list_rows
     * @param string $alias
     * @param unknown $join
     * @param string $group
     * @param string $limit
     */
    final public function pageList($where = [], $field = true, $order = '', $page = 1, $list_rows = PAGE_LIST_ROWS, $alias = 'a', $join = [], $group = null, $limit = null){
        self::$query_obj = Db::name($this->table)->where($where)->order($order);
        if(!empty($join)){
            self::$query_obj->alias($alias);
            self::$query_obj = self::$query_obj->join($join);
        }
        
        if(!empty($group)){
            self::$query_obj = self::$query_obj->group($group);
        }
        
        if(!empty($limit)){
            self::$query_obj = self::$query_obj->limit($limit);
        }
        
        $count = Db::name($this->table)->alias($alias)->join($join)->where($where)->group($group)->count();
        if($list_rows == 0){
            //查询全部
            $result_data = self::$query_obj->field($field)->limit($count)->page($page)->select();
            $result['page_count'] = 1;
        }else{
            $result_data = self::$query_obj->field($field)->limit($list_rows)->page($page)->select();
            $result['page_count'] = ceil($count/$list_rows);
        }
        $result['count'] = $count;
        $result['list'] = $result_data;

        
        self::$query_obj->removeOption();
        return $result;
    }
    
    
    /**
     * 获取单条数据
     * @param array $where
     * @param string $field
     * @param string $join
     * @param string $data
     * @return mixed
     */
    final public function getInfo($where = [], $field = true, $alias = 'a', $join = null, $data = null)
    {
        if(empty($join)){
            $result = Db::name($this->table)->where($where)->field($field)->find($data);
        }else{
            $result = Db::name($this->table)->alias($alias)->join($join)->where($where)->field($field)->find($data);
        }
    
        return $result;
    }
    
    /**
     * /**
     * 获取某个列的数组
     * @param array  $where 条件
     * @param string $field 字段名 多个字段用逗号分隔
     * @param string $key   索引
     * @return array
     */
    final public function getColumn($where = [], $field = '', $key = '')
    {
        return Db::name($this->table)->where($where)->column($field, $key);
    }
    
    /**
     * 得到某个字段的值
     * @access public
     * @param array  $where 条件
     * @param string $field   字段名
     * @param mixed  $default 默认值
     * @param bool   $force   强制转为数字类型
     * @return mixed
     */
    final public function getValue($where = [], $field = '', $default = null, $force = false)
    {
        return Db::name($this->table)->where($where)->value($field, $default, $force);
    }
    
    /**
     * 新增数据
     * @param array $data 数据
     * @param boolean $is_return_pk 返回自增主键
     */
    final public function add($data = [], $is_return_pk = true)
    {
        return Db::name($this->table)->insert($data, false, $is_return_pk);
    }
    
    /**
     * 新增多条数据
     * @param array $data 数据
     * @param int $limit 限制插入行数
     */
    final public function addList($data = [], $limit = null)
    {
        return Db::name($this->table)->insertAll($data, false, $limit);
    }
    
    /**
     * 更新数据
     * @param array $where 条件
     * @param array $data 数据
     */
    final public function update($data = [], $where = [])
    {
        return Db::name($this->table)->where($where)->update($data);
    }
    
    /**
     * 设置某个字段值
     * @param array $where 条件
     * @param string $field 字段
     * @param string $value 值
     */
    final public function setFieldValue($where = [], $field = '', $value = '')
    {
        return $this->update([$field => $value], $where);
    }
    
    /**
     * 设置数据列表
     * @param array   $data_list 数据
     * @param boolean $replace 是否自动识别更新和写入
     */
    final public function setList($data_list = [], $replace = false)
    {
        return Db::name($this->table)->saveAll($data_list, $replace);
    }
    
    /**
     * 删除数据
     * @param array $where 条件
     */
    final public function delete($where = [])
    {
        return Db::name($this->table)->where($where)->delete();
    }
    
    /**
     * 统计数据
     * @param array $where 条件
     * @param string $type 查询类型  count:统计数量|max:获取最大值|min:获取最小值|avg:获取平均值|sum:获取总和
     */
    final public function stat($where = [], $type = 'count', $field = 'id')
    {
        return Db::name($this->table)->where($where)->$type($field);
    }

    /**
     * SQL查询
     */
    final public function query($sql = '')
    {
        
        return Db::query($sql);
    }
    
    /**
     * 返回总数
     * @param unknown $where
     */
    final public function getCount($where = [], $field = '*')
    {
        return Db::name($this->table)->where($where)->count($field);
    }
    
    /**
     * 返回总数
     * @param unknown $where
     */
    final public function getSum($where = [], $field = '')
    {
        return Db::name($this->table)->where($where)->sum($field);
    }
    
    /**
     * SQL执行
     */
    final public function execute($sql = '')
    {
        return Db::execute($sql);
    }
    
    /**
     * 查询第一条数据
     * @param array $condition
     */
    final function getFirstData($condition, $field='*',$order="" )
    {
        $data = Db::table($this->table)->where($condition)->order($order)->field($field)->find();
        return $data;
    }
    
    /**
     * 验证
     * @param array $data
     * @param string $scene_name
     * @return array[$code, $error]
     */
    public function fieldValidate($data, $scene_name = ''){
        $validate = new Validate($this->rule, $this->message);
        
        if(empty($scene_name)){
            $validate_result = $validate->batch(false)->check($data);
        }else{
            $validate->scene($this->scene);
            $validate_result = $validate->scene($scene_name)->batch(false)->check($data);
        }
        
        return $validate_result ? [true, ''] : [false, $validate->getError()];
    }
    
    /**
     * 事物开启
     */
    final public function startTrans(){
        
        return Db::startTrans();
    }
    
    /**
     * 事物提交
     */
    final public function commit(){
        
        return Db::commit();
    }
    
    /**
     * 事物回滚
     */
    final public function rollback(){
        
        return Db::rollback();
    }
    
    /**
     * 获取错误信息
     */
    final public function getError(){
        return $this->error;
    }
    
    /**
     * 自增数据
     * @param array $where
     * @param string $field
     * @param int $num
     */
    final public function setInc($where = [], $field, $num = 1)
    {
        return Db::name($this->table)->where($where)->setInc($field, $num);
    }
    /**
     * 自减数据
     * @param array $where
     * @param string $field
     * @param int $num
     */
    final public function setDec($where = [], $field, $num = 1)
    {
        return Db::name($this->table)->where($where)->setDec($field, $num);
    }

    /**
     * 获取最大值
     * @param array $where
     * @param $field
     * @return mixed
     */
    final public function getMax($where = [], $field){

        return Db::name($this->table)->where($where)->max($field);
    }
}
