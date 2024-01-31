<?php declare(strict_types=1);

namespace Hamlet\Type;

/**
 * @template A
 * @template B
 * @template C
 * @template D
 * @template E
 * @template F
 * @extends Union5Type<A,B,C,D,E>
 */
readonly class Union6Type extends Union5Type
{
    /**
     * @var Type<F>
     */
    protected Type $f;

    /**
     * @param Type<A> $a
     * @param Type<B> $b
     * @param Type<C> $c
     * @param Type<D> $d
     * @param Type<E> $e
     * @param Type<F> $f
     */
    public function __construct(Type $a, Type $b, Type $c, Type $d, Type $e, Type $f)
    {
        parent::__construct($a, $b, $c, $d, $e);
        $this->f = $f;
    }

    /**
     * @return list<Type>
     */
    protected function types(): array
    {
        return [$this->a, $this->b, $this->c, $this->d, $this->e, $this->f];
    }
}
