<?php
/**
 * trait for explore the class info
 *
 * @author yuanbaoju
 */

namespace LightService\Util;

trait Explorable
{
    public function __explore()
    {
        $class = new \ReflectionClass($this);
        $methods = $class->getMethods();

        $ret = [];

        foreach ($methods as $method) {
            if (!$method->isPublic()) {
                continue;
            }

            $name = $method->getName();

            if (preg_match('/^__/', $name)) {
                continue;
            }

            $parameters = [];

            foreach ($method->getParameters() as $parameter) {
                $parameters[] = '$' . $parameter->getName();
            }

            $ret[] = sprintf('%s(%s)', $name, implode(', ', $parameters));
        }

        return $ret;
    }
}
