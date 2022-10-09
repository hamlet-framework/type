<?php declare(strict_types=1);

namespace Hamlet\Type\Resolvers;

use ReflectionClass;

/**
 * @template T as object
 */
class SubTypeResolution
{
    /**
     * @param ReflectionClass<T> $reflectionClass
     * @param Resolver $subTreeResolver
     */
    public function __construct(
        private readonly ReflectionClass $reflectionClass,
        private readonly Resolver $subTreeResolver
    ) {}

    /**
     * @return ReflectionClass<T>
     */
    public function reflectionClass(): ReflectionClass
    {
        return $this->reflectionClass;
    }

    public function subTreeResolver(): Resolver
    {
        return $this->subTreeResolver;
    }
}
