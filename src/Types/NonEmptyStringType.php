<?php declare(strict_types=1);

namespace Hamlet\Type\Types;

use Hamlet\Type\CastException;
use Hamlet\Type\Resolvers\Resolver;
use Hamlet\Type\Type;
use Override;

/**
 * @psalm-internal Hamlet\Type
 * @extends Type<non-empty-string>
 */
readonly class NonEmptyStringType extends Type
{
    /**
     * @psalm-assert-if-true non-empty-string $value
     */
    #[Override] public function matches(mixed $value): bool
    {
        return is_string($value) && $value !== '';
    }

    #[Override] public function resolveAndCast(mixed $value, Resolver $resolver): mixed
    {
        if ($this->matches($value)) {
            return $value;
        }

        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                $stringValue = (string)$value;
            } else {
                throw new CastException($value, $this);
            }
        } elseif (is_array($value)) {
            $stringValue = 'Array';
        } elseif (is_scalar($value) || is_resource($value) || is_null($value)) {
            $stringValue = (string)$value;
        } else {
            throw new CastException($value, $this);
        }

        if ($stringValue === '') {
            throw new CastException($value, $this);
        }
        return $stringValue;
    }

    #[Override] public function __toString(): string
    {
        return 'non-empty-string';
    }
}
