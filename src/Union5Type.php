<?php declare(strict_types=1);

namespace Hamlet\Type;

/**
 * @template A
 * @template B
 * @template C
 * @template D
 * @template E
 * @extends Union4Type<A,B,C,D>
 */
readonly class Union5Type extends Union4Type
{
    /**
     * @var Type<E>
     */
    protected Type $e;

    /**
     * @param Type<A> $a
     * @param Type<B> $b
     * @param Type<C> $c
     * @param Type<D> $d
     * @param Type<E> $e
     */
    public function __construct(Type $a, Type $b, Type $c, Type $d, Type $e)
    {
        parent::__construct($a, $b, $c, $d);
        $this->e = $e;
    }

    /**
     * @return list<Type>
     */
    protected function types(): array
    {
        return [$this->a, $this->b, $this->c, $this->d, $this->e];
    }
}
