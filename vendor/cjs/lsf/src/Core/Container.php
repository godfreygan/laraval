<?php
namespace CjsLsf\Core;

/**
 * DI container
 */
class Container
{
    private static $_di;

    private static function _di()
    {
        if (!isset(self::$_di)) {
            $builder = new \DI\ContainerBuilder();
            $builder->useAutowiring(true);
            $builder->useAnnotations(false);
            self::$_di = $builder->build();
        }

        return self::$_di;
    }

    public static function bind($type, $closure)
    {
        self::_di()->set($type, \DI\factory($closure)->scope(\DI\Scope::PROTOTYPE));
    }

    public static function singleton($type, $closure)
    {
        self::_di()->set($type, \DI\factory($closure)->scope(\DI\Scope::SINGLETON));
    }

    public static function instance($type, $instance)
    {
        self::_di()->set($type, \DI\value($instance));
    }

    public static function make($type)
    {
        return self::_di()->get($type);
    }
}
