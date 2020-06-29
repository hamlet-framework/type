<?php

namespace Hamlet\Cast\Parser;

use Hamlet\Cast\Type;

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
        $safeKey = __CLASS__ . '::' . $key;
        if (extension_loaded('apcu')) {
            apcu_store($safeKey, [$type, time()]);
        } else {
            $fileName = sys_get_temp_dir() . '/' . md5($safeKey);
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
        $safeKey = __CLASS__ . '::' . $key;
        if (extension_loaded('apcu')) {
            $entry = apcu_fetch($safeKey, $success);
            if ($success && $entry[1] > $timeThreshold) {
                return $entry[0];
            } else {
                return null;
            }
        } else {
            // return null;
            $fileName = sys_get_temp_dir() . '/' . md5($safeKey);
            if (!file_exists($fileName) || filemtime($fileName) < $timeThreshold) {
                return null;
            }
            include($fileName);
            return $value ?? null;
        }
    }

    /**
     * @param string $key
     * @return void
     */
    public static function remove(string $key)
    {
        $safeKey = __CLASS__ . '::' . $key;
        if (extension_loaded('apcu')) {
            apcu_delete($safeKey);
        } else {
            $fileName = sys_get_temp_dir() . '/' . md5($safeKey);
            unlink($fileName);
        }
    }
}
