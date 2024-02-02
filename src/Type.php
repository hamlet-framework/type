<?php declare(strict_types=1);

namespace Hamlet\Type;

use Hamlet\Type\Resolvers\DefaultResolver;
use Hamlet\Type\Resolvers\Resolver;
use Stringable;

/**
 * @template T
 */
abstract readonly class Type implements Stringable
{
    /**
     * Checks if the given value matches the type.
     *
     * @psalm-assert-if-true T $value
     * @param mixed $value The value to be checked.
     * @return bool True if the value matches the type, false otherwise.
     */
    abstract public function matches(mixed $value): bool;

    /**
     * Asserts that the given value matches the type.
     * Returns the value if it matches; otherwise, it throws a CastException.
     * When PHP assertions are disabled, this method acts as a simple pass-through.
     *
     * @param mixed $value The value to be asserted.
     * @return T The asserted value.
     * @throws CastException If the value does not match the type.
     * @psalm-assert T $value
     */
    final public function assert(mixed $value): mixed
    {
        assert($this->matches($value), new CastException($value, $this));
        return $value;
    }

    /**
     * Casts the given value to the specified type.
     * Throws a CastException if the value cannot be cast to the type.
     *
     * @param mixed $value The value to be cast.
     * @return T The value cast to the type.
     * @throws CastException If the value cannot be cast to the type.
     */
    final public function cast(mixed $value): mixed
    {
        $resolver = new DefaultResolver;
        return $this->resolveAndCast($value, $resolver);
    }

    /**
     * @todo Casts the value to the current type, use the resolver for whatever reason. I forgot completely
     *
     * @param mixed $value The value to be cast.
     * @param Resolver $resolver DO NOT REMEMBER WHAT IT WAS
     * @return T The value cast to the type.
     * @throws CastException If the value cannot be cast to the type.
     */
    public function resolveAndCast(mixed $value, Resolver $resolver): mixed
    {
        return $this->cast($value);
    }

    /**
     * @todo rewrite this PHPDoc
     * Returns PHP code that can produce the current type object when evaluated.
     * This is only used in Cache, so the methods don't need to implement this method.
     *
     * @return string
     */
    public function serialize(): string
    {
        return 'new ' . static::class;
    }
}
