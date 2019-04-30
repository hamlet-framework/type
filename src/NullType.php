<?php

namespace Hamlet\Cast;

/**
 * @extends Type<null>
 */
class NullType extends Type
{
    public function matches($value): bool
    {
        return is_null($value);
    }

    public function cast($value)
    {
        if ($value != null) {
            throw new CastException($value, $this);
        }
        return null;
    }

    public function __toString()
    {
        return 'null';
    }
}
