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
        $options = array_unique($options);
        usort($options, fn (Type $a, Type $b) =>
            $a instanceof NullType <=> $b instanceof NullType
        );
        $this->options = $options;
    }

    /**
     * @psalm-assert-if-true A $value
     */
    #[Override] public function matches(mixed $value): bool
    {
        foreach ($this->options as $option) {
            if ($option->matches($value)) {
                return true;
            }
        }
        return false;
    }

    #[Override] public function resolveAndCast(mixed $value, Resolver $resolver): mixed
    {
        $candidates = [];
        foreach ($this->options as $option) {
            if ($option->matches($value)) {
                return $value;
            } elseif (!$option instanceof NullType) {
                $candidates[] = $option;
            }
        }
        if (count($candidates) == 1) {
            return $candidates[0]->resolveAndCast($value, $resolver);
        }
        throw new CastException($value, $this);
    }

    #[Override] public function serialize(): string
    {
        return 'new ' . static::class . '([' . join(', ', array_map(fn (Type $a) => $a->serialize(), $this->options)) . '])';
    }

    #[Override] public function __toString(): string
    {
        return join('|', array_map(fn ($option) => (string)$option, $this->options));
    }
}
