<?php

namespace Hamlet\Cast;

use Hoa\Compiler\Llk\Llk;
use Hoa\Compiler\Llk\TreeNode;
use Hoa\Compiler\Visitor\Dump;
use Hoa\File\Read;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function typeDeclarations()
    {
        return [
            ['int'],
            ['string'],
            ['false'],
            ['null'],
            ["'a'|'b'"],
            ['\\Hamlet\\Cast\\Type'],
            ['array'],
            ['array<string>'],
            ["array<string, array<string, int|'a'|false>>"],
            ['array<string, array<string, array{0:DateTime}>>'],
            ['array|null|false|1|1.1'],
            ['array{id:int|null,name?:string|null}'],
            ["('a'|'b'|'c')"],
            ['string[][]'],
            ['(1|false)[]'],
            ['int[]|string'],
            ['int[]|string & array'],
            ['array<string,int[]|object>[]'],
            ['int[]'],
            ['array{0: string, 1: string, foo: stdClass, 28: false}'],
            ['array|array{id:int}'],
            ['non-empty-array{0:string,1:string,foo:non-empty-array,23:boolean}'],
            ['array<string,\DateTime>'],
            ['callable()'],
            ["callable(('a'|'b'), int):(string|array{\\DateTime}|callable():int)"],
            ['Closure(bool):int'],
            ['Generator<T0, int, mixed, T0>'],
            ['callable(array{0:int}[]):(int|null)'],
            ['Generator<T0, int, mixed, T0> & (object|null)'],
            // ['A::class|B::class'],
            // ['(A::FOO|A::BAR)'],
            // ['(A::FOO|false|callable():void)[][][]']
        ];
    }

    /**
     * @dataProvider typeDeclarations()
     * @param string $specification
     */
    public function testHoaParser(string $specification)
    {
        $compiler = Llk::load(new Read(__DIR__ . '/../resources/grammar.pp'));
        $ast = _class(TreeNode::class)->cast($compiler->parse($specification, 'expression'));
        $dump = new Dump();

        // echo PHP_EOL;
        // echo $specification . PHP_EOL;
        // echo $dump->visit($ast);

        Assert::assertTrue(true);
    }

    /**
     * @dataProvider typeDeclarations()
     * @param string $specification
     */
    public function testTypeParser(string $specification)
    {
        $type = Type::of($specification);

        echo PHP_EOL;
        echo $specification . PHP_EOL;
        echo $type . PHP_EOL;

        Assert::assertTrue(true);
    }
}
