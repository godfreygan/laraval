<?php

namespace Saber\MQ\Kafka;

use RdKafka\Conf;
use RdKafka\TopicConf;
use Saber\MQ\Exception\RdKafkaException;

abstract class KafkaBase
{
    /**
     * @var Conf
     */
    private $kafka_conf;

    protected $config = [
        'kafka' => [
            'metadata.broker.list' => '',
            'socket.timeout.ms' => 6000,
            'enable.auto.commit' => 'false',
            'session.timeout.ms' => 10000
        ],
        'topic' => [
            'auto.offset.reset' => 'latest', //读取最新的
            'message.timeout.ms' => 1000,
        ],
    ];

    public function __construct($config)
    {
        $this->config = array_replace_recursive($this->config, $config);
        $topic_conf = new TopicConf();

        foreach ($this->config['topic'] as $key => $value) {
            $topic_conf->set($key, $value);
        }

        $this->kafka_conf = new Conf();

        if (isset($this->config['kafka'])) {
            foreach ($this->config['kafka'] as $key => $value) {
                $this->kafka_conf->set($key, $value);
            }
        }

        $this->kafka_conf->setDefaultTopicConf($topic_conf);
        $this->kafka_conf->setErrorCb(function ($kafka, $err, $reason) {
            throw new RdKafkaException(sprintf('%s, %s', rd_kafka_err2str($err), $reason), $err);
        });
    }

    protected function getKafkaConf()
    {
        return $this->kafka_conf;
    }
}