<?php declare(strict_types=1);

namespace Hamlet\Cast;

use Hamlet\Cast\Parser\DocBlockParser;
use Hamlet\Cast\Resolvers\PropertyResolver;
use ReflectionClass;
use ReflectionException;
use stdClass;

/**
 * @template T as object
 * @extends Type<T>
 */
class ClassType extends Type
{
    /**
     * @var string
     * @psalm-var class-string<T>
     */
    private $type;

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
     * @param string $type
     * @psalm-param class-string<T> $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true T $value
     */
    public function matches($value): bool
    {
        return is_object($value) && is_a($value, $this->type);
    }

    /**
     * @param mixed $value
     * @param PropertyResolver $resolver
     * @return object
     * @throws ReflectionException
     * @psalm-return T
     * @psalm-suppress MixedAssignment
     * @psalm-suppress InvalidReturnStatement
     */
    public function resolveAndCast($value, PropertyResolver $resolver)
    {
        if ($this->matches($value)) {
            return $value;
        }

        if (is_object($value) && is_a($value, stdClass::class) || is_array($value)) {
            if (!isset(self::$reflectionClasses[$this->type])) {
                self::$reflectionClasses[$this->type] = new ReflectionClass($this->type);
            }
            $reflectionClass = self::$reflectionClasses[$this->type];

            $result = $reflectionClass->newInstanceWithoutConstructor();
            foreach ($reflectionClass->getProperties() as $property) {
                $propertyName = $property->getName();
                $resolution = $resolver->resolve($this->type, $propertyName, $value);
                $propertyValue = $resolution->value();
                $property->setAccessible(true);
                if (!isset(self::$propertyTypes[$this->type][$propertyName])) {
                    self::$propertyTypes[$this->type][$propertyName] = DocBlockParser::fromProperty($property);
                }
                $propertyType = self::$propertyTypes[$this->type][$propertyName];
                $property->setValue($result, $propertyType->resolveAndCast($propertyValue, $resolver));
            }
            return $result;
        }

        throw new CastException($value, $this);
    }

    public function __toString(): string
    {
        return $this->type;
    }
}
