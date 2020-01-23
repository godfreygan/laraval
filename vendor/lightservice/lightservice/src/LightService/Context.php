<?php
/**
 * structure of common context
 *
 * @author yuanbaoju
 */

namespace LightService;

use ArrayObject;

class Context extends ArrayObject
{
    private static $last = 0;
    public $id;

    public function __construct($array = [])
    {
        $this->id = static::$last;

        if ($this->id === PHP_INT_MAX) {
            static::$last = 0;
        } else {
            ++static::$last;
        }

        parent::__construct($array, ArrayObject::STD_PROP_LIST | ArrayObject::ARRAY_AS_PROPS);
    }
}
