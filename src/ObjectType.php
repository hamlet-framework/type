<?php declare(strict_types=1);

namespace Hamlet\Type;

/**
 * @extends Type<object>
 */
class ObjectType extends Type
{
    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true object $value
     */
    public function matches($value): bool
    {
        return is_object($value);
    }

    /**
     * @param mixed $value
     * @return object
     */
    public function cast($value)
    {
        return (object) $value;
    }

    public function __toString(): string
    {
        return 'object';
    }
}
