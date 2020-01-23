### install

- libkafka(>=1.0)
~~~
git clone https://github.com/edenhill/librdkafka.git
cd librdkafka
./configure
make
make install
~~~

- php-rdkafka
~~~
git clone https://github.com/arnaud-lb/php-rdkafka.git
cd php-rdkafka
phpize
./configure
make all -j 5
make install
~~~

- composer.json
~~~
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://gitlub.handeson.com/dev-00/message-queue.git"
    }
  ],
  "require": {
    "saber/message-queue": "^2.0.0"
  }
}
~~~

### 参考配置

~~~
<?php
return [
    'connection1' => [
        'kafka' => [
            'metadata.broker.list' => 'localhost:9092',
            'socket.timeout.ms' => 6000,
            'enable.auto.commit' => 'false',
            'session.timeout.ms' => 10000
        ],

        'topic' => [
            'auto.offset.reset' => 'latest', //读取最新的
            'message.timeout.ms' => 1000,
        ],
        'poll_timeout' => 30,
    ],

    'producers' => [
        'p1' => [
            'queue' => 'test1',
            'partition' => null,
            'key' => null,
        ]
    ],

    'consumers' => [
        'c1' => [
            'group_id' => 10,
            'queues' => [
                [
                    'queue' => 'test1',
                    'partition' => null,
                    'offset' => null,
                ],
                [
                    'queue' => 'test2',
                    'partition' => null,
                    'offset' => null,
                ]
            ],
        ]
    ],
];
~~~

### 生产者

~~~
<?php

use Saber\MQ\Kafka\Producer;

include_once '../../../vendor/autoload.php';

try {
    //配置参数 详见：https://github.com/edenhill/librdkafka/blob/v0.9.2/CONFIGURATION.md
    $mq_config = require __DIR__ . '/config.php';

    $producer = new Producer($mq_config['connection1'], ...array_values($mq_config['producers']['p1']));

    $producer->push( 'message no:' . microtime(1) * 10000);
} catch (Exception $e) {
    echo $e->getMessage();
}
~~~

### 消费者

~~~
#!/usr/bin/php
<?php

use Saber\MQ\Exception\TimeoutException;
use Saber\MQ\Kafka\Consumer;

include_once '../../../vendor/autoload.php';

try {
    $mq_config = require __DIR__ . '/config.php';
    $consumer = new Consumer(
            $mq_config['connection1'],
            $mq_config['consumers']['c1']['group_id'],
            ...$mq_config['consumers']['c1']['queues']
    );

    /**
     * 弹出一条消息处理
     * 正常只指定queue即可
     * pop模式下消费完成请手动提交commit
     */
    while (1) {
        try {
            $data = $consumer->pop();

            if (is_null($data)) {
                continue;
            }

            //do something
            echo $data . PHP_EOL;

            //do not forget commit
            $consumer->commit();
        } catch (TimeoutException $e) {

            continue;
        } catch (Exception $e) {
            throw $e;
        }
    }
} catch (Throwable $e) {
    echo $e->getMessage() . PHP_EOL;
}
~~~
