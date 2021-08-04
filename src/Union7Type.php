<?php declare(strict_types=1);

namespace Hamlet\Cast;

/**
 * @template A
 * @template B
 * @template C
 * @template D
 * @template E
 * @template F
 * @template G
 * @extends Union6Type<A,B,C,D,E,F>
 */
class Union7Type extends Union6Type
{
    /**
     * @var Type
     * @psalm-var Type<G>
     */
    protected $g;

    /**
     * @psalm-param Type<A> $a
     * @psalm-param Type<B> $b
     * @psalm-param Type<C> $c
     * @psalm-param Type<D> $d
     * @psalm-param Type<E> $e
     * @psalm-param Type<F> $f
     * @psalm-param Type<G> $g
     */
    public function __construct(Type $a, Type $b, Type $c, Type $d, Type $e, Type $f, Type $g)
    {
        parent::__construct($a, $b, $c, $d, $e, $f);
        $this->g = $g;
    }

    /**
     * @return array<Type>
     * @psalm-return array{Type<A>,Type<B>,Type<C>,Type<D>,Type<E>,Type<F>,Type<G>}
     */
    protected function types(): array
    {
        return [$this->a, $this->b, $this->c, $this->d, $this->e, $this->f, $this->g];
    }
}