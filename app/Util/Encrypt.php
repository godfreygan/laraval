<?php

namespace App\Util;


class Encrypt
{
    /**
     * @title: 加密密码
     * @param string $sPassword 待加密密码
     * @param string $sSalt 加密盐值
     * @return string 加密后结果
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public static function passwordHash($sPassword, $sSalt)
    {
        if (empty($sPassword) || empty($sSalt)) {
            return '';
        }
        $sHash = md5("blog-" . $sPassword);
        $sHash = md5("blog-" . $sHash . $sSalt);
        return $sHash;
    }

    /**
     * @title: 验证密码
     * @param string $sPassword 待验证的密码
     * @param string $sHash 加密后的哈希值
     * @param string $sSalt 加密盐值，已经在db中存好的sSalt
     * @return bool
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public static function passwordVerify($sPassword, $sHash, $sSalt = '')
    {
        $sPassword = self::passwordHash($sPassword, $sSalt);
        if ($sPassword == $sHash) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @title: 生成加密盐值
     * @return string
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public static function generateSalt($length = NULL)
    {
        if (is_null($length) || !is_numeric($length) || $length < 4)
            $length = mt_rand(4, 32);
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $salt  = "";
        for ($i = 0; $i < $length; $i++) {
            $salt .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $salt;
    }

}