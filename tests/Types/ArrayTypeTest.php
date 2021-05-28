<?php

namespace Hamlet\Cast\Types;

use DateTime;
use Hamlet\Cast\CastException;
use Hamlet\Cast\Type;
use PHPUnit\Framework\TestCase;
use stdClass;
use function Hamlet\Cast\_array;
use function Hamlet\Cast\_int;
use function Hamlet\Cast\_mixed;
use function Hamlet\Cast\_string;

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
        if (!$success) {
            $this->expectException(CastException::class);
        }
        _array(_mixed())->assert($value);
        $this->assertTrue(true);
    }

    public function testListOfStrings()
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
}
