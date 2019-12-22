<?php

namespace App\Blog\Library\Util;
/**
 * 每个分库分表策略必须继承 \App\Util\DBUtilBase，且定义2个属性
 */
class DBUtil extends \App\Util\DBUtilBase
{
    protected $db_num    = 2;  //分库总数
    protected $table_num = 10; //分表总数

    /**
     * @title: 根据数值返回对应 库名前缀 和 表名前缀
     * @param $num      int     分库分表主要参数
     * @return array    array   db_key-库名后缀，tbl_key 表名后缀
     * @throws \Exception
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public function getDBBaseByNum($num)
    {
        return parent::getDBBaseByNum($num);
    }

    /**
     * 主键 seq获取分库分表规则
     * @param $seq
     * @return array
     * @throws \Exception
     */
    public function getDBBaseBySeq($seq)
    {
        $index = substr($seq, -3, 3);
        if (!is_numeric($index)) {
            throw  new \Exception("分库分表参数异常", 99000001);
        }
        return $this->getDBBaseByNum($index);
    }
}
