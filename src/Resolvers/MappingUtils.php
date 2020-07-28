<?php

namespace Hamlet\Cast\Resolvers;

use Hamlet\Cast\CastException;
use Hamlet\Cast\Type;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

class MappingUtils
{
    /**
     * @var ReflectionClass[]
     * @psalm-var array<string,ReflectionClass>
     */
    private static $properties = [];

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
                    throw new CastException($value, $context, 'Property [' . $property . '] not mapped');
                }
            }
        } elseif (is_object($value)) {
            if (is_a($value, \stdClass::class)) {
                $properties = array_keys(get_object_vars($value));
            } else {
                $className = get_class($value);
                if (isset(self::$properties[$className])) {
                    $properties = self::$properties[$className];
                } else {
                    $properties = [];
                    try {
                        $reflectionClass = new ReflectionClass($className);
                    } catch (ReflectionException $e) {
                        throw new RuntimeException('Cannot read reflection information for ' . $className, 0, $e);
                    }

                    foreach ($reflectionClass->getProperties() as $property) {
                        $properties[] = $property->getName();
                    }
                    self::$properties[$className] = $properties;
                }
            }

            foreach ($properties as $property) {
                if (!isset($mappedProperties[$property])) {
                    throw new CastException($value, $context, 'Property [' . $property . '] not mapped');
                }
            }
        } else {
            throw new RuntimeException('Unexpected value type: ' . gettype($value));
        }
    }
}
