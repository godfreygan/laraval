<?php

namespace Saber\MQ\Kafka;

use RdKafka\Producer as RdProducer;
use Saber\MQ\Exception\RdKafkaException;
use Saber\MQ\ProducerInterface;

class Producer extends KafkaBase implements ProducerInterface
{
    /**
     * @var RdProducer
     */
    private $producer;

    private $queue;

    private $partition;

    private $options = [
        'timeout' => 30,
    ];

    public function __construct(array $config, string $queue, $partition = null)
    {
        parent::__construct($config);

        $this->queue = $queue;

        if (isset($partition)) {
            $this->partition = $partition;
        }

        $this->setDrMsgCb();
        $this->producer = new RdProducer($this->getKafkaConf());
    }

    public function push($message, array $options = []): void
    {
        $this->options = array_merge($this->options, $options);

        $queue = $this->producer->newTopic($this->queue);
        $queue->produce(
            $this->partition ?? RD_KAFKA_PARTITION_UA,
            0,
            $message,
            $this->options['key'] ?? null
        );

        if (isset($this->options['timeout'])) {
            $this->wait($this->options['timeout']);
        }
    }

    public function wait($timeout)
    {
        while ($this->producer->getOutQLen() > 0) {
            $this->producer->poll($timeout);
        }
    }

    private function setDrMsgCb()
    {
        $this->getKafkaConf()->setDrMsgCb(function ($kafka, $message) {
            if ($message->err) {
                throw new RdKafkaException(sprintf('%s, message: %s.', rd_kafka_err2str($message->err), var_export($message, 1)), $message->err);
            }
        });
    }

    public function close()
    {
        $this->producer = null;
    }

    public function __destruct()
    {
        $this->close();
    }
}
