<?php

namespace Hamlet\Type;

use Hamlet\Type\Parser\Cache;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

class CacheTest extends TestCase
{
    /**
     * @throws RandomException
     */
    public function testSetThenGet()
    {
        $type = type_of('array<int,resource|array<resource>>');
        $key = md5(random_bytes(15));

        Cache::set($key, $type);
        $copy = Cache::get($key, time() - 1);

        $this->assertEquals((string) $copy, (string) $type);
    }

    /**
     * @throws RandomException
     */
    public function testSetThenRemoveAndThenGet()
    {
        $type = type_of('array<int,resource|array<resource>>');
        $key = md5(random_bytes(15));

        Cache::set($key, $type);
        Cache::remove($key);
        $copy = Cache::get($key, time() - 1);

        $this->assertNull($copy);
    }

    /**
     * @throws RandomException
     */
    public function testSetThenGetWithFutureTimestamp()
    {
        $type = type_of('array<int,resource|array<resource>>');
        $key = md5(random_bytes(15));

        Cache::set($key, $type);
        $copy = Cache::get($key, time() + 5);

        $this->assertNull($copy);
    }

    /**
     * @throws RandomException
     */
    public function testCacheReturnsNullOnError()
    {
        $type = type_of('array<int,resource|array<resource>>');
        $key = md5(random_bytes(15));
        $safeKey = Cache::class . '::' . $key;
        $fileName = sys_get_temp_dir() . '/type-cache.' . md5($safeKey);

        $this->assertFileDoesNotExist($fileName);
        Cache::set($key, $type);
        $copy1 = Cache::get($key, time() - 1);
        $this->assertNotNull($copy1);

        $this->assertFileExists($fileName);
        file_put_contents($fileName, '<?php 1/0;');
        $copy2 = Cache::get($key, time() - 1);
        $this->assertNull($copy2);
    }
}
