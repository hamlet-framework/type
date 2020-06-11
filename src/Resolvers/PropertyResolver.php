<?php

namespace Hamlet\Cast\Resolvers;

use stdClass;

class PropertyResolver
{
    /**
     * @template T
     * @param string|null $type
     * @psalm-param class-string<T>|null $type
     * @param string $property
     * @param stdClass|array $source
     * @return Resolution
     * @psalm-suppress RedundantConditionGivenDocblockType
     * @psalm-suppress MixedArgument
     */
    public function resolve($type, string $property, $source): Resolution
    {
        if (is_array($source) && array_key_exists($property, $source)) {
            return new Resolution(true, $source[$property]);
        } elseif (is_object($source) && is_a($source, stdClass::class) && property_exists($source, $property)) {
            return new Resolution(true, $source->{$property});
        }
        return new Resolution(false, null);
    }
}
