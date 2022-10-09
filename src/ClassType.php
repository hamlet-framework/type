<?php declare(strict_types=1);

namespace Hamlet\Type;

use Hamlet\Type\Resolvers\MappingUtils;
use Hamlet\Type\Resolvers\Resolver;
use ReflectionException;
use stdClass;

/**
 * @template T as object
 * @extends Type<T>
 */
class ClassType extends Type
{
    /**
     * @var class-string<T>
     */
    private string $type;

    /**
     * @param class-string<T> $type
     */
    public function __construct(string $type)
    {
        if ($type[0] == '\\') {
            /** @psalm-suppress PropertyTypeCoercion */
            $this->type = substr($type, 1);
        } else {
            $this->type = $type;
        }
    }

    /**
     * @psalm-assert-if-true T $value
     */
    public function matches(mixed $value): bool
    {
        return is_object($value) && is_a($value, $this->type);
    }

    /**
     * @return T
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     * @throws ReflectionException
     */
    public function resolveAndCast(mixed $value, Resolver $resolver): object
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

        $validateUnmappedProperties = !$resolver->ignoreUnmappedProperties();
        $mappedProperties = [];

        $result = $reflectionClass->newInstanceWithoutConstructor();
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $propertyName    = $reflectionProperty->getName();
            $valueResolution = $subTreeResolver->getValue($this->type, $propertyName, $value);
            $propertyType    = $subTreeResolver->getPropertyType($reflectionClass, $reflectionProperty);

            if ($valueResolution->successful()) {
                if ($validateUnmappedProperties) {
                    $sourceFieldName = $valueResolution->sourceFieldName();
                    if ($sourceFieldName) {
                        $mappedProperties[$sourceFieldName] = 1;
                    }
                }
            } else {
                if ($propertyType->matches(null)) {
                    $mappedProperties[$propertyName] = 1;
                } else {
                    throw new CastException($value, $this);
                }
            }

            $result = $resolver->setValue(
                $result,
                $propertyName,
                $propertyType->resolveAndCast($valueResolution->value(), $subTreeResolver)
            );
        }
        if ($validateUnmappedProperties) {
            MappingUtils::checkMapping($value, $mappedProperties, $this);
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
