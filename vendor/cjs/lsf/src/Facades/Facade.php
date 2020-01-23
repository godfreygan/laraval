<?php
namespace CjsLsf\Facades;

class Facade
{
    protected static $resolved_instance_;

    public static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::_getFacadeAccessor());
    }

    protected static function getFacadeAccessor_()
    {
        throw new \RuntimeException("Facade does not implement getFacadeAccessor method.");
    }

    protected static function resolveFacadeInstance($name)
    {
        if (is_object($name)) {
            return $name;
        }

        if (isset(static::$resolved_instance_[$name])) {
            return static::$resolved_instance_[$name];
        }

        return static::$resolved_instance_[$name] = \App::make($name);
    }

    public static function clearResolvedInstance($name)
    {
        unset(static::$resolved_instance_[$name]);
    }

    public static function clearResolvedInstances()
    {
        static::$resolved_instance_ = array();
    }

    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();

        switch (count($args)) {
            case 0:
                return $instance->$method();
            case 1:
                return $instance->$method($args[0]);
            case 2:
                return $instance->$method($args[0], $args[1]);
            case 3:
                return $instance->$method($args[0], $args[1], $args[2]);
            case 4:
                return $instance->$method($args[0], $args[1], $args[2], $args[3]);
            default:
                return call_user_func_array(array($instance, $method), $args);
        }
    }
}
