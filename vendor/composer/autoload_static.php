<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2b12a19bc079f1833a1de2e038424aef
{
    public static $files = array (
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        '72579e7bd17821bb1321b87411366eae' => __DIR__ . '/..' . '/illuminate/support/helpers.php',
        '7b11c4dc42b3b3023073cb14e519683c' => __DIR__ . '/..' . '/ralouphie/getallheaders/src/getallheaders.php',
        '023d27dca8066ef29e6739335ea73bad' => __DIR__ . '/..' . '/symfony/polyfill-php70/bootstrap.php',
        'bbf73f3db644d3dced353b837903e74c' => __DIR__ . '/..' . '/php-di/php-di/src/DI/functions.php',
        'a0edc8309cc5e1d60e3047b5df6b7052' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/functions_include.php',
        'c964ee0ededf28c96ebd9db5099ef910' => __DIR__ . '/..' . '/guzzlehttp/promises/src/functions_include.php',
        'da6aba14df3d54e89bb7e04ae38e7543' => __DIR__ . '/..' . '/lighttracer/lighttracer/src/helper.php',
        '5f7346a77ab30c53f42d8cb245672153' => __DIR__ . '/..' . '/lighttracer/lighttracer/src/flakeid.php',
        'e2bde475e1a96658ef3f9d72bc6eebdb' => __DIR__ . '/..' . '/lightservice/lightservice/src/LightService/helpers.php',
        '70848e8c2e3efa471de54b341bc703fc' => __DIR__ . '/..' . '/cjs/console/src/Helper.php',
        'd452b30061acaec1beb643dfb7c29336' => __DIR__ . '/..' . '/cjs/lsf/src/bootstrap.php',
        '2195477101814c574c5d392b91229fc0' => __DIR__ . '/..' . '/cjs/lsf/src/helpers.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
        'aa76659f8be957867870ba8f8d045bfa' => __DIR__ . '/..' . '/serverbench/process/src/helpers.php',
        '667aeda72477189d0494fecd327c3641' => __DIR__ . '/..' . '/symfony/var-dumper/Resources/functions/dump.php',
        'c4518125a5e8c85f642eaf22f341ef2f' => __DIR__ . '/../..' . '/app/Util/helpers.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Php70\\' => 23,
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Contracts\\Translation\\' => 30,
            'Symfony\\Component\\VarDumper\\' => 28,
            'Symfony\\Component\\Translation\\' => 30,
            'Symfony\\Component\\HttpFoundation\\' => 33,
            'Symfony\\Component\\Finder\\' => 25,
            'ServerBench\\Process\\' => 20,
            'Saber\\Storage\\' => 14,
            'Saber\\MQ\\' => 9,
            'Saber\\Events\\' => 13,
        ),
        'P' => 
        array (
            'Psr\\SimpleCache\\' => 16,
            'Psr\\Log\\' => 8,
            'Psr\\Http\\Message\\' => 17,
            'Psr\\Container\\' => 14,
            'PhpDocReader\\' => 13,
        ),
        'M' => 
        array (
            'Monolog\\' => 8,
        ),
        'L' => 
        array (
            'LightTracer\\Plugin\\' => 19,
            'LightTracer\\' => 12,
            'LightService\\' => 13,
        ),
        'I' => 
        array (
            'Invoker\\' => 8,
            'Interop\\Container\\' => 18,
            'Illuminate\\Validation\\' => 22,
            'Illuminate\\Translation\\' => 23,
            'Illuminate\\Support\\' => 19,
            'Illuminate\\Filesystem\\' => 22,
            'Illuminate\\Events\\' => 18,
            'Illuminate\\Database\\' => 20,
            'Illuminate\\Contracts\\' => 21,
            'Illuminate\\Container\\' => 21,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
        ),
        'D' => 
        array (
            'Doctrine\\Common\\Inflector\\' => 26,
            'DI\\' => 3,
        ),
        'C' => 
        array (
            'CjsSimpleRoute\\' => 15,
            'CjsRedis\\' => 9,
            'CjsLsf\\' => 7,
            'CjsCron\\' => 8,
            'CjsConsole\\' => 11,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Php70\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-php70',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Contracts\\Translation\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/translation-contracts',
        ),
        'Symfony\\Component\\VarDumper\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/var-dumper',
        ),
        'Symfony\\Component\\Translation\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/translation',
        ),
        'Symfony\\Component\\HttpFoundation\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/http-foundation',
        ),
        'Symfony\\Component\\Finder\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/finder',
        ),
        'ServerBench\\Process\\' => 
        array (
            0 => __DIR__ . '/..' . '/serverbench/process/src',
        ),
        'Saber\\Storage\\' => 
        array (
            0 => __DIR__ . '/..' . '/saber/storage/src',
        ),
        'Saber\\MQ\\' => 
        array (
            0 => __DIR__ . '/..' . '/saber/message-queue/src',
        ),
        'Saber\\Events\\' => 
        array (
            0 => __DIR__ . '/..' . '/saber/events/src',
        ),
        'Psr\\SimpleCache\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/simple-cache/src',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'PhpDocReader\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-di/phpdoc-reader/src/PhpDocReader',
        ),
        'Monolog\\' => 
        array (
            0 => __DIR__ . '/..' . '/monolog/monolog/src/Monolog',
        ),
        'LightTracer\\Plugin\\' => 
        array (
            0 => __DIR__ . '/..' . '/lighttracer/lighttracer-ls/src',
        ),
        'LightTracer\\' => 
        array (
            0 => __DIR__ . '/..' . '/lighttracer/lighttracer/src',
        ),
        'LightService\\' => 
        array (
            0 => __DIR__ . '/..' . '/lightservice/lightservice/src/LightService',
        ),
        'Invoker\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-di/invoker/src',
        ),
        'Interop\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/container-interop/container-interop/src/Interop/Container',
        ),
        'Illuminate\\Validation\\' => 
        array (
            0 => __DIR__ . '/..' . '/illuminate/validation',
        ),
        'Illuminate\\Translation\\' => 
        array (
            0 => __DIR__ . '/..' . '/illuminate/translation',
        ),
        'Illuminate\\Support\\' => 
        array (
            0 => __DIR__ . '/..' . '/illuminate/support',
        ),
        'Illuminate\\Filesystem\\' => 
        array (
            0 => __DIR__ . '/..' . '/illuminate/filesystem',
        ),
        'Illuminate\\Events\\' => 
        array (
            0 => __DIR__ . '/..' . '/illuminate/events',
        ),
        'Illuminate\\Database\\' => 
        array (
            0 => __DIR__ . '/..' . '/illuminate/database',
        ),
        'Illuminate\\Contracts\\' => 
        array (
            0 => __DIR__ . '/..' . '/illuminate/contracts',
        ),
        'Illuminate\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/illuminate/container',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
        'Doctrine\\Common\\Inflector\\' => 
        array (
            0 => __DIR__ . '/..' . '/doctrine/inflector/lib/Doctrine/Common/Inflector',
        ),
        'DI\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-di/php-di/src/DI',
        ),
        'CjsSimpleRoute\\' => 
        array (
            0 => __DIR__ . '/..' . '/cjs/simple-route/src',
        ),
        'CjsRedis\\' => 
        array (
            0 => __DIR__ . '/..' . '/cjs/redis/src',
        ),
        'CjsLsf\\' => 
        array (
            0 => __DIR__ . '/..' . '/cjs/lsf/src',
        ),
        'CjsCron\\' => 
        array (
            0 => __DIR__ . '/..' . '/cjs/cron/src',
        ),
        'CjsConsole\\' => 
        array (
            0 => __DIR__ . '/..' . '/cjs/console/src',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $fallbackDirsPsr4 = array (
        0 => __DIR__ . '/..' . '/nesbot/carbon/src',
    );

    public static $prefixesPsr0 = array (
        'W' => 
        array (
            'Webpatser\\Uuid' => 
            array (
                0 => __DIR__ . '/..' . '/webpatser/laravel-uuid/src',
            ),
        ),
        'U' => 
        array (
            'UpdateHelper\\' => 
            array (
                0 => __DIR__ . '/..' . '/kylekatarnls/update-helper/src',
            ),
        ),
    );

    public static $classMap = array (
        'ArithmeticError' => __DIR__ . '/..' . '/symfony/polyfill-php70/Resources/stubs/ArithmeticError.php',
        'AssertionError' => __DIR__ . '/..' . '/symfony/polyfill-php70/Resources/stubs/AssertionError.php',
        'DivisionByZeroError' => __DIR__ . '/..' . '/symfony/polyfill-php70/Resources/stubs/DivisionByZeroError.php',
        'Error' => __DIR__ . '/..' . '/symfony/polyfill-php70/Resources/stubs/Error.php',
        'ParseError' => __DIR__ . '/..' . '/symfony/polyfill-php70/Resources/stubs/ParseError.php',
        'SessionUpdateTimestampHandlerInterface' => __DIR__ . '/..' . '/symfony/polyfill-php70/Resources/stubs/SessionUpdateTimestampHandlerInterface.php',
        'TypeError' => __DIR__ . '/..' . '/symfony/polyfill-php70/Resources/stubs/TypeError.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2b12a19bc079f1833a1de2e038424aef::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2b12a19bc079f1833a1de2e038424aef::$prefixDirsPsr4;
            $loader->fallbackDirsPsr4 = ComposerStaticInit2b12a19bc079f1833a1de2e038424aef::$fallbackDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit2b12a19bc079f1833a1de2e038424aef::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit2b12a19bc079f1833a1de2e038424aef::$classMap;

        }, null, ClassLoader::class);
    }
}
