<?php declare(strict_types=1);

namespace Hamlet\Type\Types;

use Hamlet\Type\CastException;
use Hamlet\Type\Resolvers\Resolver;
use Hamlet\Type\Type;
use Override;

/**
 * @psalm-internal Hamlet\Type
 * @extends Type<object>
 */
readonly class ObjectType extends Type
{
    #[Override] public function matches(mixed $value): bool
    {
        return is_object($value);
    }

    #[Override] public function resolveAndCast(mixed $value, Resolver $resolver): mixed
    {
        if ($this->matches($value)) {
            return $value;
        }
        if (is_scalar($value) || is_array($value) || is_resource($value) || is_null($value)) {
            return (object)$value;
        }
        throw new CastException($value, $this);
    }

    #[Override] public function __toString(): string
    {
        return 'object';
    }
}
