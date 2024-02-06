<?php declare(strict_types=1);

namespace Hamlet\Type\Types;

use Hamlet\Type\CastException;
use Hamlet\Type\Resolvers\Resolver;
use Hamlet\Type\Type;
use Override;

/**
 * @psalm-internal Hamlet\Type
 * @extends Type<string>
 */
readonly class StringType extends Type
{
    /**
     * @psalm-assert-if-true string $value
     */
    #[Override] public function matches(mixed $value): bool
    {
        return is_string($value);
    }

    #[Override] public function resolveAndCast(mixed $value, Resolver $resolver): mixed
    {
        if ($this->matches($value)) {
            return $value;
        }

        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                return (string)$value;
            } else {
                throw new CastException($value, $this);
            }
        } elseif (is_scalar($value) || is_resource($value)) {
            return (string)$value;
        } elseif (is_array($value)) {
            return 'Array';
        } elseif ($value === null) {
            return '';
        } else {
            throw new CastException($value, $this);
        }
    }

    #[Override] public function __toString(): string
    {
        return 'string';
    }
}
