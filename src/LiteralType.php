<?php

namespace Hamlet\Cast;

/**
 * @template T
 * @extends Type<T>
 */
class LiteralType extends Type
{
    /**
     * @var array
     * @psalm-var array<T>
     */
    private $values;

    /**
     * @param array $values
     * @psalm-param array<T> $values
     */
    public function __construct(...$values)
    {
        $this->values = $values;
    }

    public function matches($value): bool
    {
        foreach ($this->values as $v) {
            if ($value === $v) {
                return true;
            }
        }
        return false;
    }

    public function cast($value)
    {
        if ($this->matches($value)) {
            return $value;
        }
        foreach ($this->values as $v) {
            if ($value == $v) {
                return $v;
            }
        }
        throw new CastException($value, $this);
    }

    public function __toString()
    {
        $escape =
            /**
             * @param mixed $a
             * @return string
             */
            function ($a): string {
                if (is_string($a)) {
                    return "'$a'";
                }
                if (is_null($a)) {
                    return 'null';
                }
                if (is_bool($a)) {
                    return $a ? 'true' : 'false';
                }
                return (string) $a;
            };
        return '(' . join('|', array_map($escape, $this->values)) . ')';
    }
}
