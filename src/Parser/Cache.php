<?php

namespace Hamlet\Type\Parser;

use Hamlet\Type\Type;

class Cache
{
    /**
     * @param string $key
     * @param Type $type
     * @return void
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedOperand
     */
    public static function set(string $key, Type $type)
    {
        if (extension_loaded('apcu')) {
            apcu_store($key, [$type, time()]);
        } else {
            $fileName = sys_get_temp_dir() . '/' . md5($key);
            $payload = $type->serialize();
            $tempFileName = $fileName . '.tmp';
            file_put_contents($tempFileName, '<?php $value = ' . $payload . ';');
            rename($tempFileName, $fileName);
        }
    }

    /**
     * @param string $key
     * @param int $timeThreshold
     * @return Type|null
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public static function get(string $key, int $timeThreshold)
    {
        if (extension_loaded('apcu')) {
            $entry = apcu_fetch($key, $success);
            if ($success && $entry[1] > $timeThreshold) {
                return $entry[0];
            } else {
                return null;
            }
        } else {
            $fileName = sys_get_temp_dir() . '/' . md5($key);
            if (!file_exists($fileName) || filemtime($fileName) < $timeThreshold) {
                return null;
            }
            include($fileName);
            return $value ?? null;
        }
    }
}
