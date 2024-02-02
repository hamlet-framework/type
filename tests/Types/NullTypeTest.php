<?php

namespace Hamlet\Type\Types;

use DateTime;
use Exception;
use Hamlet\Type\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use function Hamlet\Type\_null;

class NullTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _null();
    }

    protected function baselineCast(mixed $value): null
    {
        if ($value == null) {
            return null;
        }
        throw new RuntimeException;
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
            [$resource,     false],
            [null,           true],
        ];
    }

    #[DataProvider('matchCases')] public function testMatch(mixed $value, bool $success): void
    {
        $this->assertEquals($success, _null()->matches($value));
    }

    #[DataProvider('matchCases')] public function testAssert(mixed $value, bool $success): void
    {
        $exceptionThrown = false;
        try {
            _null()->assert($value);
        } catch (Exception) {
            $exceptionThrown = true;
        }
        $this->assertEquals(!$success, $exceptionThrown);
    }
}
