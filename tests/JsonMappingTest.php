<?php

namespace Hamlet\Cast;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class JsonMappingTest extends TestCase
{
    public function testSimpleJsonMapping()
    {
        $type = Type::of('array<array{id:int,name:string,age?:int}>');
        $json = '
            [
                {
                    "id": 1,
                    "name": "Vanya",
                    "age": 10
                },
                {
                    "id": 2,
                    "name": "Masha"
                }
            ]
        ';
        $value = $type->cast(json_decode($json));

        Assert::assertCount(2, $value);
        Assert::assertEquals(10, $value[0]['age']);
        Assert::assertArrayNotHasKey('age', $value[1]);
    }

    public function testObjectMapping()
    {
        $json = '
            [
                {
                    "id": 1,
                    "name": "Vanya",
                    "address": {
                        "city": "Moscow"
                    }
                },
                {
                    "id": 2,
                    "name": "Masha",
                    "address": {
                        "city": "Vladivostok"
                    }
                },
                {
                    "id": 3,
                    "name": "Misha"
                }
            ]
        ';
        /** @var User[] $value */
        $users = _list(_class(User::class))->cast(json_decode($json));

        Assert::assertCount(3, $users);
        Assert::assertEquals('Vladivostok', $users[1]->address()->city());
        Assert::assertNull($users[2]->address());
    }
}
