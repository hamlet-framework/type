<?php declare(strict_types=1);

namespace Hamlet\Type\Types;

use Hamlet\Type\CastException;
use Hamlet\Type\Resolvers\Resolver;
use Hamlet\Type\Type;
use Override;

/**
 * @psalm-internal Hamlet\Type
 * @extends Type<numeric-string>
 */
readonly class NumericStringType extends Type
{
    #[Override] public function matches(mixed $value): bool
    {
        return is_string($value) && is_numeric($value);
    }

    #[Override] public function resolveAndCast(mixed $value, Resolver $resolver): mixed
    {
        if ($this->matches($value)) {
            return $value;
        } elseif (is_array($value) || is_null($value)) {
            throw new CastException($value, $this);
        } elseif (is_scalar($value) || is_resource($value)) {
            $value = (string) $value;
        } elseif (is_object($value) && method_exists($value, '__toString')) {
            $value = (string) $value;
        }

        if (!is_string($value) || !is_numeric($value)) {
            throw new CastException($value, $this);
        }
        return $value;
    }

    #[Override] public function __toString(): string
    {
        return 'numeric-string';
    }
}
