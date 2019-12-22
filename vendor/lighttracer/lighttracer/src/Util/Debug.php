<?php

namespace LightTracer\Util;

class Debug
{
    private static $debug = false;

    public static function setDebug($debug)
    {
        self::$debug = $debug;
    }

    public static function log($msg)
    {
        if (!self::$debug) {
            return false;
        }

        if (!is_string($msg)) {
            $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
        }

        echo "\n>>>[TRACER_DEBUG] $msg\n";

        return true;
    }

    public static function isDebug()
    {
        return self::$debug;
    }
}
