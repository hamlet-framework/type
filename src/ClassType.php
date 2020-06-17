<?php declare(strict_types=1);

namespace Hamlet\Type;

use Hamlet\Type\Resolvers\Resolver;
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
     * @psalm-return T
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    public function resolveAndCast($value, Resolver $resolver)
    {
        if ($this->matches($value)) {
            return $value;
        }

        if (!(is_object($value) && is_a($value, stdClass::class) || is_array($value))) {
            throw new CastException($value, $this);
        }

        $subTypeResolution = $resolver->resolveSubType($this->type, $value);
        $reflectionClass   = $subTypeResolution->reflectionClass();
        $subTreeResolver   = $subTypeResolution->subTreeResolver();

        $result = $reflectionClass->newInstanceWithoutConstructor();
        foreach ($reflectionClass->getProperties() as $property) {
            $propertyName    = $property->getName();
            $valueResolution = $subTreeResolver->getValue($this->type, $propertyName, $value);
            $propertyType    = $subTreeResolver->getPropertyType($property);

            if (!$valueResolution->successful() && !$propertyType->matches(null)) {
                throw new CastException($value, $this);
            }

            $result = $resolver->setValue(
                $result,
                $propertyName,
                $propertyType->resolveAndCast($valueResolution->value(), $subTreeResolver)
            );
        }
        return $result;
    }

    public function __toString(): string
    {
        return $this->type;
    }

    public function serialize(): string
    {
        return 'new ' . static::class . '(' . $this->type . '::class)';
    }
}
