<?php

namespace Hamlet\Type\Types;

use DateTime;
use Exception;
use Hamlet\Type\CastException;
use Hamlet\Type\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use function Hamlet\Type\_resource;

class ResourceTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _resource();
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
            [[],            false],
            [[1],           false],
            [new stdClass,  false],
            [$object,       false],
            [new DateTime,  false],
            [$callable,     false],
            [$invokable,    false],
            [$resource,     true],
            [null,          false],
        ];
    }

    #[DataProvider('matchCases')] public function testMatch(mixed $value, bool $success): void
    {
        $this->assertEquals($success, _resource()->matches($value));
    }

    #[DataProvider('matchCases')] public function testAssert(mixed $value, bool $success): void
    {
        $exceptionThrown = false;
        try {
            _resource()->assert($value);
        } catch (Exception) {
            $exceptionThrown = true;
        }
        $this->assertEquals(!$success, $exceptionThrown);
    }

    #[DataProvider('castCases')] public function testCast(mixed $value)
    {
        $expectedExceptionThrown = !is_resource($value);

        try {
            _resource()->cast($value);
            $this->assertFalse($expectedExceptionThrown, 'Expected exception not thrown');
        } catch (CastException) {
            $this->assertTrue($expectedExceptionThrown, "Thrown an excessive exception");
        }
    }
}
