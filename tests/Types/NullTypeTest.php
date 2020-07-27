<?php

namespace Hamlet\Cast\Types;

use DateTime;
use Exception;
use Hamlet\Cast\CastException;
use PHPUnit\Framework\TestCase;
use stdClass;
use function Hamlet\Cast\_null;

class NullTypeTest extends TestCase
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
            [$resource,     false],
            [null,           true],
        ];
    }

    /**
     * @dataProvider matchCases()
     * @param mixed $value
     * @param bool $success
     */
    public function testMatch($value, bool $success)
    {
        $this->assertEquals($success, _null()->matches($value));
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
            _null()->assert($value);
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
            [false,         null,      false],
            [0,             null,      false],
            [1,             null,       true],
            [-1,            null,       true],
            ['',            null,      false],
            ['0',           null,       true],
            ['x1',          null,       true],
            [[],            null,      false],
            [[false],       null,       true],
            [[1],           null,       true],
            [[1, 3],        null,       true],
            [new stdClass,  null,       true],
            [$object,       null,       true],
            [new DateTime,  null,       true],
            ['abs',         null,       true],
            [$callable,     null,       true],
            [$invokable,    null,       true],
            [$resource,     null,       true],
            [null,          null,      false],
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
            _null()->cast($value);
        } else {
            $this->assertSame($result, _null()->cast($value));
        }
    }
}
