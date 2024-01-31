<?php declare(strict_types=1);

namespace Hamlet\Type;

/**
 * @template A
 * @template B
 * @template C
 * @template D
 * @extends Union3Type<A,B,C>
 */
readonly class Union4Type extends Union3Type
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
     * @return list<Type>
     */
    protected function types(): array
    {
        return [$this->a, $this->b, $this->c, $this->d];
    }
}
