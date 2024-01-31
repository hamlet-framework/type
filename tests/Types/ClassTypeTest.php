<?php

namespace Hamlet\Type\Types;

use DateTime;
use DateTimeImmutable;
use Hamlet\Type\Address;
use Hamlet\Type\CastException;
use Hamlet\Type\Type;
use PHPUnit\Framework\TestCase;
use stdClass;
use function Hamlet\Type\_array;
use function Hamlet\Type\_class;
use function Hamlet\Type\_int;
use function Hamlet\Type\_mixed;
use function Hamlet\Type\_string;

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

    /**
     * @dataProvider matchCases()
     * @param mixed $value
     * @param bool $success
     */
    public function testMatch($value, bool $success)
    {
        $this->assertEquals($success, _class(stdClass::class)->matches($value), 'Failed on ' . print_r($value, true));
    }

    /**
     * @dataProvider matchCases()
     * @param mixed $value
     * @param bool $success
     */
    public function testAssert($value, bool $success)
    {
        if (!$success) {
            $this->expectException(CastException::class);
        }
        _class(stdClass::class)->assert($value);
        $this->assertTrue(true);
    }

    public function testParsing()
    {
        $type = Type::of('\\DateTime');
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

    /**
     * @dataProvider castCases()
     * @param mixed $value
     * @param bool $exceptionThrown
     */
    public function testCast($value, bool $exceptionThrown)
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
