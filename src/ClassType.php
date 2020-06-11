<?php declare(strict_types=1);

namespace Hamlet\Cast;

use Hamlet\Cast\Parser\DocBlockParser;
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
     * @return object
     * @psalm-return T
     * @psalm-suppress MixedAssignment
     * @psalm-suppress InvalidReturnStatement
     * @throws ReflectionException
     */
    public function cast($value)
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
                if (is_array($value) && array_key_exists($propertyName, $value)) {
                    $propertyValue = $value[$propertyName];
                } elseif (is_object($value) && property_exists($value, $propertyName)) {
                    $propertyValue = $value->{$propertyName};
                } else {
                    $propertyValue = null;
                }
                $property->setAccessible(true);
                if (!isset(self::$propertyTypes[$this->type][$propertyName])) {
                    self::$propertyTypes[$this->type][$propertyName] = DocBlockParser::fromProperty($property);
                }
                $propertyType = self::$propertyTypes[$this->type][$propertyName];
                $property->setValue($result, $propertyType->cast($propertyValue));
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
