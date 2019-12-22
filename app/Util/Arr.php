<?php

namespace App\Util;

/*
 * 数组处理相关类
 */

class Arr
{
    /**
     * @title: Get an item from an array using "dot" notation.
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public static function get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * @title: 取二维数组的子数组的一个元素作为键名
     * @param array $aArray 传入的二维数组
     * @param string $sFiled 需要作为key的字段
     * @return array
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public static function useFieldAsKey($aArray, $sFiled)
    {
        if (!is_array($aArray) || !count($aArray) || !is_array(current($aArray))) {
            return $aArray;
        }
        $aResult = array();
        foreach ($aArray as $v) {
            $aResult[$v[$sFiled]] = $v;
        }
        return $aResult;
    }
}
