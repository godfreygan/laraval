/* eslint-disable no-restricted-syntax */
const cwd = '/data/www/blog/';
const script = './artisan';
const exec_mode = 'fork';
const interpreter = '/usr/local/php/bin/php';
const max_memory_restart = '500M';
const max_restarts = 5;

const config = {};
config.apps = [
    {
        script: script, // php artisan queue:consumer --queue_name=blog_test --queue_type=kafka
        name: 'blog-test',// 脚本名称  测试
        instances: 1,
        exec_mode: exec_mode,
        interpreter: interpreter,
        cwd: cwd,
        args: 'queue:consumer --queue_name=blog_test --queue_type=kafka',//artisan 参数
        max_memory_restart: max_memory_restart,
        error_file: '/data/logs/app/blog/pm2/blog-test-error.log',  //错误日志
        out_file: '/data/logs/app/blog/pm2/blog-test-out.log',      //输出日志
        combine_logs: true,
        max_restarts: max_restarts
    },
    {
        script: script, // php artisan queue:consumer --queue_name=blog_test_c2 --queue_type=kafka
        name: 'blog-test-c2',// 脚本名称  测试订阅消费
        instances: 1,
        exec_mode: exec_mode,
        interpreter: interpreter,
        cwd: cwd,
        args: 'queue:consumer --queue_name=blog_test_c2 --queue_type=kafka',//artisan 参数
        max_memory_restart: max_memory_restart,
        error_file: '/data/logs/app/blog/pm2/blog-test-c2-error.log',//错误日志
        out_file: '/data/logs/app/blog/pm2/blog-test-c2-out.log',//输出日志
        combine_logs: true,
        max_restarts: max_restarts
    }
];

module.exports = config;
