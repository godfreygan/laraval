<?php

namespace App\Util;

class FromService
{
    const BLOG_API = 'blog';     //博客 接口

    public static function getInstance()
    {
        static $instance;
        if (!$instance) {
            $instance = new static();
        }
        return $instance;
    }

    //项目模块的服务名对应的命名空间
    public function fromService2Namespace($moduleName, $fromService)
    {
        $res        = '';
        $moduleName = ucfirst($moduleName);
        $config     = [
            'Blog' => [
                self::BLOG_API => '\App\\Blog\\Controllers\\',
            ]
        ];

        if (isset($config[$moduleName])) {
            $res = $config[$moduleName][$fromService];
        }
        return $res;
    }
}