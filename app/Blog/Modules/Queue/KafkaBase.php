<?php

namespace App\Blog\Modules\Queue;

use App;
use Exception;
use Log;
use Saber\MQ\Kafka\Producer;

abstract class KafkaBase
{
    use KafkaSingleton;
    protected        $queueGroup = 'common'; //redis组
    protected static $mqConfig   = [];       //kafka配置

    public function __construct()
    {
        if (empty(static::$mqConfig)) {
            static::$mqConfig = require App::configPath('kafka.php');
        }
    }

    //发送队列
    abstract public function send($type, $data = null, $id = null);

    //处理队列
    abstract public function receive();

    /**
     * @param $message 消息内容
     * @param $configKey 配置key
     * @param string $topicKey topic配置key
     * @return bool
     */
    public function baseSend($message, $configKey, $topicKey = 'p1', $isStatic = true)
    {
        static $pObj = [];
        $mq_config = self::$mqConfig;
        try {
            //配置参数 详见：https://github.com/edenhill/librdkafka/blob/v0.9.2/CONFIGURATION.md

            if ($isStatic && isset($pObj[$configKey]) && $pObj[$configKey]) {
                $producer = $pObj[$configKey];
            } else {
                $producer = new Producer($mq_config[$configKey]['connection'], ...array_values($mq_config[$configKey]['producers'][$topicKey]));
                if ($isStatic) {
                    $pObj[$configKey] = $producer;
                }
            }
            if (is_array($message)) {
                $message = \json_encode($message);
            }
            $producer->push($message);
            return true;
        } catch (Exception $e) {
            //echo $e->getMessage();
            if (isset($pObj[$configKey])) {
                unset($pObj[$configKey]);
            }
            Log::error(__METHOD__ . '发送kafka队列发生异常： ' . $e->getMessage());
            return false;
        }

    }


}