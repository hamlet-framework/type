<?php

namespace Hamlet\Cast;

/**
 * @extends Type<int>
 */
class IntType extends Type
{
    public function matches($value): bool
    {
        return is_int($value);
    }

    public function cast($value)
    {
        if (is_object($value)) {
            throw new CastException($value, $this);
        }
        return (int) $value;
    }

    public function __toString()
    {
        return 'int';
    }
}
