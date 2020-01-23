<?php
/**
 * FlakeId 全局唯一ID生成器
 *
 */

namespace LightTracer\Util;

class FlakeId
{
    /** 生成128bit的全局唯一ID
     * @return string 32位字符串
     */
    public static function generate()
    {
        return flakeid_generate(false, '');
    }

    /** 生成64bit的全局唯一ID
     * @return string 16位字符串
     */
    public static function generate64()
    {
        return flakeid_generate64(false);
    }

    public static function nextSeq($span = 0)
    {
        return flakeid_next_seq($span);
    }

    public static function getMac()
    {
        return flakeid_get_mac();
    }

    public static function getIpHex($ip = null, $raw = false)
    {
        if (is_null($ip)) {
            $ip = self::getIpv4();
        }

        $ip_parts = explode('.', $ip);
        if ($raw) {
            return pack(
                'C4',
                intval($ip_parts[0]),
                intval($ip_parts[1]),
                intval($ip_parts[2]),
                intval($ip_parts[3])
            );
        } else {
            return sprintf(
                '%02x%02x%02x%02x',
                intval($ip_parts[0]),
                intval($ip_parts[1]),
                intval($ip_parts[2]),
                intval($ip_parts[3])
            );
        }
    }

    public static function getIpv4()
    {
        $ip = self::getServerAddress();
        if (preg_match('/^\d+\.\d+\.\d+\.\d+$/', $ip)) {
            return $ip;
        } elseif (function_exists('flakeid_get_ipv4')) {
            $ipv4_raw   = flakeid_get_ipv4(true);
            $ipv4_parts = unpack('C*', $ipv4_raw);
            return implode('.', $ipv4_parts);
        } else {
            \LightTracer\error_log('can not get ipv4 address');
            return '0.0.0.0';
        }
    }

    private static function getServerAddress()
    {
        if (array_key_exists('SERVER_ADDR', $_SERVER)) {
            return $_SERVER['SERVER_ADDR'];
        } elseif (array_key_exists('LOCAL_ADDR', $_SERVER)) {
            return $_SERVER['LOCAL_ADDR'];
        } elseif (array_key_exists('SERVER_NAME', $_SERVER)) {
            $ip = gethostbyname($_SERVER['SERVER_NAME']);
            if ($ip) {
                return $ip;
            }
        }

        return gethostbyname(gethostname());
    }

    public static function generateGUID()
    {
        $data    = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return bin2hex($data);
    }

    public static function generateGUID64()
    {
        $data = openssl_random_pseudo_bytes(8);
        return bin2hex($data);
    }
}
