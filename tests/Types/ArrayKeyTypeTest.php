<?php

namespace Hamlet\Type\Types;

use DateTime;
use Exception;
use Hamlet\Type\CastException;
use Hamlet\Type\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;
use function Hamlet\Type\_array_key;

class ArrayKeyTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _array_key();
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

        return [
            [true,          false],
            [false,         false],
            [0,             true],
            [1,             true],
            [-1,            true],
            ['',            true],
            ['0',           true],
            ['string',      true],
            [[],            false],
            [[1],           false],
            [new stdClass,  false],
            [$object,       false],
            [new DateTime,  false],
            [$callable,     false],
            [$resource,     false],
            [null,          false],
        ];
    }

    #[DataProvider('matchCases')] public function testMatch(mixed $value, bool $success): void
    {
        $this->assertEquals($success, _array_key()->matches($value));
    }

    #[DataProvider('matchCases')] public function testAssert(mixed $value, bool $success): void
    {
        $exceptionThrown = false;
        try {
            _array_key()->assert($value);
        } catch (Exception) {
            $exceptionThrown = true;
        }
        $this->assertEquals(!$success, $exceptionThrown);
    }

    #[DataProvider('castCases')] public function testCast(mixed $value): void
    {
        $expectedResult = null;
        $expectedExceptionThrown = false;
        try {
            $a = [
                $value => 1
            ];
            $expectedResult = array_key_first($a);
        } catch (TypeError) {
            $expectedExceptionThrown = true;
        }

        try {
            $result = _array_key()->cast($value);
            $this->assertEquals($expectedResult, $result, 'Wrong cast result');
            $this->assertFalse($expectedExceptionThrown, 'Expected exception not thrown');
        } catch (CastException) {
            $this->assertTrue($expectedExceptionThrown, "Thrown an excessive exception");
        }
    }
}
