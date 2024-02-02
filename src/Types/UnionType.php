<?php declare(strict_types=1);

namespace Hamlet\Type\Types;

use Hamlet\Type\CastException;
use Hamlet\Type\Resolvers\Resolver;
use Hamlet\Type\Type;
use Override;

/**
 * @psalm-internal Hamlet\Type
 * @template A
 * @extends Type<A>
 */
readonly class UnionType extends Type
{
    /**
     * @var list<Type<A>>
     */
    private array $options;

    /**
     * @param list<Type<A>> $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    #[Override] public function matches(mixed $value): bool
    {
        foreach ($this->options as $type) {
            if ($type->matches($value)) {
                return true;
            }
        }
        return false;
    }

    #[Override] public function resolveAndCast(mixed $value, Resolver $resolver): mixed
    {
        if ($this->matches($value)) {
            return $value;
        }
        throw new CastException($value, $this);
    }

    #[Override] public function serialize(): string
    {
        return 'new ' . static::class . '([' . join(', ', array_map(fn (Type $a) => $a->serialize(), $this->options)) . '])';
    }

    #[Override] public function __toString(): string
    {
        return join('|', array_map(fn ($option) => (string) $option, $this->options));
    }
}
