<?php

namespace Hamlet\Type;

use Hamlet\Type\Resolvers\DefaultResolver;
use PHPUnit\Framework\TestCase;

class TrackUnmappedPropertiesTest extends TestCase
{
    public function testArrayPropertiesNotMappedToClassPropertiesDoNotThrowExceptionByDefault()
    {
        $value = [
            'id' => 1,
            'name' => 'Alexey',
            'hobby' => 'Cats, of course',
        ];

        require_once __DIR__ . '/../psalm-cases/classes/User.php';
        $this->assertInstanceOf(User::class, _class(User::class)->cast($value));
    }

    public function testArrayPropertiesNotMappedToClassPropertiesThrowExceptionWhenEnabled()
    {
        $resolver = new class() extends DefaultResolver {
            public function ignoreUnmappedProperties(): bool
            {
                return false;
            }
        };

        $value = [
            'id' => 1,
            'name' => 'Alexey',
            'hobby' => 'Cats, of course',
        ];

        require_once __DIR__ . '/../psalm-cases/classes/User.php';
        $this->expectException(CastException::class);
        _class(User::class)->resolveAndCast($value, $resolver);
    }

    public function testArrayPropertiesNotMappedToObjectLikePropertiesDoNotThrowExceptionByDefault()
    {
        $value = [
            'id' => 1,
            'name' => 'Alexey',
            'hobby' => 'Cats, of course',
        ];

        $type = Type::of('array{id:int,name:string}');
        $this->assertEquals($value, $type->cast($value));
    }

    public function testArrayPropertiesNotMappedToObjectLikePropertiesThrowExceptionWhenEnabled()
    {
        $resolver = new class() extends DefaultResolver {
            public function ignoreUnmappedProperties(): bool
            {
                return false;
            }
        };

        $value = [
            'id' => 1,
            'name' => 'Alexey',
            'hobby' => 'Cats, of course',
        ];

        $this->expectException(CastException::class);
        $type = Type::of('array{id:int,name:string}');
        $type->resolveAndCast($value, $resolver);
    }

    public function testClassPropertiesNotMappedToClassPropertiesDoNotThrowExceptionByDefault()
    {
        $value = (object) [
            'id' => 1,
            'name' => 'Alexey',
            'hobby' => 'Cats, of course',
        ];

        require_once __DIR__ . '/../psalm-cases/classes/User.php';
        $this->assertInstanceOf(User::class, _class(User::class)->cast($value));
    }

    public function testClassPropertiesNotMappedToClassPropertiesThrowExceptionWhenEnabled()
    {
        $resolver = new class() extends DefaultResolver {
            public function ignoreUnmappedProperties(): bool
            {
                return false;
            }
        };

        $value = (object) [
            'id' => 1,
            'name' => 'Alexey',
            'hobby' => 'Cats, of course',
        ];

        require_once __DIR__ . '/../psalm-cases/classes/User.php';
        $this->expectException(CastException::class);
        _class(User::class)->resolveAndCast($value, $resolver);
    }

    public function testClassPropertiesNotMappedToObjectLikePropertiesDoNotThrowExceptionByDefault()
    {
        $value = (object) [
            'id' => 1,
            'name' => 'Alexey',
            'hobby' => 'Cats, of course',
        ];

        $type = Type::of('array{id:int,name:string}');
        $this->assertEquals((array) $value, $type->cast($value));
    }

    public function testClassPropertiesNotMappedToObjectLikePropertiesThrowExceptionWhenEnabled()
    {
        $resolver = new class() extends DefaultResolver {
            public function ignoreUnmappedProperties(): bool
            {
                return false;
            }
        };

        $value = (object) [
            'id' => 1,
            'name' => 'Alexey',
            'hobby' => 'Cats, of course',
        ];

        $this->expectException(CastException::class);
        $type = Type::of('array{id:int,name:string}');
        $type->resolveAndCast($value, $resolver);
    }
}
