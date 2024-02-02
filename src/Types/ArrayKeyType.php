<?php declare(strict_types=1);

namespace Hamlet\Type\Types;

use Hamlet\Type\CastException;
use Hamlet\Type\Resolvers\Resolver;
use Hamlet\Type\Type;
use Override;

/**
 * @psalm-internal Hamlet\Type
 * @extends Type<array-key>
 */
readonly class ArrayKeyType extends Type
{
    #[Override] public function matches(mixed $value): bool
    {
        return is_int($value) || is_string($value);
    }

    #[Override] public function resolveAndCast(mixed $value, Resolver $resolver): mixed
    {
        if ($this->matches($value)) {
            return $value;
        }
        if (is_float($value) || is_bool($value) || is_resource($value)) {
            return (int) $value;
        }
        if (is_null($value)) {
            return '';
        }
        throw new CastException($value, $this);
    }

    #[Override] public function __toString()
    {
        return 'array-key';
    }
}
