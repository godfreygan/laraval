<?php

namespace App\Blog\Models;

use App\Blog\Library\Enum\DatabaseEnum;
use App\Orm;

class Sequence extends Orm
{

    //默认的链接DB数据库
    protected $connection = DatabaseEnum::BLOG_DB_1;
    //表名
    protected $table      = 'sequence';
    protected $primaryKey = 'id';
    //设置删除时间字段
    const DELETED_AT = null;
    const UPDATED_AT = null;
}