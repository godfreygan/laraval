API
===

## LightTracer\GlobalTracer

### GlobalTracer::init([])

初始化全局的Tracer

```php
<?php

LightTracer\Plugin\LightService::init([
    'endpoint_name'         =>  'your-service',                          // 必填：服务名称，一般为服务的名称或域名
    'trace_log_path'        =>  '/data/logs/app/your-service/trace_log', // 必填：日志文件存放目录，请确保有写权限
    'trace_log_span'        =>  'day',                                   // 必填：day 按天分隔文件
]);
```

其他参数
* name RootSpan名, 默认为unknown
* type RootSpan类型, 默认为空
* endpoint_ipv4 本机IP，默认会自动取
* endpoint_version APP版本
* endpoint_port APP的服务端口
* trace_sample_rate 抽样率 0.0 - 1.0, 默认为1.0
* auto_start 是否自动开始计时，默认为true
* auto_finish APP结束时是否自动结束计时，默认为true
* tags 数组，可以设置key-value
* carrier 数组，用来传输上游的trace id等信息

### GlobalTracer::logEvent($event) 

用于记录某个时刻发生了什么事情

```php
<?php
GlobalTracer::logEvent('user logined');
```

### GlobalTracer::setTag($key, $value) 

用于记录一些简单的key-value

```php
<?php
GlobalTracer::setTag('key', 'value');
```

### GlobalTracer::setError($errno, $errstr)

标记出现错误，错误之后会有告警

```php
<?php
GlobalTracer::setTag(1001, 'db connection lost');
```

### GlobalTracer::createSpan($name, $type)

创建新的span
* $name RootSpan名, 默认为unknown
* $type RootSpan类型, 默认为空
 
```php
<?php
$span = GlobalTracer::createSpan('heavy_task', 'TASK');
```

### GlobalTracer::startSpan($span)

开始span计时
* $span GlobalTracer::createSpan创建的返回值

### GlobalTracer::finishSpan($span)

结束span计时
* $span GlobalTracer::createSpan创建的返回值

### GlobalTracer::scope($func, [$name, $type])

创建新的子Span来执行Scope里面的逻辑
* $func 你的程序逻辑，这段逻辑会被自动计时
* [$name, $type] span名、类型

```php
<?php
// 异常会当错误记录，并会继续抛出
GlobalTracer::scope(function () {
    throw new \Exception('balabala');
    usleep(30 * 1000);
}, ['heavy_task', 'TASK']); // spanName, spanType

// 异常不会被记录，并会继续抛出
GlobalTracer::scope(function () {
    throw new \Exception('balabala');
    usleep(30 * 1000);
}, ['heavy_task', 'TASK'], ['ignore_exception' => true]); // spanName, spanType
```

### GlobalTracer::inject()

生成用于传给下游的关键信息carrier（traceid，spanid等），返回为key-value数组；
carrier可以将分布在不同系统下的trace关联起来，形成统一的追踪链。

```php
<?php
$carrier = $span->inject();
foreach ($carrier as $key => $value) {
    $key = 'x-' . str_replace('_', '-', $key);
    $request->addHeader($key, $value);
}

// 收到carrier后，在GlobalTracer初始化的时候可以传入
LightTracer\GlobalTracer::init([
    'carrier' => $carrier
]);
```

### GlobalTracer::setSideToClient()

设置角色为client，当发起远程请求时调用，请在startSpan之前调用

### GlobalTracer::setSideServer()

设置角色为server，当收到远程请求时调用，请在startSpan之前调用

## LightTracer\Plugin\HttpService

### HttpService::init([])
HttpService 和 GlobalTracer的用法完全相同，
HttpService 会去检测HTTP Header中是否带有carrier信息，有的话使用。

```php
<?php
// 初始化参数同 \LightTracer\GlobalTracer::init
LightTracer\Plugin\HttpService::init([
    'endpoint_name'         =>  'your-service',                          // 必填：服务名称，一般为服务的名称或域名
    'trace_log_path'        =>  '/data/logs/app/your-service/trace_log', // 必填：日志文件存放目录，请确保有写权限
    'trace_log_span'        =>  'day',                                   // 必填：day 按天分隔文件
]);
```

## LightTracer\Plugin\LightService

### LightService::init([])
LightService 和 GlobalTracer的用法完全相同，
LightService 会去读取LightService传来的carrier信息，解析生成RPC的span。

```php
<?php
LightTracer\Plugin\LightService::init([
    'endpoint_name'         =>  'your-service',                          // 必填：服务名称，一般为服务的名称或域名
    'trace_log_path'        =>  '/data/logs/app/your-service/trace_log', // 必填：日志文件存放目录，请确保有写权限
    'trace_log_span'        =>  'day',                                   // 必填：day 按天分隔文件
]);

```
