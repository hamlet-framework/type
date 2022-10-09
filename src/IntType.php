<?php declare(strict_types=1);

namespace Hamlet\Type;

/**
 * @extends Type<int>
 */
class IntType extends Type
{
    /**
     * @psalm-assert-if-true int $value
     */
    public function matches(mixed $value): bool
    {
        return is_int($value);
    }

    public function cast(mixed $value): int
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
