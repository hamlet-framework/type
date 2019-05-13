<?php

namespace Hamlet\Cast;

/**
 * @extends Type<resource>
 */
class ResourceType extends Type
{
    public function matches($value): bool
    {
        return is_resource($value);
    }

    public function cast($value)
    {
        if (!is_resource($value)) {
            throw new CastException($value, $this);
        }
        return $value;
    }

    public function __toString()
    {
        return 'resource';
    }
}
