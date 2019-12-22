<?php
/**
 * 获取图片地址
 */

namespace App\Util;


use App;
use Log;
use Saber\Storage\Storage;

class Img
{
    static private $instance;

    const TYPE_QINIU = 'qiniu';

    public static function instance($type = self::TYPE_QINIU)
    {
        if (empty(self::$instance)) {
            Log::debug(__FUNCTION__, [$type]);
            switch ($type) {
                case self::TYPE_QINIU:
                    self::$instance = Storage::init(require App::configPath('qiniu.php'));
                    break;
                default:
                    break;
            }
        }
        return (new self());
    }

    /**
     * @param $key
     * @param $bucket
     * @param int $width
     * @param int $high
     * @param bool $watermark 是否加水印
     * @return mixed
     */
    public function getImg($key, $bucket, $width = 244, $high = 244, $watermark = false)
    {
        if (empty($key) || empty($bucket)) {
            return '';
        }
        $ret = self::$instance->createUrlWithSize([
                                                      [
                                                          'key'    => $key,
                                                          'bucket' => $bucket,
                                                      ],
                                                  ], $width, $high, $watermark);
        return isset($ret[0]) ? $ret[0] : '';
    }
}