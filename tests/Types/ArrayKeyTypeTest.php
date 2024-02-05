<?php

namespace Hamlet\Type\Types;

use DateTime;
use Error;
use Exception;
use Hamlet\Type\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use function Hamlet\Type\_array_key;

class ArrayKeyTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _array_key();
    }

    protected function strictCastResultComparison(): bool
    {
        return false;
    }

    protected function baselineCast(mixed $value): int|string|null
    {
        try {
            $a = @[
                $value => 1
            ];
            return array_key_first($a);
        } catch (Error) {
            throw new RuntimeException;
        }
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
}
