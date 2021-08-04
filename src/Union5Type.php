<?php declare(strict_types=1);

namespace Hamlet\Cast;

/**
 * @template A
 * @template B
 * @template C
 * @template D
 * @template E
 * @extends Union4Type<A,B,C,D>
 */
class Union5Type extends Union4Type
{
    /**
     * @var Type
     * @psalm-var Type<E>
     */
    protected $e;

    /**
     * @psalm-param Type<A> $a
     * @psalm-param Type<B> $b
     * @psalm-param Type<C> $c
     * @psalm-param Type<D> $d
     * @psalm-param Type<E> $e
     */
    public function __construct(Type $a, Type $b, Type $c, Type $d, Type $e)
    {
        parent::__construct($a, $b, $c, $d);
        $this->e = $e;
    }

    /**
     * @return array<Type>
     * @psalm-return array{Type<A>,Type<B>,Type<C>,Type<D>,Type<E>}
     */
    protected function types(): array
    {
        return [$this->a, $this->b, $this->c, $this->d, $this->e];
    }
}
