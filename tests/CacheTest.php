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
}
