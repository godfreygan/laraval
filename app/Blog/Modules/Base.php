<?php

namespace App\Blog\Modules;

/**
 * 所有用户业务模板 继承的类
 */
abstract class Base
{

    /**
     * 返回结果类型
     */
    const FORMAT_OBJECT = 'object'; //对象
    const FORMAT_ARRAY  = 'array';  //数组

    /**
     * 设置默认的分页常量
     */
    const PAGE_NO = 1;//默认页码

    const PAGE_SIZE = 10;//默认每页条数
}
