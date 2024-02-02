<?php

namespace Hamlet\Type\Types;

use DateTime;
use Error;
use Hamlet\Type\CastException;
use Hamlet\Type\Type;
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
    use CastCasesTrait;

    protected function type(): Type
    {
        return _array(_int());
    }

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

    #[DataProvider('castCases')] public function testCast(mixed $value): void
    {
        $expectedResult = [];
        $expectedExceptionThrown = false;
        try {
            foreach ((array) $value as $key => $property) {
                $expectedResult[$key] = (int) $property;
            }
        } catch (Error) {
            $expectedExceptionThrown = true;
        }

        try {
            $result = _array(_int())->cast($value);
            $this->assertSame($expectedResult, $result);
            $this->assertFalse($expectedExceptionThrown);
        } catch (TypeError) {
            $this->assertTrue($expectedExceptionThrown);
        }
    }
}
