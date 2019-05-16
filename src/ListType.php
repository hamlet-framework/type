<?php

namespace Hamlet\Cast;

/**
 * @template T
 * @extends Type<array<T>>
 */
class ListType extends Type
{
    /**
     * @var Type
     * @psalm-var Type<T>
     */
    private $elementType;

    /**
     * @param Type $elementType
     * @psalm-param Type<T> $elementType
     */
    public function __construct(Type $elementType)
    {
        $this->elementType = $elementType;
    }

    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true array<T> $value
     */
    public function matches($value): bool
    {
        if (!is_array($value)) {
            return false;
        }
        /**
         * @psalm-suppress MixedAssignment
         */
        foreach ($value as $v) {
            if (!$this->elementType->matches($v)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param mixed $value
     * @return array
     * @psalm-return array<T>
     */
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
            $result[$k] = $this->elementType->cast($v);
        }
        return $result;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'array<' . $this->elementType . '>';
    }
}
