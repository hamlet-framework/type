<?php declare(strict_types=1);

namespace Hamlet\Type\Types;

use Hamlet\Type\Resolvers\Resolver;
use Hamlet\Type\Type;
use Override;

/**
 * @psalm-internal Hamlet\Type
 * @extends Type<bool>
 */
readonly class BoolType extends Type
{
    #[Override] public function matches(mixed $value): bool
    {
        return is_bool($value);
    }

    #[Override] public function resolveAndCast(mixed $value, Resolver $resolver): mixed
    {
        return (bool)$value;
    }

    #[Override] public function __toString(): string
    {
        return 'bool';
    }
}
