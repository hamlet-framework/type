<?php

namespace Hamlet\Type\Types;

use Hamlet\Type\CastException;
use Hamlet\Type\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function Hamlet\Type\_scalar;

class ScalarTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _scalar();
    }

    #[DataProvider('castCases')] public function testCast(mixed $value): void
    {
        if (is_scalar($value)) {
            $expectedExceptionThrown = false;
        } else {
            $expectedExceptionThrown = true;
        }

        try {
            _scalar()->cast($value);
            $this->assertFalse($expectedExceptionThrown, 'Expected exception not thrown');
        } catch (CastException) {
            $this->assertTrue($expectedExceptionThrown, "Thrown an excessive exception");
        }
    }
}
