<?php
namespace CjsSimpleRoute;

class Route {

    const author = 'cjs';
    const version = "1.0.0";

    protected $isInit = false;
    protected $appCtlNamespace = '\App\Controllers\\'; //控制器命名空间

    protected $routeExists = false; //是否满足路由，即找到路由类和方法，默认false
    protected $uri;
    protected $urlPattern = '/^[\/]?(resetapi|h5)([\/]+|[\/]?$)/i';
    
    protected function __construct() {

    }

    public static function getInstance() {
        static $instance = null;
        if(is_null($instance)) {
            $instance = new static;
        }
        return $instance;
    }

    public function init($appCtlNamespace=null, $uri=null, $ext = []) {
        if($this->isInit) {
            return true;
        }
        $this->isInit = true;
        if(!is_null($appCtlNamespace)) {
            $this->setAppCtlNamespace($appCtlNamespace);
        }
        if(is_null($uri)) {
            $uri = isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'';
        }
        $this->setUri($uri);

        return $this;
    }

    public function getUrlPattern()
    {
        return $this->urlPattern;
    }

    public function setUrlPattern($urlPattern)
    {
        $this->urlPattern = $urlPattern;
        return $this;
    }

    public function setAppCtlNamespace($appCtlNamespace) {
        $this->appCtlNamespace = $appCtlNamespace;
        return $this;
    }

    public function getAppCtlNamespace() {
        return $this->appCtlNamespace;
    }

    public function setUri($uri) {
        $this->uri = $uri;
        return $this;
    }

    public function getUri() {
        return $this->uri;
    }

    public function run($callback = '') {
        $res = '';
        $uri = $this->uri;
        $urlPattern = $this->urlPattern;
        if ( $callback instanceof \Closure) {
            $callbackRes = call_user_func_array($callback, [$this, $urlPattern, $uri]);
            $className = isset($callbackRes['className'])?$callbackRes['className']:'';
            $method = isset($callbackRes['method'])?$callbackRes['method']:'indexAction';
        } else if($urlPattern && preg_match($urlPattern, $uri)) {
            //匹配规则
            $uri = preg_replace($urlPattern, '', $uri);
            $uriInfo = explode('?', $uri, 2);
            $uriPath = trim(array_shift($uriInfo), '/');

            $className = $this->getAppCtlNamespace() . 'IndexController';
            $method = "indexAction"; //默认方法
            if (!empty($uriPath)) {
                $uriPath =  explode('/', $uriPath);
                //控制器
                $className = sprintf('%s%sController', $this->getAppCtlNamespace(), ucfirst(preg_replace_callback('/(_|-|\.)([a-zA-Z])/', function($match){return '\\'.strtoupper($match[2]);}, $uriPath[0])) );
                //动作
                if (isset($uriPath[1])) {
                    $method =  $uriPath[1] . 'Action';
                    unset($uriPath[0], $uriPath[1]);
                } else {
                    unset($uriPath[0]);
                }
            }
        } else {
            $className = '';
            $method = '';
        }
        if($className && class_exists($className)) {
            $controllerObj = new $className;
            if($method && method_exists($controllerObj, $method)) {
                $this->routeExists = true;
                $res = call_user_func(array($controllerObj, $method));
            } else {
                $this->routeExists = false;
            }
        } else {
            $this->routeExists = false;
        }

        return $res;
    }

    public function getRouteExists() {
        return $this->routeExists;
    }


}
