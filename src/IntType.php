<?php declare(strict_types=1);

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
    public function cast($value): int
    {
        if (is_object($value)) {
            throw new CastException($value, $this);
        }
        return (int) $value;
    }

    public function __toString(): string
    {
        return 'int';
    }
}
