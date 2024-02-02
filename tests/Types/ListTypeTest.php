<?php

namespace Hamlet\Type\Types;

use DateTime;
use Hamlet\Type\CastException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use function Hamlet\Type\_int;
use function Hamlet\Type\_list;
use function Hamlet\Type\_mixed;
use function Hamlet\Type\_string;
use function Hamlet\Type\type_of;

class ListTypeTest extends TestCase
{
    public static function matchCases(): array
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
            [[1 => 2],      false],
            [['a' => 0],    false],
        ];
    }

    #[DataProvider('matchCases')] public function testMatch(mixed $value, bool $success): void
    {
        $this->assertEquals($success, _list(_mixed())->matches($value), 'Failed on ' . print_r($value, true));
    }

    #[DataProvider('matchCases')] public function testAssert(mixed $value, bool $success): void
    {
        if (!$success) {
            $this->expectException(CastException::class);
        }
        _list(_mixed())->assert($value);
        $this->assertTrue(true);
    }

    public function testListOfStrings()
    {
        $a = [0 => 'a', 1 => 'b'];
        $this->assertTrue(_list(_string())->matches($a));
    }

    public function testWrongOrder()
    {
        $a = [1 => 'a', 0 => 'b'];
        $this->assertFalse(_list(_string())->matches($a));
    }

    public function testSkippedIndex()
    {
        $a = [0 => 'a', 2 => 'b'];
        $this->assertFalse(_list(_string())->matches($a));
    }

    public function testInvalidType()
    {
        $a = [1, 2, 'a'];
        $this->assertFalse(_list(_int())->matches($a));
    }

    public function testParsing()
    {
        $type = type_of('list<int>');
        $this->assertTrue($type->matches([1, 2, 3]));
        $this->assertFalse($type->matches([1 => 2]));
    }
}
