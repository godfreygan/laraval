<?php
include __DIR__ . '/common.php';

$_SERVER['REQUEST_URI'] = 'resetapi/hi/say';
$routeObj = CjsSimpleRoute\Route::getInstance()->init('\Demo\Controllers\\');
$res = $routeObj->run();
if($routeObj->getRouteExists()){
    if(is_array($res)) {
        var_export($res);
    } else {
        echo $res;
    }
} else {
    echo "404 not found!";
}

