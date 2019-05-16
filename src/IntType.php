<?php

namespace Hamlet\Cast;

/**
 * @extends Type<int>
 */
class IntType extends Type
{
    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true int $value
     */
    public function matches($value): bool
    {
        return is_int($value);
    }

    /**
     * @param mixed $value
     * @return int
     */
    public function cast($value)
    {
        if (is_object($value)) {
            throw new CastException($value, $this);
        }
        return (int) $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'int';
    }
}
