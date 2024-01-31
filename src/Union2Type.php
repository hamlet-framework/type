<?php declare(strict_types=1);

namespace Hamlet\Type;

use Hamlet\Type\Resolvers\Resolver;

/**
 * @template A
 * @template B
 * @extends Type<A|B>
 */
readonly class Union2Type extends Type
{
    /**
     * @var Type<A>
     */
    protected Type $a;

    /**
     * @var Type<B>
     */
    protected Type $b;

    /**
     * @param Type<A> $a
     * @param Type<B> $b
     */
    public function __construct(Type $a, Type $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    /**
     * @return list<Type>
     */
    protected function types(): array
    {
        return [$this->a, $this->b];
    }

    /**
     * @psalm-assert-if-true A|B $value
     */
    public function matches(mixed $value): bool
    {
        foreach ($this->types() as $a) {
            if ($a->matches($value)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return A|B
     */
    public function resolveAndCast(mixed $value, Resolver $resolver): mixed
    {
        foreach ($this->types() as $a) {
            if ($a->matches($value)) {
                return $value;
            }
        }
        foreach ($this->types() as $a) {
            try {
                return $a->resolveAndCast($value, $resolver);
            } catch (CastException $_) {
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
