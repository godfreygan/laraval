<?php

namespace App\Util;

/**
 * db 分库分表算法类
 */
abstract class DBUtilBase
{
    protected $db_num    = 1;   //总共分多少库数
    protected $table_num = 16;  //一个库总共分多少表数
    const PREFIX       = "_";
    const DATABASEKEY  = "db_key";
    const DATATABLEKEY = "tbl_key";

    protected function __construct()
    {

    }

    public static function getInstance()
    {
        static $instance;
        if (!$instance) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * @title: 根据数值返回对应 库名前缀 和 表名前缀
     * @param $num      int     分库分表主要参数
     * @return array    array   db_key-库名后缀，tbl_key 表名后缀
     * @throws \Exception
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public function getDBBaseByNum($num)
    {
        if ($num < 0 || !is_numeric($num)) {
            throw  new \Exception("分库分表参数异常", 99000001);
        }
        $num = intval($num);
        if ($num > 128) {
            $num = $num % 128;
        }

        $database = intval($num / $this->db_num) % $this->db_num + 1;
        $table    = $num % $this->table_num + 1;
        return [
            self::DATABASEKEY  => self::PREFIX . $database,
            self::DATATABLEKEY => self::PREFIX . $table
        ];
    }

    /**
     * @title: 字符串hash
     * @param $str
     * @return array
     * @throws \Exception
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public function getDBBaseByStr($str)
    {
        if ($str == null || $str == "") {
            throw  new \Exception("分库分表参数异常", 99000001);
        }
        $n = $this->getHashOrd($str);
        return $this->getDBBaseByNum($n);
    }

    /**
     * @title: 获取字符串hash
     * @param $str  string  对应10进制总和
     * @return int
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    protected function getHashOrd($str)
    {
        $n = 0;
        if (is_numeric($str)) { //是数值型，则后面直接取模
            $n = intval($str);
        } else {
            $str = trim($str . '');
            $len = mb_strlen($str);
            for ($i = 0; $i < $len; $i++) {
                $n += ord($str[$i]);
            }
        }
        $res = $n % 128;
        return $res;
    }
}
