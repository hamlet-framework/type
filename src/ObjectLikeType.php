<?php declare(strict_types=1);

namespace Hamlet\Cast;

use Hamlet\Cast\Resolvers\DefaultResolver;
use Hamlet\Cast\Resolvers\MappingUtils;
use Hamlet\Cast\Resolvers\Resolver;
use stdClass;

/**
 * @template T
 * @extends Type<array<T>>
 */
class ObjectLikeType extends Type
{
    /**
     * @var Type[]
     * @psalm-var array<string,Type<T>>
     */
    private $fields;

    /**
     * @param Type[] $fields
     * @psalm-param array<string,Type<T>> $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true array $value
     */
    public function matches($value): bool
    {
        if (!is_array($value)) {
            return false;
        }
        foreach ($this->fields as $field => $type) {
            $tokens = explode('?', $field, 2);
            if (!array_key_exists($tokens[0], $value)) {
                if (count($tokens) == 1) {
                    return false;
                } else {
                    continue;
                }
            }
            if (!$type->matches($value[$tokens[0]])) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param mixed $value
     * @param Resolver $resolver
     * @return array
     * @psalm-return array<T>
     */
    public function resolveAndCast($value, Resolver $resolver): array
    {
        if (!(is_array($value) || is_object($value) && is_a($value, stdClass::class))) {
            throw new CastException($value, $this);
        }
        $validateUnmappedProperties = !$resolver->ignoreUnmappedProperties();
        $mappedProperties = [];

        foreach ($this->fields as $field => $fieldType) {
            $tokens = explode('?', $field, 2);
            $fieldName = $tokens[0];
            $required = count($tokens) == 1;

            /**
             * @psalm-suppress ArgumentTypeCoercion
             */

            $resolution = $resolver->getValue(null, $fieldName, $value);
            if ($resolution->successful()) {
                if ($validateUnmappedProperties) {
                    $sourceFieldName = $resolution->sourceFieldName();
                    if ($sourceFieldName) {
                        $mappedProperties[$sourceFieldName] = 1;
                    }
                }
            } else {
                if (!$required) {
                    $mappedProperties[$fieldName] = 1;
                    continue;
                } else {
                    throw new CastException($value, $this);
                }
            }

            $fieldValue = $fieldType->resolveAndCast($resolution->value(), $resolver);
            $value = $resolver->setValue($value, $fieldName, $fieldValue);
        }
        if ($validateUnmappedProperties) {
            MappingUtils::checkMapping($value, $mappedProperties, $this);
        }
        return (array) $value;
    }

    public function __toString(): string
    {
        $keys = [];
        foreach ($this->fields as $name => $type) {
            $keys[] = $name . ':' . $type;
        }
        return 'array{' . join(',', $keys) . '}';
    }

    public function serialize(): string
    {
        $arguments = [];
        foreach ($this->fields as $name => $type) {
            $arguments[] = var_export($name, true) . ' => ' . $type->serialize();
        }
        return 'new ' . static::class . '([' . join(', ', $arguments) . '])';
    }
}
