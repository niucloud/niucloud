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

/**
 * 验证器 基类
 */
class BaseValidate extends Validate
{

    /**
     * 获取数据层实例
     */
    public function __get($name)
    {
    
        !str_prefix($name, LAYER_MODEL_NAME) && exception('数据层引用需前缀:' . LAYER_MODEL_NAME);
    
        return model(str_replace(LAYER_MODEL_NAME, '', $name), LAYER_MODEL_NAME);
    }
}
