<?php declare(strict_types=1);

namespace Hamlet\Cast;

/**
 * @template A
 * @template B
 * @template C
 * @extends Union2Type<A,B>
 */
class Union3Type extends Union2Type
{
    /**
     * @var Type
     * @psalm-var Type<C>
     */
    protected $c;

    /**
     * @psalm-param Type<A> $a
     * @psalm-param Type<B> $b
     * @psalm-param Type<C> $c
     */
    public function __construct(Type $a, Type $b, Type $c)
    {
        parent::__construct($a, $b);
        $this->c = $c;
    }

    /**
     * @return array<Type>
     * @psalm-return array{Type<A>,Type<B>,Type<C>}
     */
    protected function types(): array
    {
        return [$this->a, $this->b, $this->c];
    }
}
