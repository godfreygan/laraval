<?php

namespace App\Blog\Console\Commands;

use App\Blog\Console\Command;
use CjsConsole\Input\InputOption;

/**
 * Class Consumer
 * 消费者-消费队列
 * @package App\Blog\Console\Commands
 */
class Consumer extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'queue:consumer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'queue consumer command消费队列脚本';

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['queue_type', null, InputOption::VALUE_REQUIRED, '队列类型：kafka、redis', 'kafka'],
            ['queue_name', null, InputOption::VALUE_REQUIRED, '队列名', null],
            //partition
        ];
    }

    /**
     * Execute the console command.
     * php artisan queue:consumer --queue_name=test --queue_type=kafka
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->ignoreValidationErrors();                      //开启错误提示
            $queueType = strtolower($this->option('queue_type')); //队列类型，kafka，redis
            $queueName = $this->option('queue_name');             //队列名
            if (!$queueName) {
                $this->info("队列名不能为空，queue_name：{$queueName}");
            }

            if (in_array($queueType, ['kafka', 'redis'])) {
                $this->info("queue_type: {$queueType}");

                $className = "\App\Blog\Modules\Queue\\" . ucfirst($queueType) . "\\" . \HdsCommon\CommonFunc::parseName($queueName, 2);
                if (class_exists($className)) {
                    $queueObj = new $className;
                    $queueObj->receive(); //接收队列数据并处理
                } else {
                    $this->info("类不存在: {$className}");
                }

            } else {
                $this->info("不支持的队列类型: {$queueType}");
            }

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

}
