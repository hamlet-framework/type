<?php

namespace Hamlet\Type\Types;

use DateTime;
use DateTimeImmutable;
use Hamlet\Type\Address;
use Hamlet\Type\CastException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use function Hamlet\Type\_class;
use function Hamlet\Type\type_of;

class ClassTypeTest extends TestCase
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
            [new stdClass,  true],
            [$object,       false],
            [new DateTime,  false],
            [$callable,     false],
            [$invokable,    false],
            [$resource,     false],
            [null,          false],
            [[1 => 2],      false],
            [['a' => 0],    false],
        ];
    }

    #[DataProvider('matchCases')] public function testMatch(mixed $value, bool $success): void
    {
        $this->assertEquals($success, _class(stdClass::class)->matches($value), 'Failed on ' . print_r($value, true));
    }

    #[DataProvider('matchCases')] public function testAssert(mixed $value, bool $success): void
    {
        if (!$success) {
            $this->expectException(CastException::class);
        }
        _class(stdClass::class)->assert($value);
        $this->assertTrue(true);
    }

    public function testParsing()
    {
        $type = type_of('\\DateTime');
        $this->assertTrue($type->matches(new DateTime));
        $this->assertFalse($type->matches(new DateTimeImmutable));
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
        $invokable = new class()
        {
            public function __invoke()
            {
            }
        };
        $stdObject = new stdClass;

        return [
            [true,       true],
            [false,      true],
            [0,          true],
            [1,          true],
            [-1,         true],
            ['',         true],
            ['0',        true],
            ['x1',       true],
            [[],         false],
            [[false],    false],
            [[1],        false],
            [[1, 3],     false],
            [$object,    true],
            [$callable,  true],
            [$invokable, true],
            [$resource,  true],
            [null,       true],
            [$stdObject, false]
        ];
    }

    #[DataProvider('castCases')] public function testCast(mixed $value, bool $exceptionThrown): void
    {
        if ($exceptionThrown) {
            $this->expectException(CastException::class);
        }
        _class(stdClass::class)->cast($value);
        $this->assertTrue(true);
    }

    public function testNonNullableField()
    {
        require_once __DIR__ . '/../../psalm-cases/classes/Address.php';
        try {
            _class(Address::class)->cast(['country' => 'Thailand']);
        } catch (CastException $e) {
            $this->assertSame(['country' => 'Thailand'], $e->value());
            $this->assertEquals(Address::class, (string) $e->targetType());
            return;
        }
        $this->fail('Exception excepted');
    }
}
