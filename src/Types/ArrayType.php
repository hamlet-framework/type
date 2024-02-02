<?php declare(strict_types=1);

namespace Hamlet\Type\Types;

use Hamlet\Type\CastException;
use Hamlet\Type\Resolvers\Resolver;
use Hamlet\Type\Type;
use Override;

/**
 * @psalm-internal Hamlet\Type
 * @template T
 * @extends Type<array<T>>
 */
readonly class ArrayType extends Type
{
    /**
     * @var Type<T>
     */
    private Type $elementType;

    /**
     * @param Type<T> $elementType
     */
    public function __construct(Type $elementType)
    {
        $this->elementType = $elementType;
    }

    #[Override] public function matches(mixed $value): bool
    {
        if (!is_array($value)) {
            return false;
        }
        foreach ($value as $v) {
            if (!$this->elementType->matches($v)) {
                return false;
            }
        }
        return true;
    }

    #[Override] public function resolveAndCast(mixed $value, Resolver $resolver): array
    {
        if ($this->matches($value)) {
            return $value;
        }
        if (!is_array($value)) {
            if (is_scalar($value) || is_object($value) || is_resource($value) || is_null($value)) {
                $value = (array) $value;
            } else {
                throw new CastException($value, $this);
            }
        }

        $result = [];
        foreach ($value as $k => $v) {
            $result[$k] = $this->elementType->resolveAndCast($v, $resolver);
        }
        return $result;
    }

    #[Override] public function __toString(): string
    {
        return 'array<' . $this->elementType . '>';
    }

    #[Override] public function serialize(): string
    {
        return 'new ' . static::class . '(' . $this->elementType->serialize() . ')';
    }
}
