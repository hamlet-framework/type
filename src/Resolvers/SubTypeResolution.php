<?php

namespace Hamlet\Cast\Resolvers;

use ReflectionClass;

/**
 * @template T as object
 */
class SubTypeResolution
{
    /**
     * @var ReflectionClass
     * @psalm-var ReflectionClass<T>
     */
    private $reflectionClass;

    /**
     * @var Resolver
     */
    private $subTreeResolver;

    /**
     * @param ReflectionClass $reflectionClass
     * @psalm-param ReflectionClass<T> $reflectionClass
     * @param Resolver $subTreeResolver
     */
    public function __construct(ReflectionClass $reflectionClass, Resolver $subTreeResolver)
    {
        $this->reflectionClass = $reflectionClass;
        $this->subTreeResolver = $subTreeResolver;
    }

    /**
     * @return ReflectionClass
     * @psalm-return ReflectionClass<T>
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
