<?php declare(strict_types=1);

namespace Hamlet\Cast;

use Hamlet\Cast\Parser\DocBlockParser;
use Hamlet\Cast\Resolvers\Resolver;
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
     * @param Resolver $resolver
     * @return object
     * @throws ReflectionException
     * @psalm-return T
     * @psalm-suppress MixedAssignment
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    public function resolveAndCast($value, Resolver $resolver)
    {
        if ($this->matches($value)) {
            return $value;
        }

        if (is_object($value) && is_a($value, stdClass::class) || is_array($value)) {
            $reflectionClass = $resolver->getReflectionClass($this->type, $value);
            $result = $reflectionClass->newInstanceWithoutConstructor();
            foreach ($reflectionClass->getProperties() as $property) {
                $propertyName = $property->getName();
                $resolution = $resolver->getValue($this->type, $propertyName, $value);
                $propertyType = $resolver->getPropertyType($property);
                if (!$resolution->successful() && !$propertyType->matches(null)) {
                    throw new CastException($value, $this);
                }
                $propertyValue = $resolution->value();
                $result = $resolver->setValue($result, $propertyName, $propertyType->resolveAndCast($propertyValue, $resolver));
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
