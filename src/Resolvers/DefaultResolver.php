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
     * @var ReflectionClass[]
     * @psalm-var array<string,ReflectionClass>
     */
    private static $reflectionClasses = [];

    /**
     * @var Type[][]
     * @psalm-var array<string,array<string,Type>>
     */
    private static $propertyTypes = [];

    public function getValue($type, string $propertyName, $source): ValueResolution
    {
        if (is_array($source) && array_key_exists($propertyName, $source)) {
            return ValueResolution::success($source[$propertyName], $propertyName);
        } elseif (is_object($source) && is_a($source, stdClass::class) && property_exists($source, $propertyName)) {
            /**
             * @psalm-suppress MixedArgument
             */
            return ValueResolution::success($source->{$propertyName}, $propertyName);
        }
        return ValueResolution::failure();
    }

    public function setValue($object, string $propertyName, $value)
    {
        if (is_array($object)) {
            $object[$propertyName] = $value;
            return $object;
        } elseif (is_object($object)) {
            if (is_a($object, stdClass::class)) {
                $object->{$propertyName} = $value;
                return $object;
            } else {
                $reflectionClass = $this->getReflectionClass(get_class($object));
                $property = $reflectionClass->getProperty($propertyName);
                $property->setAccessible(true);
                $property->setValue($object, $value);
                return $object;
            }
        } else {
            throw new InvalidArgumentException('Unexpected type ' . var_export($object, true));
        }
    }

    /**
     * @template T
     * @param string $type
     * @psalm-param class-string<T> $type
     * @return ReflectionClass
     * @psalm-return ReflectionClass<T>
     * @psalm-suppress MixedReturnTypeCoercion
     * @throws ReflectionException
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
     * @param string $type
     * @psalm-param class-string<T> $type
     * @param mixed $value
     * @return SubTypeResolution
     * @psalm-return SubTypeResolution<T>
     *
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress MixedArgumentTypeCoercion
     * @psalm-suppress MixedReturnTypeCoercion
     *
     * @throws ReflectionException
     */
    public function resolveSubType(string $type, $value): SubTypeResolution
    {
        assert(class_exists($type));
        return new SubTypeResolution($this->getReflectionClass($type), $this);
    }

    /**
     * @template P
     * @param ReflectionClass $reflectionClass
     * @param ReflectionProperty $reflectionProperty
     * @return Type
     * @psalm-return Type<P>
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
