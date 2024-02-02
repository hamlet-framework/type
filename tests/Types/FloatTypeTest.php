<?php

namespace Hamlet\Type\Types;

use Hamlet\Type\CastException;
use Hamlet\Type\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TypeError;
use function Hamlet\Type\_float;

class FloatTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _float();
    }

    #[DataProvider('castCases')] public function testCast(mixed $value): void
    {
        $expectedResult = null;
        $expectedExceptionThrown = false;
        try {
            $expectedResult = (float) $value;
        } catch (TypeError) {
            $expectedExceptionThrown = true;
        }

        try {
            $result = _float()->cast($value);
            $this->assertSame($expectedResult, $result, 'Wrong cast result');
            $this->assertFalse($expectedExceptionThrown, 'Expected exception not thrown');
        } catch (CastException) {
            $this->assertTrue($expectedExceptionThrown, "Thrown an excessive exception");
        }
    }
}