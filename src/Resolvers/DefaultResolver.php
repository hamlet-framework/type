<?php declare(strict_types=1);

namespace Hamlet\Type\Resolvers;

use Hamlet\Type\Parser\DocBlockParser;
use Hamlet\Type\Type;
use InvalidArgumentException;
use ReflectionClass;
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
            return ValueResolution::success($source[$propertyName]);
        } elseif (is_object($source) && is_a($source, stdClass::class) && property_exists($source, $propertyName)) {
            /**
             * @psalm-suppress MixedArgument
             */
            return ValueResolution::success($source->{$propertyName});
        }
        return ValueResolution::failure();
    }

    public function setValue($object, string $propertyName, $value)
    {
        if (is_object($object) && is_a($object, stdClass::class)) {
            $object->{$propertyName} = $value;
            return $object;
        } elseif (is_array($object)) {
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
     */
    public function resolveSubType(string $type, $value): SubTypeResolution
    {
        assert(class_exists($type));
        return new SubTypeResolution($this->getReflectionClass($type), $this);
    }

    /**
     * @template P
     * @param ReflectionProperty $property
     * @return Type
     * @psalm-return Type<P>
     */
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
