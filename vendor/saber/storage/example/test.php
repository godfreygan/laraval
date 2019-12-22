<?php

use Saber\Storage\Storage;

include_once '../vendor/autoload.php';

$storage = Storage::init(require './config.php');

//获取token
$token = $storage->createToken('lm_bucket');
echo $token;

//获取链接 指定宽度、高度、水印
$resources = [
    [
        'key' => 'lm_cus',
        'bucket' => 'lm_bucket',
    ],
    [
        'key' => '20190429095012.png',
        'bucket' => 'lm_bucket_private',
    ],
];

$list = $storage->createUrl($resources);
print_r($list);

$width = 300;
$high = 200;
$watermark = 1;
$list = $storage->createUrlWithSize($resources, $width, $high, $watermark);
print_r($list);

$list = $storage->createSimpleUrl($resources);
print_r($list);
