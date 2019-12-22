<?php namespace App\Blog\Modules\Dict;

use App\Blog\Library\Enum\RedisExpireEnum;
use App\Blog\Library\Enum\RedisGroupEnum;
use App\Blog\Library\Enum\RedisKeyEnum;
use App\Blog\Library\Util\DictUtil;
use App\Blog\Models\DB\Dict as DictModel;
use App\Blog\Modules\Base;
use CjsRedis\Redis;

class Dict extends Base
{
    /**
     * @title: 通过字典组单个获取字典信息
     * @param $param    string   字典组
     * @return mixed
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public static function getDictInfoByType($param)
    {
        $ret = DictModel::where('type', $param)->get()->toArray();
        return $ret;
    }

    /**
     * @title: 从redis中批量获取字典信息
     * @param $param    array   请求参数
     * @return array|mixed
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public static function getDictInfoRedisByTypes($param)
    {
        $data = [];
        # 从redis中取
        $redisData = self::getRedisDictInfo($param);
        if (!empty($redisData['redisNot'])) {
            # redis中没找到的查数据库
            $filter  = ['type', 'parent_code', 'code', 'value', 'remark', 'rank'];
            $queData = self::getDictInfoByTypes($redisData['redisNot'], $filter);
            $data    = DictUtil::formatArrCut($queData);
            # 将查询出来的数据存到redis中
            self::setRedisDictInfo($data);
        }
        $redisHave = array_get($redisData, 'redisHave', []);
        return $redisHave + $data;
    }

    /**
     * @title: 从redis中获取字典信息
     * @param $param    array   请求参数
     * @return array
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    protected static function getRedisDictInfo($param)
    {
        $redisHave = $redisNot = [];
        foreach ($param as $item) {
            # 查询redis取值
            $redisKey  = sprintf(RedisKeyEnum::BLOG_DICT_KEY, $item);
            $redisData = Redis::get(RedisGroupEnum::DICT, $redisKey);
            if (!empty($redisData)) {
                $decode           = json_decode($redisData, true);
                $redisHave[$item] = $decode;
            } else {
                $redisNot[] = $item;
            }
        }
        return ['redisHave' => $redisHave, 'redisNot' => $redisNot];
    }

    /**
     * @title: 通过字典组批量获取字典信息
     * @param array $param 字典组
     * @param mixed $filter 过滤器
     * @return mixed
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    public static function getDictInfoByTypes($param, $filter = "*")
    {
        $ret = DictModel::select($filter)->whereIn('type', $param)->orderBy('rank', 'asc')->get()
            ->toArray();
        return $ret;
    }

    /**
     * @title: 将字典信息存入redis中
     * @param array $param 请求参数
     * @author: godfrey.gan <g854787652@gmail.com>
     */
    protected static function setRedisDictInfo($param)
    {
        foreach ($param as $key => $val) {
            # 存入redis
            $redisKey = sprintf(RedisKeyEnum::BLOG_DICT_KEY, $key);
            Redis::set(RedisGroupEnum::DICT, $redisKey,
                       json_encode($val), RedisExpireEnum::EXPIRE_DAY_ONE);
        }
    }
}