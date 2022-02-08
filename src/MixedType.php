<?php declare(strict_types=1);

namespace Hamlet\Cast;

/**
 * @extends Type<mixed>
 */
class MixedType extends Type
{
    public function matches(mixed $value): bool
    {
        return true;
    }

    /**
     * @psalm-suppress MixedReturnStatement
     */
    public function cast(mixed $value): mixed
    {
        return $value;
    }

    public function __toString(): string
    {
        return 'mixed';
    }
}
