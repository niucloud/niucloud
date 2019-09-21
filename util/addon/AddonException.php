<?php

namespace util\addon;

use think\Exception;

/**
 * 插件异常处理类
 */
class AddonException extends Exception
{

    public function __construct($message, $code, $data = '')
    {
        $this->message  = $message;
        $this->code     = $code;
        $this->data     = $data;
    }

}
