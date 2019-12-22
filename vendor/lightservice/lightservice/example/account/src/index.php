<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

define('ROOT_DIR',      __DIR__);
define('LIB_DIR',       ROOT_DIR . '/lib');
define('MODULE_DIR',    ROOT_DIR . '/module');

require __DIR__ . '/lib/lightservice.phar';

echo LightService\Server\Service::create(
    'jsonrpc',
    function($module, $method) {
        if (!isset($module) || !isset($method)) {
            return false;
        }

        $module_file = MODULE_DIR . '/' . $module . '.php';

        if (dirname($module_file) !== MODULE_DIR) {
            return false;
        }

        if (!file_exists($module_file)) {
            var_dump($module_file);
            return false;
        }

        require $module_file;

        if (!class_exists($module)) {
            return false;
        }

        $m = new $module;

        if (!method_exists($m, $method)) {
            return false;
        }

        return array($m, $method);
    }
)->respond(file_get_contents('php://input'));
