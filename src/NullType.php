<?php declare(strict_types=1);

namespace Hamlet\Type;

/**
 * @extends Type<null>
 */
class NullType extends Type
{
    /**
     * @psalm-assert-if-true null $value
     */
    public function matches(mixed $value): bool
    {
        return is_null($value);
    }

    /**
     * @return null
     */
    public function cast(mixed $value): mixed
    {
        if ($value != null) {
            throw new CastException($value, $this);
        }
        return null;
    }

    public function __toString(): string
    {
        return 'null';
    }
}
