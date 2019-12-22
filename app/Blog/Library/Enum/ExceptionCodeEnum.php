<?php

namespace App\Blog\Library\Enum;


/**
 * Class ExceptionCodeEnum
 * 定义第三方错误代码
 * @package App\Blog\Library\Enum
 */
class ExceptionCodeEnum extends Enum
{
    // 响应给对接方code值汇总 0~10000
    const SUCCESS          = 0;                     //成功返回代码
    const FAIL             = 1;                     //操作错误代码 新增/编辑/删除等操作失败
    const INVALID_ARGUMENT = 2;                     //参数错误
    const DATA_EXIST       = 3;                     //数据已存在 用户/地址等数据存在
    const DATA_NOT_EXIST   = 4;                     //数据不存在 用户/地址等数据不存在

    // 用于内部打印log日志的常量错误代号
    const HANDEL_FAIL = 9999999;                    //操作失败 适用于 接口返回true/false接口 false时异常
}