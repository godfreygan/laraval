<?php
/**
 * default global eventemitter facade
 *
 * @author yuanbaoju
 */

namespace LightService;

use LightService\Util\EventEmitter;

class DefaultEventEmitter
{
    private static $instance;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new EventEmitter;
        }

        return self::$instance;
    }

    public static function __callStatic($name, $args)
    {
        return call_user_func_array([self::getInstance(), $name], $args);
    }
}
