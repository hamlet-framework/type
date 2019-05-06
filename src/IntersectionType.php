<?php

namespace Hamlet\Cast;

/**
 * @template T
 * @template U
 * @extends Type<T & U>
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

    public function matches($value): bool
    {
        return $this->a->matches($value) && $this->b->matches($value);
    }

    public function cast($value)
    {
        return $this->b->cast($this->a->cast($value));
    }

    /**
     * @return PropertyType[]|null
     */
    private function properties()
    {
        if ($this->a instanceof PropertyType) {
            if ($this->b instanceof PropertyType) {
                return [$this->a, $this->b];
            } elseif ($this->b instanceof IntersectionType) {
                $properties = $this->b->properties();
                if ($properties) {
                    array_unshift($properties, $this->a);
                    return $properties;
                }
            }
        }
        return null;
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
