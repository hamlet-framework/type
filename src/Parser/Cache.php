<?php

namespace Hamlet\Type\Parser;

class Cache
{
    /**
     * @var array
     * @psalm-var array<string,array{0:mixed,1:int}>
     */
    private static $internal = [];

    /**
     * @template T
     * @param string $key
     * @param mixed $value
     * @psalm-param T $value
     * @return void
     */
    public static function set(string $key, $value)
    {
        if (extension_loaded('apcu')) {
            apcu_store($key, [$value, time()]);
        } else {
            self::$internal[$key] = [$value, time()];
        }
    }

    /**
     * @param string $key
     * @param int $timeThreshold
     * @return mixed
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedInferredReturnType
     */
    public static function get(string $key, int $timeThreshold)
    {
        if (extension_loaded('apcu')) {
            $entry = apcu_fetch($key, $success);
            if ($success && $entry[1] > $timeThreshold) {
                return $entry[0];
            }
        } else {
            $entry = self::$internal[$key] ?? null;
            if ($entry !== null && $entry[1] > $timeThreshold) {
                return $entry[0];
            }
        }
        return null;
    }
}
