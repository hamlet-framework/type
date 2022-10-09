<?php

namespace Hamlet\Type\Types;

use DateTime;
use Hamlet\Type\CastException;
use Hamlet\Type\Type;
use PHPUnit\Framework\TestCase;
use stdClass;
use function Hamlet\Type\_array;
use function Hamlet\Type\_int;
use function Hamlet\Type\_mixed;
use function Hamlet\Type\_string;

class ArrayTypeTest extends TestCase
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
            [[],            true],
            [[1],           true],
            [new stdClass,  false],
            [$object,       false],
            [new DateTime,  false],
            [$callable,     false],
            [$invokable,    false],
            [$resource,     false],
            [null,          false],
            [[1 => 2],      true],
            [['a' => 0],    true],
        ];
    }

    /**
     * @dataProvider matchCases()
     * @param mixed $value
     * @param bool $success
     */
    public function testMatch($value, bool $success)
    {
        $this->assertEquals($success, _array(_mixed())->matches($value), 'Failed on ' . print_r($value, true));
    }

    /**
     * @dataProvider matchCases()
     * @param mixed $value
     * @param bool $success
     */
    public function testAssert($value, bool $success)
    {
        if ($success) {
            _array(_mixed())->assert($value);
            $this->assertTrue(true, 'Failed to assert that ' . print_r($value, true) . ' is convertible to an array');
        } else {
            $this->expectException(CastException::class);
            _array(_mixed())->assert($value);
        }
    }

    public function testArrayOfStrings()
    {
        $a = [0 => 'a', 1 => 'b'];
        $this->assertTrue(_array(_string())->matches($a));
    }

    public function testWrongOrder()
    {
        $a = [1 => 'a', 0 => 'b'];
        $this->assertTrue(_array(_string())->matches($a));
    }

    public function testSkippedIndex()
    {
        $a = [0 => 'a', 2 => 'b'];
        $this->assertTrue(_array(_string())->matches($a));
    }

    public function testInvalidType()
    {
        $a = [1, 2, 'a'];
        $this->assertFalse(_array(_int())->matches($a));
    }

    public function testParsing()
    {
        $type = Type::of('array<int>');
        $this->assertTrue($type->matches([1, 2, 3]));
        $this->assertTrue($type->matches([1 => 2]));
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
            [true,       null,      true],
            [false,      null,      true],
            [0,          null,      true],
            [1,          null,      true],
            [-1,         null,      true],
            ['',         null,      true],
            ['0',        null,      true],
            ['x1',       null,      true],
            [[],         [],        false],
            [[false],    [false],   false],
            [[1],        [1],       false],
            [[1, 3],     [1, 3],    false],
            [$object,    null,      true],
            [$callable,  null,      true],
            [$invokable, null,      true],
            [$resource,  null,      true],
            [null,       null,      true],
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
        }
        $this->assertSame($result, _array(_mixed())->cast($value), 'Failed on ' . print_r($value, true));
    }
}
