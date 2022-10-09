<?php

namespace Hamlet\Type\Types;

use DateTime;
use Exception;
use Hamlet\Type\CastException;
use PHPUnit\Framework\TestCase;
use stdClass;
use function Hamlet\Type\_bool;

class BoolTypeTest extends TestCase
{
    public function matchCases()
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
            [true,          true],
            [false,         true],
            [0,             false],
            [1,             false],
            [-1,            false],
            ['',            false],
            ['0',           false],
            ['string',      false],
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
        $this->assertEquals($success, _bool()->matches($value));
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
            _bool()->assert($value);
        } catch (Exception $error) {
            $exceptionThrown = true;
        }
        $this->assertEquals(!$success, $exceptionThrown);
    }

    public function castCases()
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
            [true,          true,       false],
            [false,         false,      false],
            [0,             false,      false],
            [1,             true,       false],
            [-1,            true,       false],
            ['',            false,      false],
            ['0',           false,      false],
            ['string',      true,       false],
            [[],            false,      false],
            [[false],       true,       false],
            [[1],           true,       false],
            [[1, 3],        true,       false],
            [new stdClass,  true,       false],
            [$object,       true,       false],
            [new DateTime,  true,       false],
            [$callable,     true,       false],
            [$resource,     true,       false],
            [null,          false,      false],
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
            _bool()->cast($value);
        } else {
            $this->assertSame($result, _bool()->cast($value));
        }
    }
}
