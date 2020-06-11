<?php

namespace Hamlet\Cast;

use Hamlet\Cast\Resolvers\PropertyResolver;
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
     * @param PropertyResolver $resolver
     * @return array
     * @psalm-return array<T>
     * @psalm-suppress MixedArgument
     * @psalm-suppress RedundantCondition
     */
    public function resolveAndCast($value, PropertyResolver $resolver): array
    {
        if (!is_array($value) && !is_a($value, stdClass::class)) {
            throw new CastException($value, $this);
        }
        foreach ($this->fields as $field => $fieldType) {
            $tokens = explode('?', $field, 2);
            $fieldName = $tokens[0];
            $required = count($tokens) == 1;

            $resolution = $resolver->resolve(null, $fieldName, $value);
            if ($resolution->success()) {
                if (is_array($value)) {
                    $value[$fieldName] = $fieldType->resolveAndCast($resolution->value(), $resolver);
                } elseif (is_object($value)) {
                    $value->{$fieldName} = $fieldType->resolveAndCast($resolution->value(), $resolver);
                }
            } elseif ($required) {
                throw new CastException($value, $this);
            }
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
}
