<?php
return [
    'user_1_db' => [
        'driver'    => 'mysql',
        'read'      => [
            'host'     => 'localhost',
            'database' => 'db_user_1',
            'username' => 'userread',
            'password' => '123456',
        ],
        'write'     => [
            'host'     => 'localhost',
            'database' => 'db_user_1',
            'username' => 'root',
            'password' => '123456',
        ],
        'port'      => 3306,
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => 't_'
    ],
];
