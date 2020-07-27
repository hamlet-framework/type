<?php

namespace Hamlet\Cast\Resolvers;

use Hamlet\Cast\CastException;
use Hamlet\Cast\Type;

class MappingUtils
{
    /**
     * @param mixed $value
     * @param array $mappedProperties
     * @psalm-param array<string,int> $mappedFields
     * @param Type $context
     * @return void
     */
    public static function checkMapping($value, array $mappedProperties, Type $context)
    {
        if (is_array($value)) {
            foreach ($value as $property => $_) {
                /**
                 * @psalm-suppress MixedArrayTypeCoercion
                 */
                if (!isset($mappedProperties[$property])) {
                    throw new CastException($value, $context, 'Property ' . $property . ' not mapped');
                }
            }
        } elseif (is_object($value)) {
            foreach (get_object_vars($value) as $property => $_) {
                /**
                 * @psalm-suppress MixedArrayTypeCoercion
                 */
                if (!isset($mappedProperties[$property])) {
                    throw new CastException($value, $context, 'Property ' . $property . ' not mapped');
                }
            }
        } else {
            throw new CastException($value, $context, 'Unexpected value type');
        }
    }
}
