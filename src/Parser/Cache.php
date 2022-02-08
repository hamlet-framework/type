<?php declare(strict_types=1);

namespace Hamlet\Cast\Parser;

use Hamlet\Cast\Type;
use Throwable;

class Cache
{
    public static function set(string $key, Type $type): void
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
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public static function get(string $key, int $timeThreshold): ?Type
    {
        $safeKey = __CLASS__ . '::' . $key;
        $fileName = sys_get_temp_dir() . '/type-cache.' . md5($safeKey);
        if (!file_exists($fileName) || filemtime($fileName) < $timeThreshold) {
            return null;
        }
        try {
            include($fileName);
        } catch (Throwable $exception) {
            unlink($fileName);
            return null;
        }

        /**
         * @noinspection PhpExpressionAlwaysNullInspection
         * @psalm-suppress UndefinedVariable
         */
        return $value ?? null;
    }

    public static function remove(string $key): void
    {
        $safeKey = __CLASS__ . '::' . $key;
        $fileName = sys_get_temp_dir() . '/type-cache.' . md5($safeKey);
        unlink($fileName);
    }

    public static function purge(): void
    {
        foreach (glob(sys_get_temp_dir() . '/type-cache.*') as $fileName) {
            unlink($fileName);
        }
    }
}
