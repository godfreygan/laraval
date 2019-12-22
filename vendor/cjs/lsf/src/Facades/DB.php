<?php
namespace CjsLsf\Facades;

class DB extends Facade
{
    protected static function _getFacadeAccessor()
    {
        return 'database';
    }
}
