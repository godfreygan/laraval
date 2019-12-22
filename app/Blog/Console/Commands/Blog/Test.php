<?php

namespace App\Blog\Console\Commands\Blog;

use App\Blog\Library\Enum\RedisExpireEnum;
use App\Blog\Library\Enum\RedisGroupEnum;
use App\Blog\Library\Enum\RedisKeyEnum;
use App\Blog\Console\Command;
use CjsRedis\RedisLock;
use Log;

class Test extends Command
{
    /**
     * The console command name.
     * php artisan blog:test
     * @var string
     */
    protected $name = 'blog:test';      // 必须唯一

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试测试';

    /**
     * @title: Execute the console command.
     * @return bool
     * @throws \Exception
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public function handle()
    {
        $this->comment("测试job执行 START");
        $redisKey = sprintf(RedisKeyEnum::BLOG_JOB_CONTROL_KEY, $this->name);
        $i        = RedisLock::lock(RedisGroupEnum::BLOG, $redisKey . ":redislock", RedisExpireEnum::EXPIRE_MINUTE_TEN);   //10min 锁
        if (!$i) {       //加锁失败，请勿重复提交，不能处理后续动作
            Log::info(__METHOD__ . ' 加锁失败，阻止多次执行');
            $this->comment('existed job');
            return false;
        }

        // todo 脚本逻辑实现

        $this->comment("执行结束前 END");
        RedisLock::unlock(RedisGroupEnum::BLOG, $redisKey . ":redislock");       // 释放锁
        $this->comment("测试job执行 END");
    }
}