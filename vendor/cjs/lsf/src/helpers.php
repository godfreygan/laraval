<?php
namespace CjsLsf;

use App;
use Config;

function config_path($path)
{
    return App::configPath() . ($path ? '/' . $path : $path);
}

function storage_path($path)
{
    return App::storagePath() . ($path ? '/' . $path : $path);
}

function public_path($path)
{
    return App::publicPath() . ($path ? '/' . $path : $path);
}

function app_path($path)
{
    return App::appPath() . ($path ? '/' . $path : $path);
}

function base_path($path)
{
    return App::basePath() . ($path ? '/' . $path : $path);
}

function config($key = null, $default = null)
{
    if (is_null($key)) {
        return App::make('config');
    }

    if (is_array($key)) {
        return Config::merge($key);
    }

    return Config::get($key, $default);
}

function env($key, $default = null)
{
    $value = getenv($key);

    if (false === $value) {
        return $default;
    }

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;

        case 'false':
        case '(false)':
            return false;

        case 'empty':
        case '(empty)':
            return '';

        case 'null':
        case '(null)':
            return;
    }

    $len = strlen($value);

    if ($len > 0 && '"' === $value[0] && '"' === $value[$len - 1]) {
        return substr($value, 1, -1);
    }

    return $value;
}
