<?php

namespace Hamlet\Cast;

/**
 * @extends Type<bool>
 */
class BoolType extends Type
{
    public function matches($value): bool
    {
        return is_bool($value);
    }

    public function cast($value)
    {
        return (bool) $value;
    }

    public function __toString()
    {
        return 'bool';
    }
}
