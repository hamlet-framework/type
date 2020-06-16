<?php

namespace Hamlet\Type;

use DateTime;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use stdClass;

class CastTest extends TestCase
{
    public function values()
    {
        $handle = fopen(__FILE__, 'r');
        $callback = function (): int {
            return 1;
        };
        $object = new class()
        {
            public function __toString(): string
            {
                return '1';
            }
        };
        return [
            [1],
            [0],
            [true],
            [false],
            ["string"],
            ["1"],
            ["1.0"],
            ["true"],
            [[]],
            [[1]],
            [[false]],
            [[[]]],
            [[null]],
            [new stdClass()],
            [new DateTime()],
            [$handle],
            [$callback],
            ['strtolower'],
            [$object, '__toString'],
            [$object]
        ];
    }

    /**
     * @dataProvider values()
     * @param mixed $value
     */
    public function testBooleanCast($value)
    {
        $result = _bool()->cast($value);
        Assert::assertSame((bool) $value, $result);
    }

    /**
     * @dataProvider values()
     * @param mixed $value
     */
    public function testFloatCast($value)
    {
        try {
            $result = _float()->cast($value);
            Assert::assertEquals((float) $value, $result);
        } catch (CastException $e) {
            // @todo need to check that the warning would have been thrown
            Assert::assertTrue(true);
        }
    }

    /**
     * @dataProvider values()
     * @param mixed $value
     */
    public function testIntegerCast($value)
    {
        try {
            $result = _int()->cast($value);
            Assert::assertSame((int) $value, $result);
        } catch (CastException $e) {
            // @todo need to check that the warning would have been thrown
            Assert::assertTrue(true);
        }
    }

    /**
     * @dataProvider values()
     * @param mixed $value
     */
    public function testStringCast($value)
    {
        try {
            $result = _string()->cast($value);
            Assert::assertSame((string) $value, $result);
        } catch (CastException $e) {
            // @todo need to check that the warning would have been thrown
            Assert::assertTrue(true);
        }
    }

    /**
     * @dataProvider values()
     * @param mixed $value
     */
    public function testObjectCast($value)
    {
        $result = _object()->cast($value);
        Assert::assertEquals((object) $value, $result);
    }

    /**
     * @dataProvider values()
     * @param mixed $value
     */
    public function testNumericCast($value)
    {
        try {
            $result = _numeric()->cast($value);
            Assert::assertTrue(is_numeric($result));
        } catch (CastException $exception) {
            Assert::assertTrue(is_object($value) && !method_exists($value, '__toString'));
        }
    }

    /**
     * @dataProvider values()
     * @param mixed $value
     */
    public function testScalarCast($value)
    {
        try {
            $result = _scalar()->cast($value);
            Assert::assertTrue(is_scalar($result));
        } catch (CastException $exception) {
            Assert::assertTrue(is_object($value) && !method_exists($value, '__toString'));
        }
    }

    public function testIllegalFloatCastWithinList()
    {
        $this->expectException(CastException::class);
        $value = [1.1, null, new stdClass];
        $type = _list(_float());

        $type->cast($value);
    }
}
