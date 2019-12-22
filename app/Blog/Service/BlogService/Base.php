<?php
/**
 * 博客服务基类
 */
namespace App\Blog\Service\BlogService;

class Base extends \App\Blog\Service\Base
{
    /**
     * @title: 解析用户服务返回数据格式
     * @param $data
     * @return mixed
     * @throws \App\Blog\Library\Exceptions\ServiceException
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    protected static function data($data) {
        $data = parent::data($data);
        return $data;
    }

    /**
     * @title: 获取博客服务请求地址
     * @return array|mixed|string|null
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    protected static function myService(){
        return parent::getServiceApi('blog_service_api');
    }

}