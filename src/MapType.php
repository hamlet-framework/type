<?php

namespace Hamlet\Cast;

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

    public function matches($value): bool
    {
        if (!is_array($value)) {
            return false;
        }
        /**
         * @psalm-suppress MixedAssignment
         */
        foreach ($value as $k => $v) {
            if (!$this->keyType->matches($k) || !$this->valueType->matches($v)) {
                return false;
            }
        }
        return true;
    }

    public function cast($value)
    {
        if (!is_array($value)) {
            throw new CastException($value, $this);
        }
        $result = [];
        /**
         * @psalm-suppress MixedAssignment
         */
        foreach ($value as $k => $v) {
            $result[$this->keyType->cast($k)] = $this->valueType->cast($v);
        }
        return $result;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'array<' . $this->keyType . ',' . $this->valueType . '>';
    }
}
