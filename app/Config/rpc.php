<?php
/**
 * rpc client config
 */

use LightService\Client\Service as clientService;
use Webpatser\Uuid\Uuid;

$GLOBALS['clientServiceObj'] = new clientService([
    'blog_service_api' => [
        'type' => 'jsonrpc',
        'url' => 'http://'.env('DOMAIN_BLOG_SERVICE_HOST') . '/rpc.php',
        'idgen' => function() {
            return Uuid::generate()->__toString();
        },
        'enable_method_path' => false,
        'options' => [
            'query' => ['auth' => env('APP_NAME', 'user'),'idgen' => REQUEST_ID],
            'header' => [
            ],
            'exec_timeout' => 15000,
        ]
    ],
]);
