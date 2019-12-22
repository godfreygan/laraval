<?php
return [
    'db_blog_1' => [
        'driver'    => 'mysql',
        'read'      => [
            'host'     => env('BLOG_1_DB_READ_HOST'),
            'database' => env('BLOG_1_DB_READ_DATABASE'),
            'username' => env('BLOG_1_DB_READ_USERNAME'),
            'password' => env('BLOG_1_DB_READ_PASSWORD'),
            'port'     => env('BLOG_1_DB_READ_PORT'),
        ],
        'write'     => [
            'host'     => env('BLOG_1_DB_WRITE_HOST'),
            'database' => env('BLOG_1_DB_WRITE_DATABASE'),
            'username' => env('BLOG_1_DB_WRITE_USERNAME'),
            'password' => env('BLOG_1_DB_WRITE_PASSWORD'),
            'port'     => env('BLOG_1_DB_WRITE_PORT'),
        ],
        'sticky'    => true,//必须为true
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'prefix'    => 't_'
    ],
    'db_blog_2' => [
        'driver'    => 'mysql',
        'read'      => [
            'host'     => env('BLOG_2_DB_READ_HOST'),
            'database' => env('BLOG_2_DB_READ_DATABASE'),
            'username' => env('BLOG_2_DB_READ_USERNAME'),
            'password' => env('BLOG_2_DB_READ_PASSWORD'),
            'port'     => env('BLOG_2_DB_READ_PORT'),
        ],
        'write'     => [
            'host'     => env('BLOG_2_DB_WRITE_HOST'),
            'database' => env('BLOG_2_DB_WRITE_DATABASE'),
            'username' => env('BLOG_2_DB_WRITE_USERNAME'),
            'password' => env('BLOG_2_DB_WRITE_PASSWORD'),
            'port'     => env('BLOG_2_DB_WRITE_PORT'),
        ],
        'sticky'    => true,//必须为true
        'charset'   => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'prefix'    => 't_'
    ],
];
