<?php

namespace Hamlet\Type\Types;

use DateTime;
use Exception;
use Hamlet\Type\CastException;
use Hamlet\Type\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;

trait CastCasesTrait
{
    abstract protected function type(): Type;

    protected function strictCastResultComparison(): bool
    {
        return true;
    }

    abstract protected function baselineCast(mixed $value): mixed;

    #[DataProvider('castCases')] public function testCastMatchInvariant(mixed $value): void
    {
        $type = $this->type();
        if ($type->matches($value)) {
            $this->assertSame($value, $type->cast($value));
        } else {
            try {
                $this->assertNotSame($value, $type->cast($value));
            } catch (CastException) {
                $this->assertTrue(true);
            }
        }
    }

    #[DataProvider('castCases')] public function testMatchCastInvariant(mixed $value): void
    {
        $type = $this->type();
        try {
            $castValue = $type->cast($value);
            $this->assertTrue($type->matches($castValue));
        } catch (CastException) {
            $this->assertFalse($type->matches($value));
        }
    }

    #[DataProvider('castCases')] public function testCast(mixed $value): void
    {
        $expectedResult = null;
        try {
            $expectedResult = $this->baselineCast($value);
            $expectedExceptionThrown = false;
        } catch (Exception) {
            $expectedExceptionThrown = true;
        }

        try {
            $result = $this->type()->cast($value);
            if ($this->strictCastResultComparison()) {
                $this->assertSame($expectedResult, $result);
            } else {
                $this->assertEquals($expectedResult, $result);
            }
            $this->assertFalse($expectedExceptionThrown);
        } catch (CastException) {
            $this->assertTrue($expectedExceptionThrown);
        }
    }

    public static function castCases(): array
    {
        $resource = fopen(__FILE__, 'r');
        $stringableObject = new class ()
        {
            public function __toString()
            {
                return 'a';
            }
        };
        $object = new class()
        {
        };
        $callable = function () {
        };
        $intGenerator = function () {
            yield 1;
        };
        $stringGenerator = function() {
            yield "hello";
        };

        return [
            [true],
            [false],
            [0],
            [1],
            [-1],
            [''],
            ['0'],
            ['1'],
            ['1.22'],
            [7.5],
            ['1apple'],
            ['string'],
            [[]],
            [[1]],
            [[1, 3]],
            [[1, 'message']],
            [[1, '2']],
            [[2, 1, false]],
            [[0, null]],
            [[null, null]],
            [[false, 'sausage']],
            [['sausage', 3.1415, true]],
            [[2 => 1]],
            [['x' => 1, -1 => 3]],
            [[0 => 1, 2 => 'message']],
            [['a' => 1, 'b' => '2']],
            [[2, 1, 4 => false]],
            [[0, 0 => null]],
            [[null, 1 => null]],
            [[false, 'barbeque' => 'sausage']],
            [['sausage', 2 => 3.1415, true]],
            [new stdClass],
            [$object],
            [$stringableObject],
            [new DateTime],
            [$callable],
            [$resource],
            [null],
            [$intGenerator()],
            [$stringGenerator()],
        ];
    }
}
