<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

// require __DIR__ . '/lib/lightservice.phar';
require '../../../vendor/autoload.php';

use LightService\Client\Service;

Service::importConf(
    array(
        'base' => array(
            'type'     => 'http',
            'protocol' => 'jsonrpc',
            'conf'     => array(
                'url' => 'http://172.16.68.128:8080/index.php'
            )
        )
    )
);

$base = Service::get('base');
$account = $base->module('account');

$rep = $account->login('ali', 'baba');

echo "\n----------\n";
echo "\naccount->login\n";

if (!$account->errno()) {
    echo 'rep : ';
    var_dump($rep);
} else {
    echo 'errno  : ', $account->errno(), "\n";
    echo 'errstr : ', $account->errstr(), "\n";
}

echo "\n----------\n";
