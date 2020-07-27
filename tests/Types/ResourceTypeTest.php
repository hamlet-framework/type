<?php

namespace Hamlet\Cast\Types;

use DateTime;
use Exception;
use Hamlet\Cast\CastException;
use PHPUnit\Framework\TestCase;
use stdClass;
use function Hamlet\Cast\_resource;

class ResourceTypeTest extends TestCase
{
    public function matchCases()
    {
        $resource = fopen(__FILE__, 'r');
        $object = new class ()
        {
            public function __toString()
            {
                return 'a';
            }
        };
        $callable = function () {
        };
        $invokable = new class()
        {
            public function __invoke()
            {
            }
        };

        return [
            [true,          false],
            [false,         false],
            [0,             false],
            [1,             false],
            [-1,            false],
            ['',            false],
            ['0',           false],
            ['abc',         false],
            ['strtoupper',  false],
            [[],            false],
            [[1],           false],
            [new stdClass,  false],
            [$object,       false],
            [new DateTime,  false],
            [$callable,     false],
            [$invokable,    false],
            [$resource,     true],
            [null,          false],
        ];
    }

    /**
     * @dataProvider matchCases()
     * @param mixed $value
     * @param bool $success
     */
    public function testMatch($value, bool $success)
    {
        $this->assertEquals($success, _resource()->matches($value));
    }

    /**
     * @dataProvider matchCases()
     * @param mixed $value
     * @param bool $success
     */
    public function testAssert($value, bool $success)
    {
        $exceptionThrown = false;
        try {
            _resource()->assert($value);
        } catch (Exception $error) {
            $exceptionThrown = true;
        }
        $this->assertEquals(!$success, $exceptionThrown);
    }

    public function castCases()
    {
        $resource = fopen(__FILE__, 'r');
        $object = new class ()
        {
            public function __toString()
            {
                return 'a';
            }
        };
        $callable = function () {
        };
        $invokable = new class()
        {
            public function __invoke()
            {
            }
        };

        return [
            [true,          null,       true],
            [false,         null,       true],
            [0,             null,       true],
            [1,             null,       true],
            [-1,            null,       true],
            ['',            null,       true],
            ['0',           null,       true],
            ['x1',          null,       true],
            [[],            null,       true],
            [[false],       null,       true],
            [[1],           null,       true],
            [[1, 3],        null,       true],
            [new stdClass,  null,       true],
            [$object,       null,       true],
            [new DateTime,  null,       true],
            ['abs',         null,       true],
            [$callable,     null,       true],
            [$invokable,    null,       true],
            [$resource,     $resource, false],
            [null,          null,       true],
        ];
    }

    /**
     * @dataProvider castCases()
     * @param mixed $value
     * @param mixed $result
     * @param bool $exceptionThrown
     */
    public function testCast($value, $result, bool $exceptionThrown)
    {
        if ($exceptionThrown) {
            $this->expectException(CastException::class);
            _resource()->cast($value);
        } else {
            $this->assertSame($result, _resource()->cast($value));
        }
    }
}
