<?php

namespace Hamlet\Cast;

/**
 * @template A
 * @extends Type<A>
 */
class UnionType extends Type
{
    /**
     * @var Type[]
     * @psalm-var array<Type<A>>
     */
    private $as;

    /**
     * @param Type[] $as
     * @psalm-param array<Type<A>> $as
     */
    public function __construct(...$as)
    {
        $this->as = $as;
    }

    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true A $value
     */
    public function matches($value): bool
    {
        foreach ($this->as as $a) {
            if ($a->matches($value)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param mixed $value
     * @return mixed
     * @psalm-return A $value
     */
    public function cast($value)
    {
        foreach ($this->as as $a) {
            if ($a->matches($value)) {
                return $value;
            }
        }
        foreach ($this->as as $a) {
            try {
                return $a->cast($value);
            } catch (CastException $e) {
            }
        }
        throw new CastException($value, $this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $tokens = [];
        foreach ($this->as as $a) {
            if ($a instanceof IntersectionType) {
                $tokens[] = '(' . $a . ')';
            } else {
                $tokens[] = (string) $a;
            }
        }
        return join('|', $tokens);
    }
}
