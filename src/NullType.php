<?php declare(strict_types=1);

namespace Hamlet\Cast;

/**
 * @extends Type<null>
 */
class NullType extends Type
{
    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true null $value
     */
    public function matches($value): bool
    {
        return is_null($value);
    }

    /**
     * @param mixed $value
     * @return null
     */
    public function cast($value)
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
