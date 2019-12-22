<?php

namespace App\Blog\Service\BlogService;

use Log;

class Blog extends Base
{

    /**
     * @title: 测试测试
     * @param $data
     * @param bool $is_data
     * @return mixed
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public static function test($data, $is_data = true)
    {
        $error  = '';
        $result = $GLOBALS['clientServiceObj']->stub('blog_service_api', 'Blog')->test($data)->wait($error);
        Log::debug(__METHOD__ . '第三方请求信息', ['param' => $data, 'data' => $result, 'error' => $error]);
        if ($error) {
            $result['code'] = $error->code;
            $result['msg']  = $error->message;
        }
        if ($is_data) {
            return self::data($result);
        }
        return $result;
    }
}
