<?php
namespace App\Console;


use CjsConsole\Scheduling\Schedule;
use CjsConsole\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
    /**
     * The Artisan commands provided by your application.
     * @var array
     */
    protected $commands = [
        'App\Blog\Console\Commands\Blog\test',    // test
        'App\Blog\Console\Commands\Consumer',     // 消费队列
    ];

    /**
     * @title: Define the application's command schedule.
     * @author: godfrey.gan <g854787652@gmail.com>
     * @param Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        // 设置跑的时间表
        $schedule->command('Blog:test')->cron('* * * * *'); // test 没分钟执行
        $schedule->command(' queue:consumer --queue_name=test --queue_type=redis')->cron('* * * * *');  // redis队列脚本执行
    }
}
