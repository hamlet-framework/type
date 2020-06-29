<?php declare(strict_types=1);

namespace Hamlet\Cast;

/**
 * @extends Type<mixed>
 */
class MixedType extends Type
{
    /**
     * @param mixed $value
     * @return bool
     */
    public function matches($value): bool
    {
        return true;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function cast($value)
    {
        return $value;
    }

    public function __toString(): string
    {
        return 'mixed';
    }
}
