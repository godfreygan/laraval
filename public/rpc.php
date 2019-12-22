<?php
/**
 * rpc入口
 */
require dirname(__DIR__) . '/bootstrap/autoload.php';

//供swagger跨域使用,暂时放在这里
if(env('APP_ENV', 'production') == 'dev'){
    header('Access-Control-Allow-Origin:*');
    header('Access-Control-Allow-Headers:content-type,*');
}


$rpcServer = new \LightService\Jsonrpc\Server\Server([
    'use_msgpack' => false,
    'loader' => function($service, $method) {

        $class = '\App\\'.getModuleName().'\\Controllers\\' . $service . 'Controller';

        if (class_exists($class)) {
            return [new $class, $method.'Action'];
        }
    },
    'exception_handler'=>function($e) {
        //如果是PRD环境如果不是自定义异常，则统一抛出系统异常
        if(getenv('APP_ENV','production') == 'production' && !($e instanceof \App\Blog\Library\Exceptions\ServiceException)){
            Log::debug('exception_handler',[$e->getCode(),$e->getMessage()]);
            return [\LightService\ErrorResult::fromArray(['code' => $e->getCode(),'message' => '系统异常!'])];
        }
        return false;
    }
]);

header('Content-type: application/json;charset=utf-8');
echo $rpcServer->respond(('GET' == $_SERVER['REQUEST_METHOD']) ? $_GET : file_get_contents('php://input'));

