<?php declare(strict_types=1);

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

    /**
     * @param mixed $value
     * @return float
     */
    public function cast($value): float
    {
        if (is_object($value)) {
            throw new CastException($value, $this);
        }
        return (float) $value;
    }

    public function __toString(): string
    {
        return 'float';
    }
}
