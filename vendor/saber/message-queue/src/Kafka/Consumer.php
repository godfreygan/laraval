<?php

namespace Saber\MQ\Kafka;

use InvalidArgumentException;
use RdKafka\KafkaConsumer;
use RdKafka\TopicPartition;
use Saber\MQ\ConsumerInterface;
use Saber\MQ\Exception\RdKafkaException;
use Saber\MQ\Exception\TimeoutException;

class Consumer extends KafkaBase implements ConsumerInterface
{
    /**
     * @var KafkaConsumer
     */
    private $consumer;

    private $hold_messages;

    private $queues;

    public function __construct(array $config, $group_id, ...$queues)
    {
        parent::__construct($config);

        $this->queues = $queues;

        $this->setRebalanceCb();
        $this->getKafkaConf()->set('group.id', $group_id);

        $this->consumer = new KafkaConsumer($this->getKafkaConf());

        $subscribes = [];
        $topic_partitions = [];
        foreach ($this->queues as $queue) {
            if (isset($queue['offset'])) {
                if (!isset($queue['partition'])) {
                    throw new InvalidArgumentException('partition is required when offset set');
                }

                $topic_partitions[] = new TopicPartition(
                    $queue['queue'],
                    $queue['partition'] ?? 0,
                    $queue['offset'] ?? null
                );
            } else {
                $subscribes[] = $queue['queue'];
            }
        }

        if ($subscribes) {
            $this->consumer->subscribe($subscribes);
        }

        if ($topic_partitions) {
            $this->consumer->assign($topic_partitions);
        }
    }

    public function pop($timeout = 2000, &$out = null): ?string
    {
        $kafka_message = $this->consumer->consume($timeout);

        if (!$kafka_message->err) {
            $this->hold_messages[] = $kafka_message;

            if (func_num_args() > 1) {
                $out = [
                    'queue'     => $kafka_message->topic_name,
                    'partition' => $kafka_message->partition,
                    'offset'    => $kafka_message->offset,
                ];
            }

            return $kafka_message->payload;
        } elseif ($kafka_message->err == RD_KAFKA_RESP_ERR__PARTITION_EOF) {
            return null;
        } elseif ($kafka_message->err == RD_KAFKA_RESP_ERR__TIMED_OUT) {
            throw new TimeoutException(rd_kafka_err2str($kafka_message->err), $kafka_message->err);
        } else {
            throw new RdKafkaException(rd_kafka_err2str($kafka_message->err), $kafka_message->err);
        }
    }

    public function commit(): void
    {
        if ($this->hold_messages) {
            while ($message = array_shift($this->hold_messages)) {
                $this->consumer->commit($message);
            }
        }
    }

    private function setRebalanceCb()
    {
        $this->getKafkaConf()->setRebalanceCb(function (
            KafkaConsumer $kafka,
            $err,
            $partitions = null
        ) {
            if ($err == RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS) {
                $kafka->assign($partitions);
            } elseif ($err == RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS) {
                $kafka->assign(null);
            }
        });
    }

    public function close()
    {
        $this->consumer = null;
    }

    public function __destruct()
    {
        $this->close();
    }
}
