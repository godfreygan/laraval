## 上传服务对接
### 时序图
- 上传：

![image](http://pqes2srb7.bkt.clouddn.com/upload2.png)

- 下载/显示：

![image](http://pqes2srb7.bkt.clouddn.com/download)

#### 安装
~~~
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://gitlub.handeson.com/dev-00/storage.git"
    }
  ],
  "require": {
    "saber/storage": "^1.0.0"
  }
}
~~~

#### 配置
~~~
<?php
/**
* 配置包含七牛后台配置好的bucket，获取对应token时传入，
* image_size_default：获取图片时若不指定图片尺寸默认返回自定义的四种规格，包含原图、大、中、小
* image_size_custom：指定图片规格时显示图片的尺寸格式
* watermark 水印图片
*/
<?php
return [
    'driver' => 'qiniu',
    'config' => [
        'qiniu' => [
            'access_key' => 'your access_key', //七牛ak
            'secret_key' => 'your secret_key', //七牛sk
            'buckets' => [
                'lm_bucket' => [
                    'domain' => 'http://pqes2srb7.bkt.clouddn.com', //访问域名
                    'is_pub' => 1, //是否公有资源 1-是 0-不是
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
~~~

#### 使用
~~~
<?php

use Lightlib\Storage\Storage;

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

~~~

