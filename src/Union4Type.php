<?php declare(strict_types=1);

namespace Hamlet\Cast;

/**
 * @template A
 * @template B
 * @template C
 * @template D
 * @extends Union3Type<A,B,C>
 */
class Union4Type extends Union3Type
{
    /**
     * @var Type
     * @psalm-var Type<D>
     */
    protected $d;

    /**
     * @psalm-param Type<A> $a
     * @psalm-param Type<B> $b
     * @psalm-param Type<C> $c
     * @psalm-param Type<D> $d
     */
    public function __construct(Type $a, Type $b, Type $c, Type $d)
    {
        parent::__construct($a, $b, $c);
        $this->d = $d;
    }

    /**
     * @return array<Type>
     * @psalm-return array{Type<A>,Type<B>,Type<C>,Type<D>}
     */
    protected function types(): array
    {
        return [$this->a, $this->b, $this->c, $this->d];
    }
}
