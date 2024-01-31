<?php declare(strict_types=1);

namespace Hamlet\Type;

/**
 * @extends Type<float>
 */
readonly class FloatType extends Type
{
    public function matches(mixed $value): bool
    {
        return is_float($value);
    }

    public function cast(mixed $value): float
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
