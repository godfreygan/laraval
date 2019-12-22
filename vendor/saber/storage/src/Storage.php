<?php

namespace Saber\Storage;

use InvalidArgumentException;
use Saber\Storage\Driver\Qiniu;

class Storage
{
    private static $instance;

    /**
     * Storage constructor.
     * @param array $config
     * @return Qiniu
     */
    public static function init($config)
    {
        if (is_null(self::$instance)) {
            if (empty($config['driver'])) {
                throw new InvalidArgumentException('no driver');
            }

            $driver = __NAMESPACE__ . '\\Driver\\' . ucfirst($config['driver']);
            self::$instance = new $driver($config['config'][$config['driver']]);
        }

        return self::$instance;
    }
}