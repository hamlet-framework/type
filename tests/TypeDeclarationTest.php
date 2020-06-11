<?php

namespace Hamlet\Cast;

use DateTime;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class TypeDeclarationTest extends TestCase
{
    public function testSimpleCast()
    {
        _string()->cast("this");
        _union(_string(), _null())->cast("this");
        _map(_int(), _class(DateTime::class))->cast([]);

        Assert::assertTrue(true);
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
        Assert::assertEquals('array<int,null|DateTime>', (string) $type);
    }

    public function testLiteralType()
    {
        $type = _literal('a', 1, false);

        Assert::assertTrue($type->matches('a'));
        Assert::assertTrue($type->matches(1));
        Assert::assertTrue($type->matches(false));

        Assert::assertFalse($type->matches('1'));
        Assert::assertFalse($type->matches(0));
        Assert::assertFalse($type->matches(null));
    }

    public function testPropertyType()
    {
        $value = ['id' => 12];
        $type = _object_like([
            'id' => _int()
        ]);
        assert($type->matches($value));

        Assert::assertEquals(12, $type->cast($value)['id']);
    }

    public function testIntersectionType()
    {
        /** @var Type<array{id:int,name:string,online?:bool}> $type */
        $type = Type::of('array{id:int,name:string,online?:bool}');

        Assert::assertEquals('array{id:int,name:string,online?:bool}', (string) $type);

        $value = ['id' => 12, 'name' => 'hey there'];
        Assert::assertTrue($type->matches($value));

        $value = ['id' => 1, 'name' => 'too', 'online' => false];
        Assert::assertTrue($type->matches($value));

        $value = ['id' => 1, 'name' => 'too', 'online' => 2.3];
        Assert::assertFalse($type->matches($value));

        $value = ['id' => 1];
        Assert::assertFalse($type->matches($value));
    }

    public function testIntersectionCast()
    {
        /** @var Type<array{id:int,name:string,online?:bool}> $type */
        $type = _object_like([
            'id'      => _int(),
            'name'    => _string(),
            'online?' => _bool()
        ]);

        $object = new class()
        {
            public function __toString(): string
            {
                return 'hey there';
            }
        };
        $value = ['id' => '12monkeys', 'name' => $object, 'online' => '0'];

        Assert::assertEquals(['id' => 12, 'name' => 'hey there', 'online' => false], $type->cast($value));
    }

    /**
     * @expectedException \Hamlet\Cast\CastException
     */
    public function testPropertyTypeThrowsExceptionOnMissingProperty()
    {
        _object_like(['id' => _int()])->cast([]);
    }

    public function testNonRequiredPropertyTypeThrowsNoExceptionOnMissingProperty()
    {
        _object_like(['id?' => _int()])->cast([]);
        Assert::assertTrue(true);
    }

    public function testListType()
    {
        $type = _list(_string());

        Assert::assertTrue($type->matches(['a', 'b']));
        Assert::assertFalse($type->matches(['a', 2]));
        Assert::assertEquals(['a', '2'], $type->cast(['a', 2]));
    }

    /**
     * @expectedException \Hamlet\Cast\CastException
     */
    public function testCastOrFail()
    {
        $type = _union(_class(DateTime::class), _null());
        $type->cast(1.1);
    }

    public function testCastable()
    {
        $type = _float();
        Assert::assertEquals(2.5, $type->cast("2.5"));
    }

    public function testCast()
    {
        $type = _list(_string());
        $list = $type->cast([0, 1.4, 'test', false, null]);

        Assert::assertEquals(['0', '1.4', 'test', '', ''], $list);
    }

    public function testCastToNull()
    {
        Assert::assertNull(_null()->cast(0));
        Assert::assertNull(_null()->cast(''));
        Assert::assertNull(_null()->cast([]));
        Assert::assertNull(_null()->cast(false));
    }
}
