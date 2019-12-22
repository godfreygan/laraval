<?php
return [
    'sequence_tbl_prefix' => [      // sequence业务对应的唯一标识前缀
        't_user' => 'U',
    ],
    'sequence_db_config'  => [      // sequence业务缓存down了之后使用的db配置
        'host'     => env('BLOG_SEQUENCE_HOST'),
        'database' => env('BLOG_SEQUENCE_DATABASE'),
        'username' => env('BLOG_SEQUENCE_USERNAME'),
        'password' => env('BLOG_SEQUENCE_PASSWORD'),
        'port'     => env('BLOG_SEQUENCE_PORT', 3306),
    ],
    'blog_service_api'    => env('DOMAIN_BLOG_SERVICE_HOST'),
];