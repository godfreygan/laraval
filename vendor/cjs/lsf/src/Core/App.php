<?php
namespace CjsLsf\Core;

class App extends Container
{
    private static $_service;
    private static $_base_path;
    private static $_app_path;
    private static $_config_path;
    private static $_storage_path;
    private static $_public_path;
    private static $_static_path;
    private static $_runtime_path;
    private static $_upload_path;

    public static function handle($input = null)
    {
        return self::$_service->respond($input ?: file_get_contents('php://input'));
    }

    public static function invoke($module, $method, $params = [], $id = null)
    {
        return self::$_service->invoke($module, $method, $params, $id);
    }

    public static function invokeMethod($method, $params = [], $id = null)
    {
        return self::$_service->invokeMethod($method, $params, $id);
    }

    public static function bootstrap($lookup, $protocol = 'jsonrpc')
    {
        //self::$_service = Service::create($protocol, $lookup);
    }

    public static function setBasePath($base_path)
    {
        self::$_base_path = $base_path;
    }

    public static function basePath()
    {
        return self::$_base_path;
    }

    public static function appPath($path = '')
    {
        return (self::$_app_path ?: self::basePath() . '/app') . ($path ? '/' . $path : $path);
    }

    public static function configPath($path = '')
    {
        return (self::$_config_path ?: self::basePath() . '/config') . ($path ? '/' . $path : $path);
    }

    public static function storagePath($path = '')
    {
        return (self::$_storage_path ?: self::basePath() . '/storage') . ($path ? '/' . $path : $path);
    }

    public static function publicPath($path = '')
    {
        return (self::$_public_path ?: self::basePath() . '/public') . ($path ? '/' . $path : $path);
    }

    public static function staticPath($path = '')
    {
        return (self::$_static_path ?: self::basePath() . '/static') . ($path ? '/' . $path : $path);
    }

    public static function runtimePath($path = '')
    {
        return (self::$_runtime_path ?: self::basePath() . '/runtime') . ($path ? '/' . $path : $path);
    }

    public static function uploadPath($path = '')
    {
        return (self::$_upload_path ?: self::basePath() . '/upload') . ($path ? '/' . $path : $path);
    }

    public static function setAppPath($app_path)
    {
        self::$_app_path = $app_path;
    }

    public static function setConfigPath($config_path)
    {
        self::$_config_path = $config_path;
    }

    public static function setStoragePath($storage_path)
    {
        self::$_storage_path = $storage_path;
    }

    public static function setPublicPath($public_path)
    {
        self::$_public_path = $public_path;
    }

    public static function setStaticPath($static_path)
    {
        self::$_static_path = $static_path;
    }

    public static function setRuntimePath($runtime_path)
    {
        self::$_runtime_path = $runtime_path;
    }

    public static function setUploadPath($upload_path)
    {
        self::$_upload_path = $upload_path;
    }
}
