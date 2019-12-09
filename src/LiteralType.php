<?php

namespace Hamlet\Cast;

/**
 * @template T
 * @extends Type<T>
 */
class LiteralType extends Type
{
    /**
     * @var array<T>
     */
    private $values;

    /**
     * @param T ...$values
     */
    public function __construct(...$values)
    {
        $this->values = $values;
    }

    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true T $value
     */
    public function matches($value): bool
    {
        foreach ($this->values as $v) {
            if ($value === $v) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param mixed $value
     * @return mixed
     * @psalm-return T
     */
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

    public function __toString(): string
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

        if (count($this->values) > 1) {
            return '(' . join('|', array_map($escape, $this->values)) . ')';
        } else {
            return $escape($this->values[0]);
        }
    }
}
