<?php

namespace App\Blog\Library\Util;


class DictUtil
{
    /**
     * @title: 批量数据格式化
     * @param $list array    请求参数
     * @return array
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public static function formatList($list)
    {
        $result = [];
        foreach ($list as $item) {
            $result[$item['type']][$item['code']] = $item['value'];
        }
        return $result;
    }

    /**
     * @title: 格式化数据
     * @param $list array   请求参数
     * @return array
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public static function formatUtilList($list)
    {
        $result = [];
        foreach ($list as $key => $value) {
            foreach ($value as $v) {
                $result[$v['type']][$v['code']] = $v['value'];
            }
        }
        return $result;
    }

    /**
     * @title: 单个数据格式化
     * @param $list array   请求参数
     * @return array
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public static function formatOneList($list)
    {
        $result = [];
        foreach ($list as $item) {
            $result[$item['code']] = $item['value'];
        }
        return $result;
    }

    /**
     * @title: 数据组装
     * @param $param    array   参数
     * @return array
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public static function formatArrCut($param)
    {
        $ret = [];
        foreach ($param as $value) {
            $ret[$value['type']] [] = $value;
        }
        return $ret;
    }

    /**
     * @title: 字典子集格式化
     * @param $arrList  array   请求参数
     * @param $parent   integer 父级ID
     * @param $son      integer 子即ID
     * @param $keyName  string  新数组的key
     * @return mixed
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public static function formatArr($arrList, $parent, $son, $keyName)
    {
        foreach ($arrList as $key => $val) {
            foreach ($val as $k => $v) {
                if ($v[$parent] == 0) {
                    $parArray[$key][] = $v;
                }
            }
        }
        foreach ($parArray as $pKey => $pVal) {
            foreach ($pVal as $key => $val) {
                foreach ($arrList as $aKey => $aVal) {
                    foreach ($aVal as $sKey => $sVal) {
                        if ($sVal[$parent] === $val[$son]) {
                            $parArray[$pKey][$key][$keyName][] = $sVal;
                        } else {
                            $parArray[$pKey][$key][$keyName] = [];

                        }
                    }
                }
            }
        }
        return $parArray;
    }
}