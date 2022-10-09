<?php declare(strict_types=1);

namespace Hamlet\Type;

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
     * @var Type<D>
     */
    protected Type $d;

    /**
     * @param Type<A> $a
     * @param Type<B> $b
     * @param Type<C> $c
     * @param Type<D> $d
     */
    public function __construct(Type $a, Type $b, Type $c, Type $d)
    {
        parent::__construct($a, $b, $c);
        $this->d = $d;
    }

    /**
     * @return array{Type<A>,Type<B>,Type<C>,Type<D>}
     */
    protected function types(): array
    {
        return [$this->a, $this->b, $this->c, $this->d];
    }
}
