<?php

namespace App\Blog\Models;

use App\Blog\Library\Enum\DatabaseEnum;
use App\Blog\Library\Util\DBUtil;

trait SetDBPartition
{

    /**
     * 设置分库分表，在继承OrmPartition的Model中使用
     * @param $user_id
     *
     * @return $this
     * @throws \Exception
     */
    public function setDBTable($user_id, $partition_type = 0)
    {
        if ($partition_type == 1) {
            $dbInfo = DBUtil::getInstance()->getDBBaseByStr($user_id);
        } else {
            $dbInfo = DBUtil::getInstance()->getDBBaseByNum($user_id);
        }
        $db_name          = DatabaseEnum::BLOG_DB . $dbInfo['db_key'];//算法计算出具体的DB
        $table_name       = self::TABLE_PREFIX . $dbInfo['tbl_key'];  //算法计算出具体的表
        $this->connection = $db_name;
        $this->table      = $table_name;
        return $this;
    }
}