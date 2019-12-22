<?php

namespace LightTracer\Util;

class Time
{
    /**
     * 当前时间戳，整形，单位微秒
     */
    public static function microNow()
    {
        return floor(microtime(true) * 1000 * 1000);
    }

    /**
     * 生成 CAT的时间格式
     * @param int $us 微秒
     * @return string
     */
    public static function catTime($us)
    {
        $time = floor($us / 1000 / 1000);
        return date('Y-m-d H:i:s.', $time) . (floor($us / 1000) % 1000);
    }
}
