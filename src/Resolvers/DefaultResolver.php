<?php declare(strict_types=1);

namespace Hamlet\Cast\Resolvers;

use Hamlet\Cast\Parser\DocBlockParser;
use Hamlet\Cast\Type;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use stdClass;

class DefaultResolver implements Resolver
{
    /**
     * @var array<string,ReflectionClass>
     */
    private static array $reflectionClasses = [];

    /**
     * @var array<string,array<string,Type>>
     */
    private static array $propertyTypes = [];

    public function getValue($type, string $propertyName, $source): ValueResolution
    {
        if (is_array($source) && array_key_exists($propertyName, $source)) {
            return ValueResolution::success($source[$propertyName], $propertyName);
        } elseif (is_object($source) && is_a($source, stdClass::class) && property_exists($source, $propertyName)) {
            return ValueResolution::success($source->{$propertyName}, $propertyName);
        }
        return ValueResolution::failure();
    }

    /**
     * @throws ReflectionException
     */
    public function setValue(object|array $object, string $propertyName, mixed $value): object|array
    {
        if (is_array($object)) {
            $object[$propertyName] = $value;
            return $object;
        } elseif (is_object($object)) {
            if (is_a($object, stdClass::class)) {
                $object->{$propertyName} = $value;
            } else {
                $reflectionClass = $this->getReflectionClass(get_class($object));
                $property = $reflectionClass->getProperty($propertyName);
                $property->setAccessible(true);
                $property->setValue($object, $value);
            }
            return $object;
        } else {
            throw new InvalidArgumentException('Unexpected type ' . var_export($object, true));
        }
    }

    /**
     * @template T
     * @param class-string<T> $type
     * @return ReflectionClass<T>
     * @psalm-suppress MixedReturnTypeCoercion
     */
    protected function getReflectionClass(string $type): ReflectionClass
    {
        if (!isset(self::$reflectionClasses[$type])) {
            assert(class_exists($type));
            self::$reflectionClasses[$type] = new ReflectionClass($type);
        }
        return self::$reflectionClasses[$type];
    }

    /**
     * @template T
     * @param class-string<T> $type
     * @param mixed $value
     * @return SubTypeResolution<T>
     */
    public function resolveSubType(string $type, mixed $value): SubTypeResolution
    {
        assert(class_exists($type));
        return new SubTypeResolution($this->getReflectionClass($type), $this);
    }

    /**
     * @template P
     * @param ReflectionClass $reflectionClass
     * @param ReflectionProperty $reflectionProperty
     * @return Type<P>
     */
    public function getPropertyType(ReflectionClass $reflectionClass, ReflectionProperty $reflectionProperty): Type
    {
        $className = $reflectionClass->getName();
        $propertyName = $reflectionProperty->getName();

        if (!isset(self::$propertyTypes[$className][$propertyName])) {
            self::$propertyTypes[$className][$propertyName] =
                DocBlockParser::fromProperty($reflectionClass, $reflectionProperty);
        }
        return self::$propertyTypes[$className][$propertyName];
    }

    public function ignoreUnmappedProperties(): bool
    {
        return true;
    }
}
