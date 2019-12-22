<?php
return [
    'prefix' => env('LOG_DIR', App::storagePath('logs')) . '/'. env('APP_NAME', 'user') .'/'. env('APP_NAME', 'user') .'.log',
    'level'  =>  env('LOG_LEVEL', 'debug'),
    'name'   => env('APP_ENV', 'production'),
    'channel'=> env('APP_NAME', 'user'),
];
