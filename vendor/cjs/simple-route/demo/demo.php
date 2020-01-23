<?php
include __DIR__ . '/common.php';

$_SERVER['REQUEST_URI'] = 'resetapi/hi/say';
$routeObj = CjsSimpleRoute\Route::getInstance()->init('\Demo\Controllers\\');
$res = $routeObj->run(function($me){
    $uri = $me->getUri();
    $urlPattern = $me->getUrlPattern();
    if($urlPattern){
        $uri = preg_replace($urlPattern, '', $uri);
    }
    $uriInfo = explode('?', $uri, 2);
    $uriPath = trim(array_shift($uriInfo), '/');

    $className = $me->getAppCtlNamespace() . 'IndexController';
    $method = "indexAction"; //默认方法
    if (!empty($uriPath)) {
        $uriPath =  explode('/', $uriPath);
        //控制器
        $className = sprintf('%s%sController', $me->getAppCtlNamespace(), ucfirst(preg_replace_callback('/(_|-|\.)([a-zA-Z])/', function($match){return '\\'.strtoupper($match[2]);}, $uriPath[0])) );
        //动作
        if (isset($uriPath[1])) {
            $method =  $uriPath[1] . 'Action';
            unset($uriPath[0], $uriPath[1]);
        } else {
            unset($uriPath[0]);
        }
    }
    return [
        'className'=>$className,
        'method'=>$method,
    ];
});
if($routeObj->getRouteExists()){
    if(is_array($res)) {
        var_export($res);
    } else {
        echo $res;
    }
} else {
    echo "404 not found!";
}

