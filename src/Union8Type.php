<?php declare(strict_types=1);

namespace Hamlet\Type;

/**
 * @template A
 * @template B
 * @template C
 * @template D
 * @template E
 * @template F
 * @template G
 * @template H
 * @extends Union7Type<A,B,C,D,E,F,G>
 */
readonly class Union8Type extends Union7Type
{
    /**
     * @var Type<H>
     */
    protected Type $h;

    /**
     * @param Type<A> $a
     * @param Type<B> $b
     * @param Type<C> $c
     * @param Type<D> $d
     * @param Type<E> $e
     * @param Type<F> $f
     * @param Type<G> $g
     * @param Type<H> $h
     */
    public function __construct(Type $a, Type $b, Type $c, Type $d, Type $e, Type $f, Type $g, Type $h)
    {
        parent::__construct($a, $b, $c, $d, $e, $f, $g);
        $this->h = $h;
    }

    /**
     * @return list<Type>
     */
    protected function types(): array
    {
        return [$this->a, $this->b, $this->c, $this->d, $this->e, $this->f, $this->g, $this->h];
    }
}
