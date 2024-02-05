<?php declare(strict_types=1);

namespace Hamlet\Type\Types;

use Hamlet\Type\CastException;
use Hamlet\Type\Resolvers\Resolver;
use Hamlet\Type\Type;
use Override;

/**
 * @psalm-internal Hamlet\Type
 * @extends Type<null>
 */
readonly class NullType extends Type
{
    /**
     * @psalm-assert-if-true null $value
     */
    #[Override] public function matches(mixed $value): bool
    {
        return is_null($value);
    }

    #[Override] public function resolveAndCast(mixed $value, Resolver $resolver): mixed
    {
        if ($value == null) {
            return null;
        }
        throw new CastException($value, $this);
    }

    #[Override] public function __toString(): string
    {
        return 'null';
    }
}
