<?php
/**
 * using for parsing of cli's arguments
 */

namespace ServerBench\Process;

use ArrayObject;

class CliArguments extends ArrayObject
{
    public function __construct($options)
    {
        $opts = [];
        $longopts = [];
        $relations = [];

        foreach ($options as $k => $v) {
            if (is_numeric($k)) {
                if (strlen($v) == 1) {
                    $opts[] = $v;
                } else {
                    $longopts[] = $v;
                }
            } else {
                $opts[] = $k;
                $longopts[] = $v;
                $k = trim($k, ':');
                $v = trim($v, ':');
                $relations[$k] = $v;
                $relations[$v] = $k;
            }
        }

        $arguments = [];

        foreach (getopt(implode('', $opts), $longopts) as $k => $v) {
            if (isset($relations[$k]) && !isset($arguments[$relations[$k]])) {
                $arguments[$relations[$k]] = $v;
            }

            $arguments[$k] = $v;
        }

        parent::__construct($arguments, ArrayObject::STD_PROP_LIST | ArrayObject::ARRAY_AS_PROPS);
    }

    public function get($option, $default = null)
    {
        return isset($this[$option]) ? $this[$option] : $default;
    }
}
