<?php

namespace App\Blog\Modules\Queue\Redis;

use App\Blog\Modules\Queue\RedisBase;
use App;
use CjsRedis\RedisLock;
use Exception;
use Log;
use CjsRedis\RedisQueue;

class Test extends RedisBase
{
    protected $queue      = 'test_123'; //实际的redis队列名
    protected $queueGroup = 'common';   //redis组

    /**
     * 向队列发送内容
     * @param $message 队列内容
     */
    public function send($message)
    {
        $ret = 0;
        if (!$message) {
            return $ret;
        }
        if (is_array($message)) {
            $message = json_encode($message);
        }
        $ret = RedisQueue::setQueue($this->queueGroup, $this->queue, $message);
        return $ret;
    }


    /**
     * 消费队列，接收对接信息
     * php artisan queue:consumer --queue_name=test --queue_type=redis
     */
    public function receive()
    {
        //redis加锁 todo  --待删除
        for ($j = 1; $j < 100; $j++) {//放入队列测试数据
            $this->send("你好" . mt_rand(100, 9999));
        }
        //以上代码待删除 todo

        //消费脚本加锁
        $i = RedisLock::lock($this->queueGroup, $this->queue . ":redislock", 120);//120秒锁
        if (!$i) {//加锁失败，请勿重复提交，不能处理后续动作
            echo "加锁失败，请勿重复提交，不能处理后续动作" . $i . PHP_EOL;
            Log::info(__METHOD__ . ' 加锁失败，阻止多次执行');
            return false;
        }

        Log::info(__METHOD__ . '开始接收redis队列：' . $this->queue . ' 数据');
        $i = 0; //循环次数，计数器
        while (true) {
            ++$i;

            //读取队列并开始处理业务逻辑代码 start
            try {
                //获取队列数据
                $data = RedisQueue::getQueue($this->queueGroup, $this->queue);
                if ($data) {
                    $i = 0;
                    echo "获取到的队列内容" . $data . PHP_EOL;
                }

            } catch (Exception $e) {
                echo __METHOD__ . $e->getMessage() . PHP_EOL;
            }
            //读取队列并开始处理业务逻辑代码 end

            if ($i > 10000) {//尝试10000次没数据，退出
                sleep(3);
                break;
            }
        }


        //释放锁
        RedisLock::unlock($this->queueGroup, $this->queue . ":redislock");

    }


}