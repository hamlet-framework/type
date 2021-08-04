<?php declare(strict_types=1);

namespace Hamlet\Cast;

/**
 * @template A
 * @template B
 * @template C
 * @template D
 * @template E
 * @template F
 * @extends Union5Type<A,B,C,D,E>
 */
class Union6Type extends Union5Type
{
    /**
     * @var Type
     * @psalm-var Type<F>
     */
    protected $f;

    /**
     * @psalm-param Type<A> $a
     * @psalm-param Type<B> $b
     * @psalm-param Type<C> $c
     * @psalm-param Type<D> $d
     * @psalm-param Type<E> $e
     * @psalm-param Type<F> $f
     */
    public function __construct(Type $a, Type $b, Type $c, Type $d, Type $e, Type $f)
    {
        parent::__construct($a, $b, $c, $d, $e);
        $this->f = $f;
    }

    /**
     * @return array<Type>
     * @psalm-return array{Type<A>,Type<B>,Type<C>,Type<D>,Type<E>,Type<F>}
     */
    protected function types(): array
    {
        return [$this->a, $this->b, $this->c, $this->d, $this->e, $this->f];
    }
}
