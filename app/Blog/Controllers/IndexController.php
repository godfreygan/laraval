<?php

namespace App\Blog\Controllers;

use App\Util\ValidatorUtil;
use CjsRedis\Redis;
use App\Blog\Library\Exceptions\ServiceException;

class IndexController extends Base
{

    public function indexAction()
    {
        return $this->responseSuccess(['tips' => 'web服务正常'], __METHOD__);
    }
}