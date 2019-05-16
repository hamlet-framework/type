<?php

namespace Hamlet\Cast;

/**
 * @extends Type<resource>
 */
class ResourceType extends Type
{
    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true resource $type
     */
    public function matches($value): bool
    {
        return is_resource($value);
    }

    /**
     * @param mixed $value
     * @return resource
     */
    public function cast($value)
    {
        if (!is_resource($value)) {
            throw new CastException($value, $this);
        }
        return $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'resource';
    }
}
