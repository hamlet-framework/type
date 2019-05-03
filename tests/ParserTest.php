<?php

namespace Hamlet\Cast;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function types()
    {
        return [
            ['array<int,string|null>'],
            ['array<int>'],
            ['int'],
            ['\\DateTime|null']
        ];
    }

    /**
     * @dataProvider types()
     * @param string $declaration
     */
    public function testParse($declaration)
    {
        Assert::assertEquals($declaration, (string) Type::of($declaration));
    }
}
