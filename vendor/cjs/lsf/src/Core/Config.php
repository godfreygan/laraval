<?php
namespace CjsLsf\Core;

class Config
{
    private static $_config = [];

    public static function get($key = null, $default = null)
    {
        if (!isset($key)) {
            return self::$_config;
        }

        $pieces = explode('.', $key);
        $count = count($pieces);
        $cur = self::$_config;
        $ret = $default;

        foreach ($pieces as $i => $piece) {
            if (is_array($cur) && array_key_exists($piece, $cur)) {
                $cur = $cur[$piece];

                if ($i === $count - 1) {
                    $ret = $cur;
                }
            } else {
                break;
            }
        }

        return $ret;
    }

    public static function set($key, $val)
    {
        $pieces = explode('.', $key);
        $config = [];
        $cur    = &$config;

        foreach ($pieces as $i => $piece) {
            $cur[$piece] = array();
            $cur = &$cur[$piece];
        }

        $cur = $val;
        self::$_config = array_replace_recursive(self::$_config, $config);
    }

    public static function has($key)
    {
        $pieces = explode('.', $key);
        $count  = count($pieces);
        $cur = self::$_config;
        $ret = false;

        foreach ($pieces as $i => $piece) {
            if (is_array($cur) && array_key_exists($piece, $cur)) {
                $cur = $cur[$piece];

                if ($i === $count - 1) {
                    $ret = true;
                }
            } else {
                break;
            }
        }

        return $ret;
    }

    public static function merge($config, $key = null)
    {
        if (isset($key)) {
            $pieces = explode('.', $key);
            $conf = [];
            $cur  = &$conf;

            foreach ($pieces as $i => $piece) {
                $cur[$piece] = array();
                $cur = &$cur[$piece];
            }

            $cur = $config;
            self::$_config = array_merge_recursive(self::$_config, $conf);
        } else {
            self::$_config = array_merge_recursive(self::$_config, $config);
        }
    }

    public static function replace($config, $key = null)
    {
        if (isset($key)) {
            $pieces = explode('.', $key);
            $conf = [];
            $cur  = &$conf;

            foreach ($pieces as $i => $piece) {
                $cur[$piece] = array();
                $cur = &$cur[$piece];
            }

            $cur = $config;
            self::$_config = array_replace_recursive(self::$_config, $conf);
        } else {
            self::$_config = array_replace_recursive(self::$_config, $config);
        }
    }

    public static function load($file, $key = null)
    {
        $ext = pathinfo($file, PATHINFO_EXTENSION);

        switch ($ext) {
            case 'php':
                self::loadPhp($file, $key);
                break;
            case 'ini':
                self::loadIni($file, $key);
                break;
            case 'json':
                self::loadJson($file, $key);
                break;
        }
    }

    public static function loadJson($file, $key = null)
    {
        $json = file_get_contents($file);
        $config = json_decode($json);
        self::replace($config, $key);
    }

    public static function loadIni($file, $key = null)
    {
        $config = parse_ini_file($file, true);
        self::replace($config, $key);
    }

    public static function loadPhp($file, $key = null)
    {
        $config = require($file);
        self::replace($config, $key);
    }
}
