<?php

namespace Hamlet\Cast;

/**
 * @template T
 * @extends Type<list<T>>
 */
class ListType extends Type
{
    /**
     * @var Type<T>
     */
    private $elementType;

    /**
     * @param Type<T> $elementType
     */
    public function __construct(Type $elementType)
    {
        $this->elementType = $elementType;
    }

    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true list<T> $value
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
     * @return list<T>
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
        foreach ($value as $v) {
            $result[] = $this->elementType->cast($v);
        }
        return $result;
    }

    public function __toString(): string
    {
        return 'array<' . $this->elementType . '>';
    }
}
