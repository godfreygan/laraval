<?php
include __DIR__ . '/common.php';

$_SERVER['REQUEST_URI'] = 'hi/say';
$routeObj = CjsSimpleRoute\Route::getInstance()->init('\Demo\Controllers\\')->setUrlPattern('');
$res = $routeObj->run(function($me){
    $ret = [
        'className'=>$me->getAppCtlNamespace() . 'IndexController',
        'method'=>"indexAction", //默认方法
    ];
    $uri = $me->getUri();
    $uriInfo = explode('?', $uri, 2);
    $uriPath = trim(array_shift($uriInfo), '/');

    if (!empty($uriPath)) {
        $uriPath =  explode('/', $uriPath);
        $ret['className'] = sprintf('%s%sController', $me->getAppCtlNamespace(), ucfirst(preg_replace_callback('/(_|-|\.)([a-zA-Z])/', function($match){return '\\'.strtoupper($match[2]);}, $uriPath[0])) );
        if (isset($uriPath[1])) {
            $ret['method'] =  $uriPath[1] . 'Action';
            unset($uriPath[0], $uriPath[1]);
        } else {
            unset($uriPath[0]);
        }
    }
    return $ret;
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

