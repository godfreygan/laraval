<?php
return [
    'prefix' => CjsLsf\env(App::storagePath('logs')) . '/lsf/lsf-service.log',
    'level'  =>  CjsLsf\env('LOG_LEVEL', 'debug'),
    'name'   => CjsLsf\env('APP_ENV', 'production'),
    'channel'=>'lsf-service',
];
