<?php

namespace Hamlet\Cast;

/**
 * @extends Type<float>
 */
class FloatType extends Type
{
    public function matches($value): bool
    {
        return is_float($value);
    }

    public function cast($value)
    {
        if (is_object($value)) {
            throw new CastException($value, $this);
        }
        return (float) $value;
    }

    public function __toString()
    {
        return 'float';
    }
}
