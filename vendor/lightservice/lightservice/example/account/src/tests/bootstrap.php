<?php
define('ROOT_DIR', __DIR__);

$config_file = ROOT_DIR . '/config.json';

if (!file_exists($config_file)) {
    die("\nno config file found\n");
}

$config = json_decode(file_get_contents($config_file), true);

require '../../../../vendor/autoload.php';

class_alias('LightService\Client\Service', 'Service');

Service::importConf($config);
