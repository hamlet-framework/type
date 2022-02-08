<?php declare(strict_types=1);

namespace Hamlet\Cast\Resolvers;

use Hamlet\Cast\CastException;
use Hamlet\Cast\Type;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use stdClass;

class MappingUtils
{
    /**
     * @var array<string,array<string>>
     */
    private static array $properties = [];

    /**
     * @param array<string,int> $mappedProperties
     */
    public static function checkMapping(mixed $value, array $mappedProperties, Type $context): void
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
            if (is_a($value, stdClass::class)) {
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
