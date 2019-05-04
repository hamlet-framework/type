<?php

namespace Hamlet\Cast\Reader;

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
            ['int[]'],
            ["callable(('a'|'b'), int):(string|array{\\DateTime}|callable():void)"],
            ['array{0: string, 1: string, foo: stdClass, 28: false}'],
            ['non-empty-array{string,string,foo:non-empty-array,23:boolean}'],
            ['A::class|B::class'],
            ['Closure(bool):int'],
            ['array<string,\DateTime>'],
            ['Generator<T0, int, mixed, T0>'],
            ['Generator<T0, int, mixed, T0> & object']
        ];
    }

    /**
     * @dataProvider typeDeclarations()
     * @param string $specification
     */
    public function testHoaParser(string $specification)
    {
        $compiler = Llk::load(new Read(__DIR__ . '/../../src/Reader/grammar.pp'));
        /** @var TreeNode $ast */
        $ast = $compiler->parse($specification, 'type');
        $visitor = new Dump();
        echo $specification . PHP_EOL;
        echo $visitor->visit($ast);

        Assert::assertTrue(true);
    }
}
