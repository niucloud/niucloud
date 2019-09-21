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

namespace app\common\validate;
use think\Validate;

class Addons extends Validate
{
    protected $rule = [
        'name' => 'require',
        'title' => 'require',
        'description' => 'require',
    ];
    
    protected $message = [
        'name.require' => '标识不能为空',
        'title.require' => '中文名不能为空',
        'description.require' => '简介不能为空',
    ];
    
}
