<?php

namespace Hamlet\Cast;

/**
 * @extends Type<string>
 */
class StringType extends Type
{
    public function matches($value): bool
    {
        return is_string($value);
    }

    public function cast($value)
    {
        if (is_array($value)) {
            throw new CastException($value, $this);
        }
        if (is_object($value) && !method_exists($value, '__toString')) {
            throw new CastException($value, $this);
        }
        return (string) $value;
    }

    public function __toString()
    {
        return 'string';
    }
}
