<?php
define('ROOT_DIR', __DIR__);

$config_file = ROOT_DIR . '/config.json';

if (!file_exists($config_file)) {
    die("no config file");
}

$config = json_decode(file_get_contents($config_file), true);
var_dump($config);

require '../../../../vendor/autoload.php';

use LightService\Client\Service;

Service::importConf($config);
