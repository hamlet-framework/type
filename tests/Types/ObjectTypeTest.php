<?php

namespace Hamlet\Type\Types;

use Error;
use Hamlet\Type\CastException;
use Hamlet\Type\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function Hamlet\Type\_object;

class ObjectTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _object();
    }

    #[DataProvider('castCases')] public function testCast(mixed $value): void
    {
        $expectedExceptionThrown = false;
        try {
            $expectedResult = (object) $value;
        } catch (Error) {
            $expectedExceptionThrown = true;
        }

        try {
            $result = _object()->cast($value);
            $this->assertEquals($expectedResult, $result, 'Wrong cast result');
            $this->assertFalse($expectedExceptionThrown, 'Expected exception not thrown');
        } catch (CastException) {
            $this->assertTrue($expectedExceptionThrown, "Thrown an excessive exception");
        }
    }
}