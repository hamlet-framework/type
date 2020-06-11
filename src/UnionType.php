<?php

namespace Hamlet\Cast;

use Hamlet\Cast\Resolvers\PropertyResolver;

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
     * @param Type ...$as
     * @psalm-param Type<A> ...$as
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
     * @param PropertyResolver $resolver
     * @return mixed
     * @psalm-return A
     */
    public function resolveAndCast($value, PropertyResolver $resolver)
    {
        foreach ($this->as as $a) {
            if ($a->matches($value)) {
                return $value;
            }
        }
        foreach ($this->as as $a) {
            try {
                return $a->resolveAndCast($value, $resolver);
            } catch (CastException $e) {
            }
        }
        throw new CastException($value, $this);
    }

    public function __toString(): string
    {
        $tokens = [];
        foreach ($this->as as $a) {
            $tokens[] = (string) $a;
        }
        return join('|', $tokens);
    }
}
