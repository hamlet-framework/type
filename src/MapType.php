<?php declare(strict_types=1);

namespace Hamlet\Type;

use Hamlet\Type\Resolvers\Resolver;
use stdClass;

/**
 * @template K as array-key
 * @template V
 * @extends Type<array<K,V>>
 */
readonly class MapType extends Type
{
    /**
     * @var Type<K>
     */
    private Type $keyType;

    /**
     * @var Type<V>
     */
    private Type $valueType;

    /**
     * @param Type<K> $keyType
     * @param Type<V> $valueType
     */
    public function __construct(Type $keyType, Type $valueType)
    {
        $this->keyType = $keyType;
        $this->valueType = $valueType;
    }

    /**
     * @psalm-assert-if-true array<K,V> $value
     */
    public function matches(mixed $value): bool
    {
        if (!is_array($value)) {
            return false;
        }
        /**
         * @psalm-suppress MixedAssignment
         * @psalm-suppress TypeDoesNotContainType
         */
        foreach ($value as $k => $v) {
            if (!$this->keyType->matches($k) || !$this->valueType->matches($v)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return array<K,V>
     */
    public function resolveAndCast(mixed $value, Resolver $resolver): array
    {
        if (!(is_array($value) || is_object($value) && is_a($value, stdClass::class))) {
            throw new CastException($value, $this);
        }
        $result = [];
        /**
         * @psalm-suppress MixedAssignment
         */
        foreach (((array) $value) as $k => $v) {
            $result[$this->keyType->resolveAndCast($k, $resolver)] = $this->valueType->resolveAndCast($v, $resolver);
        }
        return $result;
    }

    public function __toString(): string
    {
        return 'array<' . $this->keyType . ',' . $this->valueType . '>';
    }

    public function serialize(): string
    {
        return 'new ' . static::class . '(' . $this->keyType->serialize() . ', ' . $this->valueType->serialize() . ')';
    }
}
