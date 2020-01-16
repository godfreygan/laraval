<?php
/**
 * 重新发送kafka消息
 */

namespace App\Blog\Modules\Queue\Kafka;

use App\Blog\Modules\Queue\KafkaBase;
use Saber\Events\Event;
use Log;

class KafkaResend extends KafkaBase
{
    protected $queue = '';

    public function send($type, $data = null, $id = null)
    {
    }

    public function send02($queue, $type, $data = null, $id = null)
    {
        if (empty($queue) || empty($type) || empty($data)) {
            return FALSE;
        }
        $kafkaEvent = new Event($type, $data, $id);
        $message    = $kafkaEvent->__toString();
        Log::info("队列：" . $queue . "生成消息ID:" . $kafkaEvent->getId());
        return $this->baseSend($message, $queue, 'p1');
    }

    public function receive()
    {
    }
}