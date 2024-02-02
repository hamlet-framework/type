<?php declare(strict_types=1);

namespace Hamlet\Type\Resolvers;

use Hamlet\Type\Type;
use ReflectionClass;
use ReflectionProperty;
use stdClass;

/**
 * @todo add PHPDoc to this interface
 */
interface Resolver
{
    /**
     * @template T
     * @param class-string<T>|null $type
     * @param string $propertyName
     * @param stdClass|array $source
     * @return ValueResolution
     */
    public function getValue(?string $type, string $propertyName, stdClass|array $source): ValueResolution;

    /**
     * @template T
     * @param object|array $object
     * @param string $propertyName
     * @param T $value
     * @return object|array updated object
     */
    public function setValue(object|array $object, string $propertyName, mixed $value): object|array;

    /**
     * @template T
     * @param class-string<T> $type
     * @param mixed $value
     * @return SubTypeResolution<T>
     */
    public function resolveSubType(string $type, mixed $value): SubTypeResolution;

    /**
     * @param ReflectionClass $reflectionClass
     * @param ReflectionProperty $reflectionProperty
     * @return Type
     */
    public function getPropertyType(ReflectionClass $reflectionClass, ReflectionProperty $reflectionProperty): Type;

    public function ignoreUnmappedProperties(): bool;
}
