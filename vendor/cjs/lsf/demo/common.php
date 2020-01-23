<?php
/**
 * ls demo - common.php
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';

//设置基本目录
App::setBasePath(__DIR__);

//设置项目配置目录
App::setConfigPath(App::appPath() . '/Config/');
//加载配置文件
Config::loadPhp(App::configPath('app.php'), 'app');
Config::loadPhp(App::configPath('db.php'), 'db');
Config::loadPhp(App::configPath('log.php'), 'log');


class demoLog{
    public function say($str) {
        return "hi," . $str;
    }
}
//编写单例模式
App::singleton('log', function() {

    return new demoLog();

});




