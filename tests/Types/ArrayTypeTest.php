<?php

namespace Hamlet\Type\Types;

use DateTime;
use Hamlet\Type\CastException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;
use function Hamlet\Type\_array;
use function Hamlet\Type\_int;
use function Hamlet\Type\_mixed;
use function Hamlet\Type\_string;
use function Hamlet\Type\type_of;

class ArrayTypeTest extends TestCase
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
            [[1 => 2],      true],
            [['a' => 0],    true],
        ];
    }

    #[DataProvider('matchCases')] public function testMatch(mixed $value, bool $success): void
    {
        $this->assertEquals($success, _array(_mixed())->matches($value), 'Failed on ' . print_r($value, true));
    }

    #[DataProvider('matchCases')] public function testAssert(mixed $value, bool $success): void
    {
        if ($success) {
            _array(_mixed())->assert($value);
            $this->assertTrue(true, 'Failed to assert that ' . print_r($value, true) . ' is convertible to an array');
        } else {
            $this->expectException(CastException::class);
            _array(_mixed())->assert($value);
        }
    }

    public function testArrayOfStrings(): void
    {
        $a = [0 => 'a', 1 => 'b'];
        $this->assertTrue(_array(_string())->matches($a));
    }

    public function testWrongOrder(): void
    {
        $a = [1 => 'a', 0 => 'b'];
        $this->assertTrue(_array(_string())->matches($a));
    }

    public function testSkippedIndex(): void
    {
        $a = [0 => 'a', 2 => 'b'];
        $this->assertTrue(_array(_string())->matches($a));
    }

    public function testInvalidType(): void
    {
        $a = [1, 2, 'a'];
        $this->assertFalse(_array(_int())->matches($a));
    }

    public function testParsing(): void
    {
        $type = type_of('array<int>');
        $this->assertTrue($type->matches([1, 2, 3]));
        $this->assertTrue($type->matches([1 => 2]));
    }

    public static function castCases(): array
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
            [true,       [true],      false],
            [false,      [false],     false],
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

    #[DataProvider('castCases')] public function testCast(mixed $value, mixed $expectedResult, bool $expectedExceptionThrown): void
    {
        $result = null;
        $exceptionThrown = false;
        try {
            $result = (array) $value;
        } catch (TypeError) {
            $exceptionThrown = true;
        }

        $this->assertSame($expectedResult, $result);
        $this->assertEquals($expectedExceptionThrown, $exceptionThrown);
    }
}
