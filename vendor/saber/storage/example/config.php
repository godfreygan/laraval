<?php
return [
    'driver' => 'qiniu',
    'config' => [
        'qiniu' => [
            'access_key' => 'APN5g5hdKFqGj1G_5xHuouTy-41QMpvxYa0b8e2-', //七牛ak
            'secret_key' => '2zmK9ynnMj2-sKwvmthfRcE7EQnETLfP1Wlhzt9C', //七牛sk
            'buckets' => [
                'lm_bucket' => [
                    'domain' => 'http://pqes2srb7.bkt.clouddn.com',
                    'is_pub' => 1,
                ],
                'lm_bucket_private' => [
                    'domain' => 'http://pqp9pnj2h.bkt.clouddn.com',
                    'is_pub' => 0,
                ],
            ],
            'image_size_default' => [ //默认图片规格 获取图片不指定尺寸和水印的情况下 系统默认自动加载的链接
                'original' => null,
                'big' => 'imageMogr2/auto-orient/thumbnail/500/blur/1x0/quality/75|imageslim',
                'middle' => 'imageMogr2/auto-orient/thumbnail/300/blur/1x0/quality/75|imageslim',
                'small' => 'imageMogr2/auto-orient/thumbnail/100/blur/1x0/quality/75|imageslim',
            ],
            'image_size_custom' => [ //接收参数（宽、高、水印）时加载的链接
                'imageMogr2/auto-orient/thumbnail/%dx%d/blur/1x0/quality/75|imageslim',
                'imageMogr2/auto-orient/blur/1x0/quality/75|%s|imageslim',
                'imageMogr2/auto-orient/thumbnail/%dx%d/blur/1x0/quality/75|%s|imageslim',
            ],
            'watermark' => 'watermark/1/image/aHR0cHM6Ly9vanBibHkxdW4ucW5zc2wuY29tL2xvZ28ucG5n/dissolve/50/gravity/SouthEast/dx/10/dy/10', //水印
        ],
    ],
];