<?php declare(strict_types=1);

namespace Hamlet\Type\Types;

use Hamlet\Type\Resolvers\Resolver;
use Hamlet\Type\Type;
use Override;

/**
 * @psalm-internal Hamlet\Type
 * @extends Type<mixed>
 */
readonly class MixedType extends Type
{
    /**
     * @psalm-assert-if-true mixed $value
     */
    #[Override] public function matches(mixed $value): bool
    {
        return true;
    }

    #[Override] public function resolveAndCast(mixed $value, Resolver $resolver): mixed
    {
        return $value;
    }

    #[Override] public function __toString(): string
    {
        return 'mixed';
    }
}
