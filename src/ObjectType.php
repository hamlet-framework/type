<?php

namespace Hamlet\Cast;

/**
 * @extends Type<object>
 */
class ObjectType extends Type
{
    public function matches($value): bool
    {
        return is_object($value);
    }

    public function cast($value)
    {
        return (object) $value;
    }

    public function __toString()
    {
        return 'object';
    }
}
