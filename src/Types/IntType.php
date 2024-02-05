<?php declare(strict_types=1);

namespace Hamlet\Type\Types;

use Hamlet\Type\CastException;
use Hamlet\Type\Resolvers\Resolver;
use Hamlet\Type\Type;
use Override;

/**
 * @psalm-internal Hamlet\Type
 * @extends Type<int>
 */
readonly class IntType extends Type
{
    /**
     * @psalm-assert-if-true int $value
     */
    #[Override] public function matches(mixed $value): bool
    {
        return is_int($value);
    }

    #[Override] public function resolveAndCast(mixed $value, Resolver $resolver): mixed
    {
        if ($this->matches($value)) {
            return $value;
        }
        if (is_scalar($value) || is_resource($value)) {
            return (int)$value;
        }
        if ($value == [] || $value === null) {
            return 0;
        }
        return 1;
    }

    #[Override] public function __toString(): string
    {
        return 'int';
    }
}
