<?php

namespace Hamlet\Cast;

/**
 * @todo please note, that intersection types are only rudimentary supported by psalm at this moment
 *
 * @template T
 * @template U
 * @extends Type<T&U>
 */
class IntersectionType extends Type
{
    /**
     * @var Type
     * @psalm-var Type<T>
     */
    private $a;

    /**
     * @var Type
     * @psalm-var Type<U>
     */
    private $b;

    /**
     * @param Type $a
     * @psalm-param Type<T> $a
     * @param Type $b
     * @psalm-param Type<U> $b
     */
    public function __construct(Type $a, Type $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true T&U $value
     */
    public function matches($value): bool
    {
        return $this->a->matches($value) && $this->b->matches($value);
    }

    /**
     * @param mixed $value
     * @return mixed
     * @psalm-return T&U
     */
    public function cast($value)
    {
        $convertedValue = $this->b->cast($this->a->cast($value));
        if (!$this->a->matches($convertedValue) || !$this->b->matches($convertedValue)) {
            throw new CastException($value, $this);
        }
        return $convertedValue;
    }

    /**
     * @return PropertyType[]
     */
    private function properties()
    {
        $properties = [];
        if ($this->a instanceof PropertyType) {
            $properties[] = $this->a;
        }
        if ($this->b instanceof PropertyType) {
            $properties[] = $this->b;
        }
        if ($this->a instanceof IntersectionType) {
            $properties = array_merge($properties, $this->a->properties());
        }
        if ($this->b instanceof IntersectionType) {
            $properties = array_merge($properties, $this->b->properties());
        }
        return $properties;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $properties = $this->properties();
        if ($properties) {
            $items = [];
            foreach ($properties as $property) {
                $items[] = $property->name() . ($property->required() ? '' : '?') . ':' . $property->type();
            }
            return 'array{' . join(',', $items) . '}';
        } else {
            $a = ($this->a instanceof UnionType) ? '(' . $this->a . ')' : ((string) $this->a);
            $b = ($this->b instanceof UnionType) ? '(' . $this->b . ')' : ((string) $this->b);
            return $a . ' & ' . $b;
        }
    }
}
