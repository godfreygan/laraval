<?php

namespace LightTracer\Writer;

use LightTracer\Util\Debug as DebugUtil;

class FileWriter extends AbstractWriter
{
    protected $file_handler = false;
    protected $file_name = false;
    protected $file_prefix = '';
    protected $file_extension = 'log';
    protected $file_dir = '.';
    protected $time_span = 600; // 单位秒
    protected $name;
    protected $last_time;
    protected $last_now;

    /***
     * 初始化
     * @param string $prefix 文件名的前缀
     * @param string $dir 文件的目录
     * @param int $span 设置日志文件的间隔时间，单位秒
     * @param string $extension 文件的扩展名
     * @param bool 是否开启调试模式
     */
    public function __construct($prefix = '', $dir = '.', $span = 600, $extension = 'log')
    {
        $this->file_prefix    = $prefix;
        $this->file_dir       = $dir;
        $this->time_span      = $span;
        $this->file_extension = $extension;

        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        if (!$this->time_span) {
            $this->name = $this->file_prefix . '.' . $this->file_extension;
        }
    }

    public function write($log)
    {
        if (is_array($log)) {
            $log = json_encode($log);
            if (!$log) {
                $this->triggerError('FileWriter error :' . json_last_error_msg());
                return false;
            }
        }

        $name = $this->getCurrentName();
        if ($name != $this->file_name) {
            if ($this->file_handler) {
                DebugUtil::log('fclose ' . $this->file_name);
                fclose($this->file_handler);
            }

            $log_path = $this->file_dir . "/{$name}";
            DebugUtil::log("fopen $log_path");
            $this->file_handler = fopen($log_path, 'a+');
            $this->file_name    = $name;

            stream_set_chunk_size($this->file_handler, 1024 * 1024); // 1MB
        }

        $result = fwrite($this->file_handler, $log . "\n");
        if ($result === false) {
            $error = "file write failed: $name\n";
            $this->triggerError($error);
            return false;
        }

        return true;
    }

    /***
     * 取得当前日志名
     */
    public function getCurrentName()
    {
        $span = $this->time_span;

        if (!$span) {
            return $this->name;
        }

        switch ($span) {
            case 'daily':
            case 'day':
                $span = 86400;
                break;
            case 'hourly':
            case 'hour':
                $span = 3600;
                break;
            case 'minute':
                $span = 60;
                break;
        }

        $time = time();
        if ($time === $this->last_time) {
            return $this->name;
        }
        $this->last_time = $time;

        $now = $time - ($time + date('Z')) % $span;
        if ($now === $this->last_now) {
            return $this->name;
        }
        $this->last_now = $now;

        if ($span % 86400 === 0) { // day
            $this->name = $this->file_prefix . date('Ymd', $now) . '.' . $this->file_extension;
        } elseif ($span % 3600 === 0) { // hour
            $this->name = $this->file_prefix . date('YmdH', $now) . '.' . $this->file_extension;
        } else { // minute
            $this->name = $this->file_prefix . date('YmdHi', $now) . '.' . $this->file_extension;
        }

        return $this->name;
    }
}
