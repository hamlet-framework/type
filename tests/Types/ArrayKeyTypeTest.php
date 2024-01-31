<?php

namespace Hamlet\Type\Types;

use DateTime;
use Exception;
use Hamlet\Type\CastException;
use PHPUnit\Framework\TestCase;
use stdClass;
use function Hamlet\Type\_array_key;

class ArrayKeyTypeTest extends TestCase
{
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

    /**
     * @dataProvider matchCases()
     * @param mixed $value
     * @param bool $success
     */
    public function testMatch($value, bool $success)
    {
        $this->assertEquals($success, _array_key()->matches($value));
    }

    /**
     * @dataProvider matchCases()
     * @param mixed $value
     * @param bool $success
     */
    public function testAssert($value, bool $success)
    {
        $exceptionThrown = false;
        try {
            _array_key()->assert($value);
        } catch (Exception $error) {
            $exceptionThrown = true;
        }
        $this->assertEquals(!$success, $exceptionThrown);
    }

    public static function castCases(): array
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
            [true,          1,                  false],
            [false,         0,                  false],
            [0,             0,                  false],
            [1,             1,                  false],
            [-1,            -1,                 false],
            ['',            '',                 false],
            ['0',           '0',                false],
            ['string',      'string',           false],
            [[],            0,                  false],
            [[1],           1,                  false],
            [[1, 3],        1,                  false],
            [new stdClass,  null,               true],
            [$object,       (string) $object,   false],
            [new DateTime,  null,               true],
            [$callable,     null,               true],
            [$resource,     (int) $resource,    false],
            [null,          0,                  false],
        ];
    }

    /**
     * @dataProvider castCases()
     * @param mixed $value
     * @param mixed $result
     * @param bool $exceptionThrown
     */
    public function testCast($value, $result, bool $exceptionThrown)
    {
        if ($exceptionThrown) {
            $this->expectException(CastException::class);
            _array_key()->cast($value);
        } else {
            $this->assertSame($result, _array_key()->cast($value));
        }
    }
}
