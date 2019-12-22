<?php
/**
 * id generator of client's message
 * you could override the default generator by assign $generator
 *
 * @author yuanbaoju
 */

namespace LightService\Util;

class IdGen
{
    private $generate;

    public function __construct($generate = null)
    {
        $this->generate = $generate ?: function () {
            return ObjectId::generate();
        };
    }

    public function __invoke()
    {
        return $this->nextId();
    }

    public function nextId()
    {
        return call_user_func($this->generate);
    }
}
