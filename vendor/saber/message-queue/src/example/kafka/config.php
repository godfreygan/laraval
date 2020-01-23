<?php
return [
    'connection1' => [
        'kafka' => [
            'metadata.broker.list'    => 'localhost:9092',
            'socket.timeout.ms'       => 6000,
            'enable.auto.commit'      => 'false',
            'session.timeout.ms'      => 10000,
            'request.required.acks'   => 1,
            'max.poll.interval.ms'    => 300000,
            'socket.keepalive.enable' => 'true'
        ],

        'topic' => [
            'auto.offset.reset'  => 'earliest', //读取最新的
            'message.timeout.ms' => 3000,
        ],
        'poll_timeout' => 30,
    ],

    'producers' => [
        'p1' => [
            'queue'     => 'queue.test',
            'partition' => null,
        ]
    ],

    'consumers' => [
        'c1' => [
            'group_id' => 'queue.test',
            'queues'   => [
                [
                    'queue'     => 'queue.test',
                    'partition' => null,
                    'offset'    => null,
                ],
            ],
        ],
    ],
];