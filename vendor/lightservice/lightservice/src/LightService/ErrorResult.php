<?php
/**
 * structure for error result
 *
 * @author yuanbaoju
 */

namespace LightService;

class ErrorResult
{
    public $code;
    public $message;

    public function __construct($message, $code = -1)
    {
        // parent::__construct([], ArrayObject::STD_PROP_LIST | ArrayObject::ARRAY_AS_PROPS);
        $this->message = $message;
        $this->code = $code;
    }

    public static function fromException($ex)
    {
        return new static($ex->getMessage(), $ex->getCode());
    }

    public static function fromArray($err)
    {
        return new static($err['message'], $err['code']);
    }

    public function __toString()
    {
        return sprintf('LightService\\Error (%d): %s', $this->code, $this->message);
    }
}
