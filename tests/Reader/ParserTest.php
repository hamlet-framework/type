<?php

namespace Hamlet\Cast\Reader;

use Hoa\Compiler\Llk\Llk;
use Hoa\Compiler\Visitor\Dump;
use Hoa\File\Read;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function typeDeclarations()
    {
        return [
            ['\\Hamlet\\Cast\\Type'],
            ['array'],
            ['int'],
            ['array<string, array<string, array{DateTime}>>'],
            ['array|null|false|1|1.1'],
            ['array{id:int|null,name?:string|null}'],
            ["('a'|'b'|'c')"],
            ["'a'|'b'"],
            ['string[][]'],
            ['(1|false)[]'],
            ['(A::FOO|A::BAR)'],
            ['(A::FOO|false|callable():void)[][][]'],
            ['int[]'],
            ["callable(('a'|'b'), int):(string|array{\\DateTime}|callable():void)"],
            ['array{0: string, 1: string, foo: stdClass, 28: false}'],
            ['non-empty-array{string,string,foo:non-empty-array,23:boolean}'],
            ['A::class|B::class'],
            ['Closure(bool):int'],
            ['array<string,\DateTime>'],
            ['Generator<T0, int, mixed, T0>'],
            ['int[]|string'],
            ['int[]|string & array'],
            ['callable(array{int}[]):(int|null)'],
            ['array<string,int[]|object>[]'],
            ['Generator<T0, int, mixed, T0> & (object|null)']
        ];
    }

    /**
     * @dataProvider typeDeclarations()
     * @param string $specification
     */
    public function testHoaParser(string $specification)
    {
        $compiler = Llk::load(new Read(__DIR__ . '/../../src/Reader/grammar.pp'));
        $ast = $compiler->parse($specification, 'expression');
        $dump = new Dump();

        echo PHP_EOL;
        echo $specification . PHP_EOL;
        echo $dump->visit($ast);

        Assert::assertTrue(true);
    }
}
