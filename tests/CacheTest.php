<?php

namespace Hamlet\Cast;

use Hamlet\Cast\Parser\Cache;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    public function testSetThenGet()
    {
        $type = Type::of('array<int,resource|array<resource>>');
        $key = md5(random_bytes(15));

        Cache::set($key, $type);
        $copy = Cache::get($key, time() - 1);

        $this->assertEquals((string) $copy, (string) $type);
    }

    public function testSetThenRemoveAndThenGet()
    {
        $type = Type::of('array<int,resource|array<resource>>');
        $key = md5(random_bytes(15));

        Cache::set($key, $type);
        Cache::remove($key);
        $copy = Cache::get($key, time() - 1);

        $this->assertNull($copy);
    }

    public function testSetThenGetWithFutureTimestamp()
    {
        $type = Type::of('array<int,resource|array<resource>>');
        $key = md5(random_bytes(15));

        Cache::set($key, $type);
        $copy = Cache::get($key, time() + 5);

        $this->assertNull($copy);
    }

    public function testCacheReturnsNullOnError()
    {
        $type = Type::of('array<int,resource|array<resource>>');
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
