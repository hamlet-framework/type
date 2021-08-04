<?php declare(strict_types=1);

namespace Hamlet\Cast;

use Hamlet\Cast\Resolvers\Resolver;

/**
 * @template A
 * @template B
 * @extends Type<A|B>
 */
class Union2Type extends Type
{
    /**
     * @var Type
     * @psalm-var Type<A>
     */
    protected $a;

    /**
     * @var Type
     * @psalm-var Type<B>
     */
    protected $b;

    /**
     * @psalm-param Type<A> $a
     * @psalm-param Type<B> $b
     */
    public function __construct(Type $a, Type $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    /**
     * @return array<Type>
     * @psalm-return array{Type<A>,Type<B>}
     */
    protected function types(): array
    {
        return [$this->a, $this->b];
    }

    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true A|B $value
     */
    public function matches($value): bool
    {
        foreach ($this->types() as $a) {
            if ($a->matches($value)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param mixed $value
     * @param Resolver $resolver
     * @return mixed
     * @psalm-return A|B
     */
    public function resolveAndCast($value, Resolver $resolver)
    {
        foreach ($this->types() as $a) {
            if ($a->matches($value)) {
                return $value;
            }
        }
        foreach ($this->types() as $a) {
            try {
                return $a->resolveAndCast($value, $resolver);
            } catch (CastException $e) {
            }
        }
        throw new CastException($value, $this);
    }

    public function __toString(): string
    {
        $tokens = [];
        foreach ($this->types() as $a) {
            $tokens[] = (string) $a;
        }
        return join('|', $tokens);
    }

    public function serialize(): string
    {
        $arguments = [];
        foreach ($this->types() as $a) {
            $arguments[] = $a->serialize();
        }
        return 'new ' . static::class . '(' . join(', ', $arguments) . ')';
    }
}
