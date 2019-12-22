<?php

namespace App\Blog\Models\DB;

use App\Orm;
use App\Blog\Models\SoftDeletes;

class Dict extends Orm
{
    use SoftDeletes;

    //默认的链接DB数据库
    protected $connection = 'db_blog_1';
    //表名
    protected $table = 'dict';

    //设置删除时间字段
    const DELETED_AT = null;
}
