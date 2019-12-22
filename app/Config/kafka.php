<?php
return [
    'blog_test'       => [     //业务名，区分host，用途：测试，自产自销
        'connection' => [
            'kafka'        => [
                'metadata.broker.list'  => env('KAFKA_BROKER_LIST', 'localhost:9092'),
                'socket.timeout.ms'     => 6000,
                'enable.auto.commit'    => 'false',
                'request.required.acks' => 1,
                'session.timeout.ms'    => 10000,
            ],
            'topic'        => [
                'auto.offset.reset'  => 'latest', //读取最新的
                'message.timeout.ms' => 3000,
            ],
            'poll_timeout' => 30,
        ],
        'producers'  => [
            'p1' => [
                'queue'     => 'blog.test', //队列名，topic名
                'partition' => null,
                'key'       => null,
            ],
        ],
        'consumers'  => [
            'c1' => [
                'group_id' => 'blog.test',      // group name 最好和消费内容有关联
                'queues'   => [
                    [
                        'queue'     => 'blog.test',//队列名，topic名
                        'partition' => null,
                        'offset'    => null,
                    ],
                ],
            ],
            'c_testc2' => [        // 订阅消费
                'group_id' => 'blog.test.c2',
                'queues'   => [
                    [
                        'queue'     => 'blog.test', //队列名，topic名，必须和生产者topic一致
                        'partition' => null,
                        'offset'    => null,
                    ],
                ],
            ],
        ],
    ],
];

