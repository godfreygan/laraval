LightService Plugin for LightTracer
===

LightService用户使用此Plugin可以快速接入LightTracer，并且自动追踪RPC调用情况。

接入步骤
===

* composer 添加此模块

```json

"repositories": [{
  "name": "lighttracer-ls",
  "type": "vcs",
  "reference": "origin/master",
  "url": "https://gitlub.handeson.com/dev-00/lighttracer-ls.git"
},{
  "name": "lighttracer",
  "type": "vcs",
  "reference": "origin/master",
  "url": "https://gitlub.handeson.com/dev-00/lighttracer.git"
},{
    "name": "lightservice/lightservice",
    "type": "vcs",
    "reference": "origin/master",
    "url": "https://gitlub.handeson.com/dev-00/lightservice.php.git"
  }
],

"require": {
  "lighttracer/lighttracer-ls": "^1.0.0"
}
```

* 程序启动(public/index.php)

```php
<?php

// 务必放在程序启动最开始
LightTracer\Plugin\LightService::init([
    'endpoint_name'  => 'your-service',                            // 项目名称
    'trace_log_path' => '/data/logs/app/sms-service/trace_log',    // 存放路径
    'trace_log_span' => 'day'                                      // 按天分割
]);

// 获取当前trace_id
$trace_id = GlobalTracer::traceId();
```

相关资源
===
* [LightTracer](https://gitlub.handeson.com/dev-00/lighttracer)
* [LightService](https://gitlub.handeson.com/dev-00/lightservice.php)

用法
===
* 基本用法 (https://gitlub.handeson.com/dev-00/lighttracer/blob/master/examples/basic.php)
* RPC用法 (https://gitlub.handeson.com/dev-00/lighttracer/blob/master/examples/rpc.php)
* 其他用法 (https://gitlub.handeson.com/dev-00/lighttracer/blob/master/examples/scope.php)

