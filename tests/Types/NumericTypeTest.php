<?php

namespace Hamlet\Type\Types;

use Hamlet\Type\CastException;
use Hamlet\Type\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function Hamlet\Type\_numeric;

class NumericTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _numeric();
    }

    #[DataProvider('castCases')] public function testCast(mixed $value): void
    {
        if (is_int($value) || is_float($value) || (is_string($value) && (is_numeric($value)))) {
            $expectedExceptionThrown = false;
        } else {
            $expectedExceptionThrown = true;
        }

        try {
            _numeric()->cast($value);
            $this->assertFalse($expectedExceptionThrown, 'Expected exception not thrown');
        } catch (CastException) {
            $this->assertTrue($expectedExceptionThrown, "Thrown an excessive exception");
        }
    }
}
