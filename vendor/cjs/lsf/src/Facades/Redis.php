<?php
namespace CjsLsf\Facades;

class Redis extends Facade
{
    protected static function _getFacadeAccessor()
    {
        return 'redis';
    }
}
