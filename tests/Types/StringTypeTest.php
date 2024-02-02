<?php

namespace Hamlet\Type\Types;

use Error;
use Hamlet\Type\CastException;
use Hamlet\Type\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function Hamlet\Type\_string;

class StringTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _string();
    }

    #[DataProvider('castCases')] public function testCast(mixed $value): void
    {
        $expectedResult = null;
        $expectedExceptionThrown = false;
        try {
            $expectedResult = (string) $value;
        } catch (Error) {
            $expectedExceptionThrown = true;
        }

        try {
            $result = _string()->cast($value);
            $this->assertSame($expectedResult, $result, 'Wrong cast result');
            $this->assertFalse($expectedExceptionThrown, 'Expected exception not thrown');
        } catch (CastException) {
            $this->assertTrue($expectedExceptionThrown, "Thrown an excessive exception");
        }
    }
}