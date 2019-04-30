<?php

namespace Hamlet\Cast;

/**
 * @extends Type<mixed>
 */
class MixedType extends Type
{
    public function matches($value): bool
    {
        return true;
    }

    public function cast($value)
    {
        return $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'mixed';
    }
}
