<?php declare(strict_types=1);

namespace Hamlet\Type\Types;

use Hamlet\Type\CastException;
use Hamlet\Type\Resolvers\Resolver;
use Hamlet\Type\Type;
use Override;

/**
 * @psalm-internal Hamlet\Type
 * @extends Type<float>
 */
readonly class FloatType extends Type
{
    #[Override] public function matches(mixed $value): bool
    {
        return is_float($value);
    }

    #[Override] public function resolveAndCast(mixed $value, Resolver $resolver): mixed
    {
        if ($this->matches($value)) {
            return $value;
        }
        if (is_scalar($value) || is_resource($value)) {
            return (float) $value;
        }
        if ($value == []) {
            return 0.0;
        }
        if ($value !== null) {
            return 1.0;
        }
        throw new CastException($value, $this);
    }

    #[Override] public function __toString(): string
    {
        return 'float';
    }
}
