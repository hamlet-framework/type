<?php

namespace Hamlet\Type\Types;

use DateTime;
use Error;
use Exception;
use Hamlet\Type\CastException;
use Hamlet\Type\Type;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use function Hamlet\Type\_bool;
use function Hamlet\Type\_int;
use function Hamlet\Type\_literal;
use function Hamlet\Type\_null;
use function Hamlet\Type\_resource;
use function Hamlet\Type\_string;
use function Hamlet\Type\_tuple;
use function Hamlet\Type\_union;

class TupleTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _tuple(_int(), _string());
    }

    #[DataProvider('castCases')] public function testCast(mixed $value): void
    {
        $expectedExceptionThrown = true;
        try {
            $arrayValue = is_array($value) ? $value : (array) $value;
            if (count($arrayValue) == 2) {
                $expectedResult = [(int) $arrayValue[0], (string) $arrayValue[1]];
                $expectedExceptionThrown = false;
            }
        } catch (Error) {
        }

        try {
            $result = _tuple(_int(), _string())->cast($value);
            $this->assertSame($expectedResult, $result);
            $this->assertFalse($expectedExceptionThrown, 'Expected exception not thrown');
        } catch (CastException) {
            $this->assertTrue($expectedExceptionThrown, "Thrown an excessive exception");
        }
    }
}
