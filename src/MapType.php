<?php

namespace Hamlet\Cast;

use stdClass;

/**
 * @template K as array-key
 * @template V
 * @extends Type<array<K,V>>
 */
class MapType extends Type
{
    /**
     * @var Type
     * @psalm-var Type<K>
     */
    private $keyType;

    /**
     * @var Type
     * @psalm-var Type<V>
     */
    private $valueType;

    /**
     * @param Type $keyType
     * @psalm-param Type<K> $keyType
     * @param Type $valueType
     * @psalm-param Type<V> $valueType
     */
    public function __construct(Type $keyType, Type $valueType)
    {
        $this->keyType = $keyType;
        $this->valueType = $valueType;
    }

    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true array<K,V> $value
     */
    public function matches($value): bool
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
     * @param mixed $value
     * @return array
     * @psalm-return array<K,V>
     * @psalm-suppress InvalidReturnType
     */
    public function cast($value)
    {
        if (is_array($value) || is_object($value) && is_a($value, stdClass::class)) {
            $result = [];
            /**
             * @psalm-suppress MixedAssignment
             */
            foreach (((array) $value) as $k => $v) {
                $result[$this->keyType->cast($k)] = $this->valueType->cast($v);
            }
            return $result;
        }
        throw new CastException($value, $this);
    }

    public function __toString(): string
    {
        return 'array<' . $this->keyType . ',' . $this->valueType . '>';
    }
}
