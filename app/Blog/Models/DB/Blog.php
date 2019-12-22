<?php

namespace App\Blog\Models\DB;


use App\Blog\Models\SetDBPartition;
use App\Blog\Models\SoftDeletes;
use App\OrmPartition;

class Blog extends OrmPartition
{
    use SoftDeletes;    // 所有Model必须继承

    use SetDBPartition; // 分库分表必须继承
    //默认的链接DB数据库；如果是分库分表操作必须保留此字段,设置为空
    protected $connection = '';
    //设置表名；如果是分库分表操作必须保留此字段,设置为空
    protected $table = '';
    //设置删除时间字段-可选
    const DELETED_AT = 'delete_time';
    //设置分表前缀，供SetDBPartition.setDBTable使用
    const TABLE_PREFIX = 'blog';

}