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

    public function testJsonEmbeddedCasting()
    {
        $json = '
            {
                "Lyuba": "2.34",
                "Sveta": "17.01"
            }
        ';
        $weights = _map(_string(), _float())->cast(json_decode($json));

        Assert::assertCount(2, $weights);
        Assert::assertArrayHasKey('Lyuba', $weights);
        Assert::assertArrayHasKey('Sveta', $weights);
        Assert::assertSame(2.34, $weights['Lyuba']);
        Assert::assertSame(17.01, $weights['Sveta']);
    }

    public function testMapOfMaps()
    {
        $json = '
            {
                "Dostoyevsky": {
                    "Crime and Punishment": 1866,
                    "Idiot": 1869
                },
                "Tolstoy": {
                    "War and Peace": 1869
                },
                "Bulgakov": {
                    "The Master and Margarita": "1940"
                }
            }
        ';
        $writers = _map(_string(), _map(_string(), _int()))->cast(json_decode($json));

        Assert::assertCount(3, $writers);
        Assert::assertSame(1866, $writers['Dostoyevsky']['Crime and Punishment']);
        Assert::assertSame(1869, $writers['Dostoyevsky']['Idiot']);
        Assert::assertSame(1869, $writers['Tolstoy']['War and Peace']);
        Assert::assertSame(1940, $writers['Bulgakov']['The Master and Margarita']);
    }
}
