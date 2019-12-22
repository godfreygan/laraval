<?php

namespace App\Blog\Library\Enum;

/**
 * Class RedisKeyEnum
 * redis key前缀常量
 * @package App\Blog\Library\Enum
 */
class RedisKeyEnum extends Enum
{
    const BLOG_DICT_KEY           = 'blog:%s';                         // 字典列表
    const BLOG_JOB_CONTROL_KEY    = 'blog:job:%s';                     // 脚本控制
    const CONFIG_PAYMENT_INFO_KEY = 'config:payment:%s';               // 支付平台信息

}
