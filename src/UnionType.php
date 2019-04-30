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
     * @psalm-suppress RedundantConditionGivenDocblockType
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

    public function __toString()
    {
        return join('|', $this->as);
    }
}
