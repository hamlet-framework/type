<?php

namespace Hamlet\Cast\Types;

use DateTime;
use PHPUnit\Framework\TestCase;
use stdClass;
use function Hamlet\Cast\_mixed;

class MixedTypeTest extends TestCase
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
            [true,          true],
            [false,         true],
            [0,             true],
            [1,             true],
            [-1,            true],
            ['',            true],
            ['0',           true],
            ['abc',         true],
            ['strtoupper',  true],
            [[],            true],
            [[1],           true],
            [new stdClass,  true],
            [$object,       true],
            [new DateTime,  true],
            [$callable,     true],
            [$invokable,    true],
            [$resource,     true],
            [null,          true],
        ];
    }

    /**
     * @dataProvider matchCases()
     * @param mixed $value
     * @param bool $success
     */
    public function testMatch($value, bool $success)
    {
        $this->assertEquals($success, _mixed()->matches($value));
    }

    /**
     * @dataProvider matchCases()
     * @param mixed $value
     * @param bool $success
     */
    public function testAssert($value, bool $success)
    {
        _mixed()->assert($value);
        $this->assertTrue(true);
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
            [true,             true,      false],
            [false,           false,      false],
            [0,                   0,      false],
            [1,                   1,      false],
            [-1,                 -1,      false],
            ['',                 '',      false],
            ['0',               '0',      false],
            ['x1',             'x1',      false],
            [[],                 [],      false],
            [[false],       [false],      false],
            [[1],               [1],      false],
            [[1, 3],         [1, 3],      false],
            [$object,       $object,      false],
            [$callable,   $callable,      false],
            [$invokable, $invokable,      false],
            [$resource,   $resource,      false],
            [null,             null,      false],
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
        _mixed()->cast($value);
        $this->assertTrue(true);
    }
}
