<?php declare(strict_types=1);

namespace Hamlet\Cast;

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
    public function matches($value): bool
    {
        return is_bool($value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function cast($value): bool
    {
        return (bool) $value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'bool';
    }
}
