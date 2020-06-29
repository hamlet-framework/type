<?php declare(strict_types=1);

namespace Hamlet\Cast\Resolvers;

use Hamlet\Cast\Type;
use ReflectionClass;
use ReflectionProperty;
use stdClass;

interface Resolver
{
    /**
     * @template T
     * @param string|null $type
     * @psalm-param class-string<T>|null $type
     * @param string $propertyName
     * @param stdClass|array $source
     * @return ValueResolution
     */
    public function getValue($type, string $propertyName, $source): ValueResolution;

    /**
     * @template T
     * @param stdClass|array|object $object
     * @param string $propertyName
     * @param mixed $value
     * @psalm-param T $value
     * @return stdClass|array|object updated object
     */
    public function setValue($object, string $propertyName, $value);

    /**
     * @template T
     * @param string $type
     * @psalm-param class-string<T> $type
     * @param mixed $value
     * @return SubTypeResolution
     * @psalm-return SubTypeResolution<T>
     */
    public function resolveSubType(string $type, $value): SubTypeResolution;

    /**
     * @template P
     * @param ReflectionClass $reflectionClass
     * @param ReflectionProperty $reflectionProperty
     * @return Type
     * @psalm-return Type<P>
     */
    public function getPropertyType(ReflectionClass $reflectionClass, ReflectionProperty $reflectionProperty): Type;
}
