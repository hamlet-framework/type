<?php

namespace Hamlet\Type;

use DateTime;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class CastTest extends TestCase
{
    public static function values(): array
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

    #[DataProvider('values')] public function testBooleanCast(mixed $value): void
    {
        $result = _bool()->cast($value);
        Assert::assertSame((bool) $value, $result);
    }

    #[DataProvider('values')] public function testFloatCast(mixed $value): void
    {
        try {
            $result = _float()->cast($value);
            Assert::assertEquals((float) $value, $result);
        } catch (CastException) {
            // @todo need to check that the warning would have been thrown
            Assert::assertTrue(true);
        }
    }

    #[DataProvider('values')] public function testIntegerCast(mixed $value): void
    {
        try {
            $result = _int()->cast($value);
            Assert::assertSame((int) $value, $result);
        } catch (CastException) {
            // @todo need to check that the warning would have been thrown
            Assert::assertTrue(true);
        }
    }

    #[DataProvider('values')] public function testStringCast(mixed $value): void
    {
        try {
            $result = _string()->cast($value);
            Assert::assertSame((string) $value, $result);
        } catch (CastException) {
            // @todo need to check that the warning would have been thrown
            Assert::assertTrue(true);
        }
    }

    #[DataProvider('values')] public function testObjectCast(mixed $value): void
    {
        $result = _object()->cast($value);
        Assert::assertEquals((object) $value, $result);
    }

    #[DataProvider('values')] public function testNumericCast(mixed $value): void
    {
        try {
            $result = _numeric()->cast($value);
            Assert::assertTrue(is_numeric($result));
        } catch (CastException) {
            Assert::assertTrue(is_object($value) && !method_exists($value, '__toString'));
        }
    }

    #[DataProvider('values')] public function testScalarCast(mixed $value): void
    {
        try {
            $result = _scalar()->cast($value);
            Assert::assertTrue(is_scalar($result));
        } catch (CastException) {
            Assert::assertTrue(is_object($value) && !method_exists($value, '__toString'));
        }
    }

    public function testObjectMatch(): void
    {
        $this->assertTrue(_object()->matches(new stdClass));
        $this->assertTrue(_object()->matches(new DateTime));
        $this->assertTrue(_object()->matches(function () {
        }));

        $this->assertFalse(_object()->matches(null));
        $this->assertFalse(_object()->matches(false));
        $this->assertFalse(_object()->matches(true));
        $this->assertFalse(_object()->matches(1));
        $this->assertFalse(_object()->matches(1.1));
        $this->assertFalse(_object()->matches('s'));
    }

    public function testIllegalFloatCastWithinList(): void
    {
        $this->expectException(CastException::class);
        $value = [1.1, null, new stdClass];
        $type = _list(_float());

        $type->cast($value);
    }
}
