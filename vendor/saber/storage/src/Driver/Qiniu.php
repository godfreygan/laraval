<?php

namespace Saber\Storage\Driver;

use Saber\Storage\Exception\IllegalBucketException;

class Qiniu
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * 生成token
     * @param string $bucket 配置在气候的存储区域
     * @param null $key 制定保存在七牛的key
     * @param int $expires token有效期
     * @param array $policy 上传策略
     * @return string
     */
    public function createToken($bucket, $key = null, $expires = 3600, $policy = [])
    {
        $deadline = time() + $expires;
        $scope    = $bucket;
        if ($key !== null) {
            $scope .= ':' . $key;
        }

        $args['policy']   = $policy;
        $args['scope']    = $scope;
        $args['deadline'] = $deadline;
        return $this->signWithData(json_encode($args));
    }

    /**
     * 生成资源链接
     * @param array $resources 资源
     * @param int $private_url_expires 私有图片链接有效期
     * @return array
     * @throws IllegalBucketException
     */
    public function createUrl(array $resources, $private_url_expires = 3600)
    {
        $urls = [];

        foreach ($resources as $resource) {
            $url = [];

            $base_url = $this->baseUrl($resource['bucket'], $resource['key']);

            foreach ($this->config['image_size_default'] as $k => $img_size) {
                $url[$k] = $base_url . '?' . $img_size;
            }

            $urls[$resource['key']] = array_map(function ($v) use ($resource, $private_url_expires) {
                return $this->privateDownloadUrl($v, $resource, $private_url_expires);
            }, $url);
        }

        return $urls;
    }

    /**
     * 生成资源链接
     * @param array $resources 资源
     * @param int $width 图片宽度
     * @param int $high 图片高度
     * @param bool $watermark 是否加水印
     * @param int $private_url_expires 私有图片链接有效期
     * @return array
     * @throws IllegalBucketException
     */
    public function createUrlWithSize(
        array $resources,
        int $width = 0,
        int $high = 0,
        $watermark = false,
        $private_url_expires = 3600
    )
    {
        $urls = [];
        foreach ($resources as $resource) {
            $url = $this->baseUrl($resource['bucket'], $resource['key']);

            if ($width && $high && $watermark) {
                //指定宽高 和水印
                $tpl = sprintf($this->config['image_size_custom'][2], $width, $high, $this->config['watermark']);
            } elseif (!($width && $high) && $watermark) {
                //不指定宽高 只加水印
                $tpl = sprintf($this->config['image_size_custom'][1], $watermark);
            } elseif ($width && $high && !$watermark) {
                //指定宽高 不加水印
                $tpl = sprintf($this->config['image_size_custom'][0], $width, $high);
            }
            if (isset($tpl) && $tpl) {
                $url .= '?' . $tpl;
            }

            $url = $this->privateDownloadUrl($url, $resource, $private_url_expires);

            $urls[] = $url;
        }

        return $urls;
    }

    /**
     * 生成最基础的url
     * @param array $resources
     * @param int $private_url_expires
     * @return array
     * @throws IllegalBucketException
     */
    public function createSimpleUrl(array $resources, $private_url_expires = 3600)
    {
        $urls = [];

        foreach ($resources as $resource) {
            $base_url               = $this->baseUrl($resource['bucket'], $resource['key']);
            $urls[$resource['key']] = $this->privateDownloadUrl($base_url, $resource, $private_url_expires);
        }

        return $urls;
    }

    private function privateDownloadUrl($base_url, $resource, $expires = 3600)
    {
        $base_url = rtrim($base_url, '?');

        if (isset($this->config['buckets'][$resource['bucket']]['is_pub'])
            && $this->config['buckets'][$resource['bucket']]['is_pub']
            || !isset($this->config['buckets'][$resource['bucket']]['is_pub'])
        ) {
            return $base_url;
        }

        $deadline = time() + $expires;

        $pos = strpos($base_url, '?');
        if ($pos !== false) {
            $base_url .= '&e=';
        } else {
            $base_url .= '?e=';
        }
        $base_url .= $deadline;

        return $base_url . '&token=' . $this->sign($base_url);
    }

    public function sign($data)
    {
        $hmac = hash_hmac('sha1', $data, $this->config['secret_key'], true);
        return $this->config['access_key'] . ':' . $this->urlSafeEncode($hmac);
    }

    public function signWithData($data)
    {
        $encodedData = $this->urlSafeEncode($data);
        return $this->sign($encodedData) . ':' . $encodedData;
    }

    private static function urlSafeEncode($data)
    {
        $find    = ['+', '/'];
        $replace = ['-', '_'];
        return str_replace($find, $replace, base64_encode($data));
    }

    /**
     * @param $bucket
     * @param $key
     * @return string
     * @throws IllegalBucketException
     */
    private function baseUrl($bucket, $key)
    {
        if (!isset($this->config['buckets'][$bucket]['domain'])) {
            throw new IllegalBucketException('can not find specified bucket in config file.');
        }

        return $this->config['buckets'][$bucket]['domain'] . '/' . $key;
    }
}