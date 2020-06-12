<?php

namespace Hamlet\Cast\Resolvers;

use Hamlet\Cast\Parser\DocBlockParser;
use Hamlet\Cast\Type;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use stdClass;

class Resolver
{
    /**
     * @var ReflectionClass[]
     * @psalm-var array<class-string, ReflectionClass>
     */
    private static $reflectionClasses = [];

    /**
     * @var Type[][]
     * @psalm-var array<class-string,array<string,Type>>
     */
    private static $propertyTypes = [];

    /**
     * @template T
     * @param string|null $type
     * @psalm-param class-string<T>|null $type
     * @param string $propertyName
     * @param stdClass|array $source
     * @return ValueResolution
     * @psalm-suppress RedundantConditionGivenDocblockType
     * @psalm-suppress MixedArgument
     */
    public function getValue($type, string $propertyName, $source): ValueResolution
    {
        if (is_array($source) && array_key_exists($propertyName, $source)) {
            return ValueResolution::success($source[$propertyName]);
        } elseif (is_object($source) && is_a($source, stdClass::class) && property_exists($source, $propertyName)) {
            return ValueResolution::success($source->{$propertyName});
        }
        return ValueResolution::failure();
    }

    /**
     * @template T
     * @param stdClass|array|object $object
     * @param string $propertyName
     * @param mixed $value
     * @psalm-param T $value
     * @return stdClass|array|object
     * @psalm-suppress RedundantConditionGivenDocblockType
     * @throws ReflectionException
     */
    public function setValue($object, string $propertyName, $value)
    {
        if (is_object($object) && is_a($object, \stdClass::class)) {
            $object->{$propertyName} = $value;
            return $object;
        } elseif (is_array($object)) {
            /** @psalm-suppress MixedAssignment */
            $object[$propertyName] = $value;
            return $object;
        } elseif (is_object($object)) {
            $reflectionClass = $this->getReflectionClass(get_class($object));
            $property = $reflectionClass->getProperty($propertyName);
            $property->setAccessible(true);
            $property->setValue($object, $value);
            return $object;
        }
        throw new InvalidArgumentException('Unexpected type ' . var_export($object, true));
    }

    /**
     * @template T as object
     * @param string $type
     * @psalm-param class-string<T> $type
     * @return ReflectionClass
     * @psalm-return ReflectionClass<T>
     * @throws ReflectionException
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function getReflectionClass(string $type): ReflectionClass
    {
        if (!isset(self::$reflectionClasses[$type])) {
            self::$reflectionClasses[$type] = new ReflectionClass($type);
        }
        return self::$reflectionClasses[$type];
    }

    public function getPropertyType(ReflectionProperty $property): Type
    {
        $type = $property->getDeclaringClass()->getName();
        $propertyName = $property->getName();

        if (!isset(self::$propertyTypes[$type][$propertyName])) {
            self::$propertyTypes[$type][$propertyName] = DocBlockParser::fromProperty($property);
        }
        return self::$propertyTypes[$type][$propertyName];
    }
}
