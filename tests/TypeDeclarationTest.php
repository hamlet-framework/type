<?php

namespace Hamlet\Cast;

use DateTime;
use PhpParser\Node\Expr\Cast;
use PHPUnit\Framework\TestCase;
use stdClass;

class TypeDeclarationTest extends TestCase
{
    public function testSimpleCast()
    {
        _string()->cast("this");
        _union(_string(), _null())->cast("this");
        _map(_int(), _class(DateTime::class))->cast([]);

        $this->assertTrue(true);
    }

    public function testTypeString()
    {
        $type = _map(
            _int(),
            _union(
                _null(),
                _class(DateTime::class)
            )
        );
        $this->assertEquals('array<int,null|DateTime>', (string)$type);
    }

    public function testLiteralType()
    {
        $type = _literal('a', 1, false, null);

        $this->assertTrue($type->matches('a'));
        $this->assertTrue($type->matches(1));
        $this->assertTrue($type->matches(false));
        $this->assertTrue($type->matches(null));

        $this->assertFalse($type->matches('1'));
        $this->assertFalse($type->matches(0));
        $this->assertFalse($type->matches(true));

        $this->assertEquals('a', $type->cast('a'));
        $this->assertEquals(1, $type->cast('1'));
        $this->assertEquals(false, $type->cast(false));
        $this->assertEquals(false, $type->cast('0'));

        $this->expectException(CastException::class);
        $type->cast(new stdClass);
    }

    public function testPropertyType()
    {
        $value = ['id' => 12];
        $type = _object_like([
            'id' => _int()
        ]);
        assert($type->matches($value));

        $this->assertEquals(12, $type->cast($value)['id']);
    }

    public function testIntersectionType()
    {
        /** @var Type<array{id:int,name:string,online?:bool}> $type */
        $type = Type::of('array{id:int,name:string,online?:bool}');

        $this->assertEquals('array{id:int,name:string,online?:bool}', (string)$type);

        $value = ['id' => 12, 'name' => 'hey there'];
        $this->assertTrue($type->matches($value));

        $value = ['id' => 1, 'name' => 'too', 'online' => false];
        $this->assertTrue($type->matches($value));

        $value = ['id' => 1, 'name' => 'too', 'online' => 2.3];
        $this->assertFalse($type->matches($value));

        $value = ['id' => 1];
        $this->assertFalse($type->matches($value));
    }

    public function testIntersectionCast()
    {
        /** @var Type<array{id:int,name:string,online?:bool}> $type */
        $type = _object_like([
            'id' => _int(),
            'name' => _string(),
            'online?' => _bool()
        ]);

        $object = new class() {
            public function __toString(): string
            {
                return 'hey there';
            }
        };
        $value = ['id' => '12monkeys', 'name' => $object, 'online' => '0'];

        $this->assertEquals(['id' => 12, 'name' => 'hey there', 'online' => false], $type->cast($value));
    }

    public function testPropertyTypeThrowsExceptionOnMissingProperty()
    {
        $this->expectException(CastException::class);
        _object_like(['id' => _int()])->cast([]);
    }

    public function testNonRequiredPropertyTypeThrowsNoExceptionOnMissingProperty()
    {
        _object_like(['id?' => _int()])->cast([]);
        $this->assertTrue(true);
    }

    public function testListType()
    {
        $type = _list(_string());

        $this->assertTrue($type->matches(['a', 'b']));
        $this->assertFalse($type->matches(['a', 2]));
        $this->assertFalse($type->matches('a, b, c'));

        $this->assertEquals(['a', '2'], $type->cast(['a', 2]));

        $this->expectException(CastException::class);
        $type->cast('a, b, c');
    }

    public function testCastOrFail()
    {
        $type = _union(_class(DateTime::class), _null());

        $this->expectException(CastException::class);
        $type->cast(1.1);
    }

    public function testCastable()
    {
        $type = _float();
        $this->assertEquals(2.5, $type->cast("2.5"));
    }

    public function testUnionType()
    {
        $type = _union(_int(), _null());
        $this->assertTrue($type->matches(1));
        $this->assertTrue($type->matches(null));
        $this->assertFalse($type->matches(new stdClass));
    }

    public function invalidNumericStrings()
    {
        return [
            ['hey'],
            ['null'],
            [[]],
            [new stdClass],
            [new class() {
                public function __toString()
                {
                    return 'sousage';
                }
            }],
        ];
    }

    /**
     * @dataProvider invalidNumericStrings()
     * @param mixed $value
     */
    public function testInvalidNumericStrings($value)
    {
        $this->expectException(CastException::class);
        _numeric_string()->cast($value);
    }

    public function testNumericString()
    {
        $type = _numeric_string();
        $this->assertTrue($type->matches('1.2'));
        $this->assertTrue($type->matches('1'));
        $this->assertFalse($type->matches(''));
        $this->assertFalse($type->matches(false));
        $this->assertFalse($type->matches(null));
        $this->assertFalse($type->matches([]));

        $object = new class()
        {
            public function __toString()
            {
                return "1.2";
            }
        };
        $this->assertEquals('1.2', $type->cast($object));
        $this->assertEquals('1', $type->cast(1));
        $this->assertEquals('1', $type->cast(true));
        $this->assertEquals('0', $type->cast(false));
        $this->assertEquals('0', $type->cast(null));
    }

    public function values()
    {
        return [
            [null],
            [true],
            [false],
            ['a'],
            [1],
            [1.0],
            [fopen(__FILE__, 'r')],
            [new stdClass],
            [[]],
            [[1]],
            [function () {
            }],
        ];
    }

    /**
     * @dataProvider values()
     * @param mixed $value
     */
    public function testMixedType($value)
    {
        $this->assertTrue(_mixed()->matches($value));
        $this->assertSame($value, _mixed()->cast($value));
    }

    public function testObjectLikeType()
    {
        $type = _object_like([
            'name' => _string(),
            'age?' => _int(),
        ]);
        $this->assertTrue($type->matches(['name' => 'Ivan']));
        $this->assertTrue($type->matches(['name' => 'Ivan', 'address' => 'Moscow']));
        $this->assertTrue($type->matches(['name' => 'Ivan', 'age' => 22]));
        $this->assertFalse($type->matches(['name' => 'Ivan', 'age' => 'unknown']));
        $this->assertFalse($type->matches("user"));

        $this->expectException(CastException::class);
        $type->cast("user");
    }

    public function testCast()
    {
        $type = _list(_string());
        $list = $type->cast([0, 1.4, 'test', false, null]);

        $this->assertEquals(['0', '1.4', 'test', '', ''], $list);
    }

    public function testCastToNull()
    {
        $this->assertNull(_null()->cast(0));
        $this->assertNull(_null()->cast(''));
        $this->assertNull(_null()->cast([]));
        $this->assertNull(_null()->cast(false));
    }

    public function testNumericStringCastsToString()
    {
        $value = _list(_numeric_string())->cast([1.0, "2.34", -1]);
        $this->assertSame(['1', '2.34', '-1'], $value);
    }
}
