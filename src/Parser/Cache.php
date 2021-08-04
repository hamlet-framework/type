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
        $fileName = sys_get_temp_dir() . '/type-cache.' . md5($safeKey);
        $payload = $type->serialize();
        $tempFileName = $fileName . '.tmp';
        file_put_contents($tempFileName, '<?php $value = ' . $payload . ';');
        chmod($tempFileName, 0755);
        rename($tempFileName, $fileName);
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
        $fileName = sys_get_temp_dir() . '/type-cache.' . md5($safeKey);
        if (!file_exists($fileName) || filemtime($fileName) < $timeThreshold) {
            return null;
        }
        /**
         * @psalm-suppress UnresolvableInclude
         */
        try {
            include($fileName);
        } catch (\Throwable $e) {
            unlink($fileName);
            return null;
        }

        /**
         * @noinspection PhpExpressionAlwaysNullInspection
         * @psalm-suppress UndefinedVariable
         */
        return $value ?? null;
    }

    /**
     * @param string $key
     * @return void
     */
    public static function remove(string $key)
    {
        $safeKey = __CLASS__ . '::' . $key;
        $fileName = sys_get_temp_dir() . '/type-cache.' . md5($safeKey);
        unlink($fileName);
    }

    /**
     * @return void
     */
    public static function purge()
    {
        foreach (glob(sys_get_temp_dir() . '/type-cache.*') as $fileName) {
            unlink($fileName);
        }
    }
}
