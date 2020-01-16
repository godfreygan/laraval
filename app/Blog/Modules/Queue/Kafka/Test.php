<?php

namespace App\Blog\Modules\Queue\Kafka;

use App\BLog\Modules\Queue\KafkaBase;
use App;
use CjsRedis\RedisLock;
use Exception;
use Log;
use Saber\MQ\Exception\TimeoutException;
use Saber\MQ\Kafka\Consumer;
use Saber\Events\Event;

class Test extends KafkaBase
{
    protected $queue = 'test';

    /**
     * 向队列发送内容
     * @param $message 队列内容
     * @return bool
     */
    public function send($type, $data = null, $id = null)
    {
        $kafkaEvent = new Event($type, $data, $id);
        $message    = $kafkaEvent->__toString();
        Log::info("队列：" . $this->queue . "生成消息ID:" . $kafkaEvent->getId());
        return $this->baseSend($message, $this->queue, 'p1');
    }


    /**
     * 消费队列，接收对接信息
     * php artisan queue:consumer --queue_name=test --queue_type=kafka
     * @return bool
     * @throws \Throwable
     */
    public function receive()
    {
        //测试数据
        for ($j = 1; $j < 10; $j++) {
            $this->send('testsuccess', ['userid' => 123, 'username' => 'admin' . $j]);
        }

        //消费脚本加锁
        $i = RedisLock::lock($this->queueGroup, $this->queue . ":redislock", 120);//120秒锁
        if (!$i) {//加锁失败，请勿重复提交，不能处理后续动作
            echo "加锁失败，请勿重复提交，不能处理后续动作" . $i . PHP_EOL;
            Log::info(__METHOD__ . ' 加锁失败，阻止多次执行');
            return false;
        }

        Log::info(__METHOD__ . '开始接收kafka队列' . $this->queue . '数据');
        try {
            $mq_config = self::$mqConfig;
            $configKey = $this->queue;
            $consumer  = new Consumer(
                $mq_config[$configKey]['connection'],
                $mq_config[$configKey]['consumers']['c1']['group_id'],
                ...$mq_config[$configKey]['consumers']['c1']['queues']
            );
            //路由job
            while (true) {
                $ok = false;
                try {
                    $data = $consumer->pop(); //读数据
                    //处理业务逻辑
                    echo PHP_EOL . $data . PHP_EOL;

                    $ok = true;
                } catch (TimeoutException $e) {
//                    Log::debug(__METHOD__ . "超时");
                    continue;
                } catch (Exception $e) {
                    Log::error(__METHOD__ . $e->getMessage());
                    break;
                }

                if ($ok) {
                    $consumer->commit();
                }
            }

        } catch (Exception $e) {
            Log::error(__METHOD__ . $e->getMessage());
        }

        //释放锁
        RedisLock::unlock($this->queueGroup, $this->queue . ":redislock");

    }


}