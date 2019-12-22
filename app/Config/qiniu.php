<?php
return [
    'driver' => 'qiniu',
    'config' => [
        'qiniu' => [
            'access_key' => 'your access_key', //七牛ak
            'secret_key' => 'your secret_key', //七牛sk
            'buckets' => [ //这里需要服务方提供bucket配置
                // 配置
                'prd_pub' => [
                    'domain' => 'http://yourfiledomain.com',
                    'is_pub' => 1,
                ],
                'prd-sec' => [
                    'domain' => 'http://yoursecfiledomain.com',
                    'is_pub' => 0,
                ],
            ],
            'image_size_default' => [ //默认图片规格 获取图片不指定尺寸和水印的情况下 系统默认自动加载的链接
                'original' => null,
                'big' => 'imageMogr2/auto-orient/thumbnail/500/blur/1x0/quality/75',
                'middle' => 'imageMogr2/auto-orient/thumbnail/300/blur/1x0/quality/75',
                'small' => 'imageMogr2/auto-orient/thumbnail/100/blur/1x0/quality/75',
            ],
            'image_size_custom' => [ //接收参数（宽、高、水印）时加载的链接
                'imageMogr2/auto-orient/thumbnail/%dx%d/blur/1x0/quality/75',
                'imageMogr2/auto-orient/blur/1x0/quality/75|%s',
                'imageMogr2/auto-orient/thumbnail/%dx%d/blur/1x0/quality/75|%s',
            ],
            'watermark' => 'watermark/1/image/aHR0cHM6Ly9vanBibHkxdW4ucW5zc2wuY29tL2xvZ28ucG5n/dissolve/50/gravity/SouthEast/dx/10/dy/10', //水印
        ],
    ],
];