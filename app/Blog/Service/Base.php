<?php

namespace App\Blog\Service;

use App\Blog\Library\Exceptions\ServiceException;
use CjsLsf\Core\Config;
use Log;

/**
 * 第三方服务base
 * 所有方法必须为static类型
 */
class Base
{
    /**
     * \App\Mobile\Service\Base::getServiceApi('blog_service_api');
     * @param $serviceCode  blog_service_api 其code值参考 config/app.php文件配置的key名
     */
    public static function getServiceApi($serviceCode)
    {
        $key = 'app.' . $serviceCode;
        $api = Config::get($key);
        if ($api) {
            return $api;
        } else {
            return '';
        }
    }

    /**
     * 博客服务API地址
     * @return mixed|null
     */
    protected static function blogApi()
    {
        return Config::get('app.blog_api');
    }

    /**
     * 获取第三方返回数据data部分
     * @param $resopnsData
     * @return mixed
     * @throws ServiceException
     */
    protected static function data($resopnsData)
    {
        if ($resopnsData['code']) {
            Log::error(__METHOD__ . ' 第三方请求失败', [$resopnsData]);
            throw new ServiceException($resopnsData['msg'], $resopnsData['code']);
        }
        return $resopnsData['data'];
    }
}