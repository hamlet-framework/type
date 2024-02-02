<?php

namespace Hamlet\Type\Types;

use Hamlet\Type\CastException;
use Hamlet\Type\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TypeError;
use function Hamlet\Type\_int;

class IntTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _int();
    }

    #[DataProvider('castCases')] public function testCast(mixed $value): void
    {
        $expectedResult = null;
        $expectedExceptionThrown = false;
        try {
            $expectedResult = (int) $value;
        } catch (TypeError) {
            $expectedExceptionThrown = true;
        }

        try {
            $result = _int()->cast($value);
            $this->assertSame($expectedResult, $result, 'Wrong cast result');
            $this->assertFalse($expectedExceptionThrown, 'Expected exception not thrown');
        } catch (CastException) {
            $this->assertTrue($expectedExceptionThrown, "Thrown an excessive exception");
        }
    }
}