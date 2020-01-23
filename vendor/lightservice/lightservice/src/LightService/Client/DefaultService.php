<?php
/**
 * default global service facade
 *
 * @author yuanbaoju
 */

namespace LightService\Client;

class DefaultService
{
    private static $instance;

    public static function init($opts)
    {
        self::$instance = new Service($opts);
    }

    public function __callStatic($name, $args)
    {
        return self::$instance->$name(...$args);
    }
}
