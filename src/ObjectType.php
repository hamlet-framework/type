<?php declare(strict_types=1);

namespace Hamlet\Type;

/**
 * @extends Type<object>
 */
class ObjectType extends Type
{
    /**
     * @psalm-assert-if-true object $value
     */
    public function matches(mixed $value): bool
    {
        return is_object($value);
    }

    public function cast(mixed $value): object
    {
        return (object) $value;
    }

    public function __toString(): string
    {
        return 'object';
    }
}
