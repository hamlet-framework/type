<?php declare(strict_types=1);

namespace Hamlet\Type;

/**
 * @extends Type<bool>
 */
class BoolType extends Type
{
    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true bool $value
     */
    public function matches(mixed $value): bool
    {
        return is_bool($value);
    }

    public function cast(mixed $value): bool
    {
        return (bool) $value;
    }

    public function __toString(): string
    {
        return 'bool';
    }
}
