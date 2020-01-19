<?php
return array(
    'common'         => array(
        'host'     => env('COMMON_REDIS_HOST','127.0.0.1'),
        'port'     => env('COMMON_REDIS_PORT', 6379),
        'database' => env('COMMON_REDIS_DATABASE', 0),
        'password' => env('COMMON_REDIS_PASSWORD', ''),
        'prefix'   => env('APP_NAME', 'user') .':common:',
        'desc'     => '无法归类的业务模块'
    ),
    'blog'         => array(
        'host'     => env('BLOG_REDIS_HOST','127.0.0.1'),
        'port'     => env('BLOG_REDIS_PORT', 6379),
        'database' => env('BLOG_REDIS_DATABASE', 9),
        'password' => env('BLOG_REDIS_PASSWORD', ''),
        'prefix'   => env('APP_NAME', 'user') .':blog:',
        'desc'     => '博客相关'
    ),
    'dict'         => array(
        'host'     => env('DICT_REDIS_HOST','127.0.0.1'),
        'port'     => env('DICT_REDIS_PORT', 6379),
        'database' => env('DICT_REDIS_DATABASE', 3),
        'password' => env('DICT_REDIS_PASSWORD', ''),
        'prefix'   => env('APP_NAME', 'user') .':dict:',
        'desc'     => '字典相关'
    ),
    'sequence'        => array(
        'host'     => env('SEQUENCE_REDIS_HOST','127.0.0.1'),
        'port'     => env('SEQUENCE_REDIS_PORT', 6379),
        'database' => env('SEQUENCE_REDIS_DATABASE', 2),
        'password' => env('SEQUENCE_REDIS_PASSWORD', ''),
        'prefix'   => env('APP_NAME', 'user') .':sequence:',
        'desc'     => '生成唯一值'
    ),
);
