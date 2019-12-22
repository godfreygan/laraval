<?php
/**
 * 这个文件是无命名空间的助手函数
 * 添加此类函数注意影响面
 */

use Carbon\Carbon;
use App\Util\Request;

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     * @author chengjinsheng
     */
    function env($key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            return value($default);
        }
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'null':
            case '(null)':
                return null;
            case 'empty':
            case '(empty)':
                return '';
        }
        return $value;
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     * @return mixed
     * @author chengjinsheng
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('with')) {
    /**
     * Return the given object. Useful for chaining.
     *
     * @param mixed $object
     * @return mixed
     * @author chengjinsheng
     */
    function with($object)
    {
        return $object;
    }
}

if (!function_exists('isNumericInArray')) {
    /**
     * 校验一维数组中值是否均为Numeric类型
     *
     * @param array $arr
     * @return bool
     * @author chengjinsheng
     */
    function isNumericInArray($arr)
    {
        if (!is_array($arr) || empty($arr)) {
            return false;
        }
        foreach ($arr as $v) {
            if (!is_numeric($v)) {
                return false;
            }
        }
        return true;
    }
}

/**
 * 将二维数组的值提取为键
 *
 * @param array $arr 二维数组
 * @param string $str 要作为键的值
 * @return array
 * @author chengjinsheng
 * @date 2016-10-11
 */
function getKeyChange($arr, $str)
{
    $data = [];
    foreach ($arr as $v) {
        $data[$v[$str]] = $v;
    }
    return $data;
}

/**
 * 将二维数组的值提取为键
 *
 * @param array $arr 二维数组
 * @param string $str 要作为键的值
 * @return array
 * @author chengjinsheng
 * @date 2017-09-06
 */
function getFieldAsKey($arr, $field)
{
    $keys  = array_column($arr, $field);
    $aData = array_combine($keys, $arr);

    return $aData;
}

/*
 * 获取指定日期的开始和结束时间戳 默认yesterday
 */
function getStartEndOfDate($sDate = null)
{
    $dt          = $sDate ? Carbon::parse($sDate) : Carbon::yesterday();
    $startOfDate = $dt->startOfDay()->timestamp;
    $endOfDate   = $dt->endOfDay()->timestamp;

    return [
        'startOfDate' => $startOfDate, 'endOfDate' => $endOfDate,
    ];
}

/**
 * 获取指定日期段内每一天的日期
 *
 * @param $sStartDate
 * @param $sEndDate
 *
 * @return array
 *
 * @author  chengjinsheng
 * @version 1.0
 */
function getDateRange($sStartDate, $sEndDate)
{
    $stime    = strtotime($sStartDate);
    $etime    = strtotime($sEndDate);
    $aDateArr = [];
    while ($stime <= $etime) {
        $aDateArr[] = date('Y-m-d', $stime);
        $stime      += 86400;
    }
    return $aDateArr;
}

//获取业务模块名
function getModuleName()
{
    $moduleName = getenv('MODULE_NAME');
    if (!$moduleName) {
        $moduleName = isset($_GET['module_name']) ? ucfirst($_GET['module_name']) : 'Blog';
    }
    if (!$moduleName || !preg_match('/^[a-z0-9_-]+$/i', $moduleName)) {
        $moduleName = 'Blog';
    }
    return ucfirst($moduleName);
}

if (!function_exists('request')) {
    /**
     * 获取当前Request对象实例
     * @return Request
     */
    function request()
    {
        return Request::instance();
    }
}

/**
 * 二维数组排序
 * @param $array        数据数组
 * @param $cond         条件（结构为：array(
 *                                            array(列名1, SORT_ASC/SORT_DESC, SORT_STRING/SORT_NUMERIC),
 *                                            array(列名1, SORT_ASC/SORT_DESC, SORT_STRING/SORT_NUMERIC)
 *                                    )
 *                        第三参数表示按照string还是数字进行排序，可不传，可为空，默认为类型不变进行排序）
 * 示例：resort($array, [['id', SORT_DESC], ['create_time', SORT_ASC]])
 */
if (!function_exists('resort')) {
    function resort($array, $cond)
    {
        if (!is_null($array) && count($array) > 0) {
            if (count($cond) == 0) {        // 如果没有排序条件，直接返回
                return $array;
            } else {
                $sortList = array();            // 排序规则列表
                $typeList = array();            // 排序字段类型列表
                foreach ($cond as $sort) {
                    $sortList[] = !empty($sort[1]) ? $sort[1] : SORT_DESC;
                    $typeList[] = isset($sort[2]) ? $sort[2] : SORT_REGULAR;
                }

                $valueList = array();        // 值列表
                foreach ($cond as $sort) {
                    $columnName = $sort[0];
                    $values     = array();
                    foreach ($array as $index => $row) {
                        $values[] = $row[$columnName];
                    }

                    $valueList[] = $values;
                }

                $args = array();        // 参数列表
                for ($i = 0; $i < count($cond); $i++) {
                    $args[] = &$valueList[$i];
                    $args[] = &$sortList[$i];
                    $args[] = &$typeList[$i];
                }
                $args[] = &$array;
                call_user_func_array('array_multisort', $args);        // 进行排序操作
                return $array;
            }
        } else {
            return array();
        }
    }
}

if (!function_exists('trimArr')) {
    function trimArr($arr)
    {
        $newArr = array_map(function ($item) {
            if (is_array($item)) {
                $v = trimArr($item);
            } elseif (is_string($item)) {
                $v = trim($item);
            } else {
                $v = $item;
            }
            return $v;
        }, $arr);
        return $newArr;
    }
}

if (!function_exists('removeSpecialChars')) {
    function removeSpecialChars(array $arr)
    {
        $newArr = array_map(function ($item) {
            if (is_array($item)) {
                $v = removeSpecialChars($item);
            } else {
                $v = preg_replace('/[^\P{L}\P{N}\s]/u', '', $item);
            }
            return $v;
        }, $arr);
        return $newArr;
    }
}

if (!function_exists('numToWord')) {
    /**
     * 把数字1-1亿换成汉字表述，如：123->一百二十三
     * @param [num] $num [数字]
     * @return [string] [string]
     */
    function numToWord($num)
    {
        $chiNum    = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九');
        $chiUni    = array('', '十', '百', '千', '万', '亿', '十', '百', '千');
        $chiStr    = '';
        $num_str   = (string)$num;
        $count     = strlen($num_str);
        $last_flag = true; //上一个 是否为0
        $zero_flag = true; //是否第一个
        $temp_num  = null; //临时数字
        $chiStr    = '';   //拼接结果
        if ($count == 2) {//两位数
            $temp_num = $num_str[0];
            $chiStr   = $temp_num == 1 ? $chiUni[1] : $chiNum[$temp_num] . $chiUni[1];
            $temp_num = $num_str[1];
            $chiStr   .= $temp_num == 0 ? '' : $chiNum[$temp_num];
        } else {
            if ($count > 2) {
                $index = 0;
                for ($i = $count - 1; $i >= 0; $i--) {
                    $temp_num = $num_str[$i];
                    if ($temp_num == 0) {
                        if (!$zero_flag && !$last_flag) {
                            $chiStr    = $chiNum[$temp_num] . $chiStr;
                            $last_flag = true;
                        }
                    } else {
                        $chiStr    = $chiNum[$temp_num] . $chiUni[$index % 9] . $chiStr;
                        $zero_flag = false;
                        $last_flag = false;
                    }
                    $index++;
                }
            } else {
                $chiStr = $chiNum[$num_str[0]];
            }
        }
        return $chiStr;
    }

}