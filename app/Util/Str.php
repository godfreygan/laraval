<?php namespace App\Util;

/**
 * 字符串处理相关类
 *
 * @author chengjinsheng
 * @date 2016-10-11
 */
class Str
{
    /**
     * 判断数据类型是否正确
     *
     * @param mix $p_mData 检测数据源
     * @param string $p_sDataType 匹配数据类型
     * @return bool
     * @author chengjinsheng
     */
    public static function chkDataType($p_mData, $p_sDataType)
    {
        if ('' == $p_mData) {
            return false;
        }
        switch ($p_sDataType) {
            case 'i':
                return 0 < preg_match('/^-?[1-9]?[0-9]*$/', $p_mData) ? true : false;
            case 'url':
                return 0 < preg_match('/^https?:\/\/([a-z0-9-]+\.)+[a-z0-9]{2,4}.*$/', $p_mData) ? true : false;
            case 'email':
                return 0 < preg_match('/^[a-z0-9_+.-]+\@([a-z0-9-]+\.)+[a-z0-9]{2,4}$/i', $p_mData) ? true : false;
            case 'idcard':
                return 0 < preg_match('/^[0-9]{15}$|^[0-9]{17}[a-zA-Z0-9]/', $p_mData) ? true : false;
            case 'area':
                return 0 < preg_match('/^\d+(\.\d{1,2})?$/', $p_mData) ? true : false;
            case 'money':
                return 0 < preg_match('/^\d+(\.\d{1,2})?$/', $p_mData) ? true : false;
            case 'length':
                return 0 < preg_match('/^\d+(\.\d{1,2})?$/', $p_mData) ? true : false;
            case 'mobile':
                return 0 < preg_match("/^((1[3-9][0-9])|200)[0-9]{8}$/", $p_mData) ? true : false;
            case 'phone':
                return 0 < preg_match('/^(\d{3,4}-?)?\d{7,8}$/', $p_mData) ? true : false;
            case 'chinese':
                return 0 < preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", $p_mData) ? true : false;
            default:
                return false;
        }
    }

    /*
     * 获取随机字符串
     *
     * @author chengjinsheng
     * @param int $p_iLength 随机字符串长度
     * @param int $p_iStyle 随机字符要求：0-number, 1-alpha, 2-all
     * @return string
     */
    public static function getRand($p_iLength, $p_iStyle = 0)
    {
        $sTmp    = '0123456789abcdefghijklmnopqrstuvwxyz';
        $sReturn = '';
        switch ($p_iStyle) {
            case 0:
                $iStart = 0;
                $iEnd   = 9;
                break;
            case 1:
                $iStart = 10;
                $iEnd   = 35;
                break;
            case 2:
                $iStart = 0;
                $iEnd   = 35;
                break;
            default:
                $iStart = 0;
                $iEnd   = 9;
        }
        for ($i = 0; $i < $p_iLength; $i++) {
            $sReturn .= substr($sTmp, rand($iStart, $iEnd), 1);
        }
        return $sReturn;
    }

    /**
     * 检查字符串长度
     *
     * @param string $p_sData 字符串
     * @param int $p_iMinLength 最小长度
     * @param int $p_iMaxLength 最大长度
     * @param bool $p_bMultiByte 是否区分中英文字符串
     * @return bool
     * @author chengjinsheng
     */
    public static function chkStrLength($p_sData, $p_iMinLength = 0, $p_iMaxLength = 0, $p_bMultiByte = false)
    {
        if ($p_bMultiByte) {
            $iLen = strlen($p_sData);
        } else {
            $iLen = mb_strlen($p_sData);
        }
        if ($p_iMinLength > 0) {
            if ($p_iMinLength > $iLen) {
                return false;
            }
        }
        if ($p_iMaxLength > 0) {
            if ($p_iMaxLength < $iLen) {
                return false;
            }
        }
        return true;
    }

    /**
     * 截取字符串
     *
     * @param string $p_sData 原始字符串
     * @param int $p_iLength 截取长度
     * @param string $p_sSubfix 省略符
     * @param bool $p_bMultiByte 是符含中文等字符
     * @return string
     * @author chengjinsheng
     */
    public static function subStr($p_sData, $p_iLength, $p_sSubfix = '...', $p_bMultiByte = false)
    {
        if ($p_bMultiByte) {
            if (strlen($p_sData) > $p_iLength) {
                return substr($p_sData, 0, $p_iLength - strlen($p_sSubfix)) . $p_sSubfix;
            } else {
                return $p_sData;
            }
        } else {
            if (mb_strlen($p_sData) > $p_iLength) {
                return mb_substr($p_sData, 0, $p_iLength - mb_strlen($p_sSubfix)) . $p_sSubfix;
            } else {
                return $p_sData;
            }
        }
    }

    /**
     * 获取$first/($first+$second) 相除后，省去小数点2位后的数值，不是四舍五入哦
     * \App\Util\Str::getNum2(1, 2);
     * @param $first
     * @param $second
     * @return float|int
     * @author chengjinsheng
     */
    public static function getNum2($first, $second)
    {
        if (!$first) {
            return 0;
        }
        if (!$second) {
            $second = 0;
        }
        $v = $first / ($first + $second);
        $v = floor($v * 100);
        return $v / 100;
    }

    /**
     * 使用星号替换字符串
     * @param $str 原始字符串
     * @param int $firstLength 保留开始字符串长度
     * @param int $endLength 保留结尾字符串长度
     * @param int $replaceLength 加密串长度
     * @param string $replace 加密串
     * @return string
     */
    public static function getShowReplaceStr($str, $firstLength = 4, $endLength = 4, $replaceLength = 4, $replace = '*')
    {
        if (empty($str)) {
            return '';
        }
        $firstStr = mb_substr($str, 0, $firstLength);
        $endStr   = mb_substr($str, -$endLength, $firstLength);
        $strPad   = str_pad('', $replaceLength, $replace);
        return $firstStr . $strPad . $endStr;
    }
}
