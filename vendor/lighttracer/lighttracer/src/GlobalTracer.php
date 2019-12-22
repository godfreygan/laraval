<?php

namespace LightTracer;

class GlobalTracer
{
    protected static $global_tracer = null;

    public static function init($conf = [])
    {
        self::$global_tracer = Factory::createTracer($conf);
        return self::$global_tracer;
    }

    public static function setGlobalTracer($tracer)
    {
        $old_tracer          = self::$global_tracer;
        self::$global_tracer = $tracer;

        return $old_tracer;
    }

    public static function __callStatic($name, array $arguments)
    {
        if (is_null(self::$global_tracer)) {
            self::initGlobalTracer();
        }

        return call_user_func_array([self::$global_tracer, $name], $arguments);
    }
}
