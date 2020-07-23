<?php

namespace Hamlet\Cast\Parser;

use Hamlet\Cast\Type;
use Hoa\Compiler\Llk\Llk;
use Hoa\Compiler\Llk\TreeNode;
use Hoa\Compiler\Visitor\Dump;
use Hoa\File\Read;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use function Hamlet\Cast\_class;

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
            ['Hamlet\\Cast\\Type'],
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
            ['array<string,int[]|object>[]'],
            ['int[]'],
            ['array{0: string, 1: string, foo: stdClass, 28: false}'],
            ['array|array{id:int}'],
            ['non-empty-array{0:string,1:string,foo:non-empty-array,23:boolean}'],
            ['array<string,DateTime>'],
            ['callable()'],
            ["callable(('a'|'b'), int):(string|array{DateTime}|callable():int)"],
            ['Closure(bool):int'],
            ['Generator<T0, int, mixed, T0>'],
            ['callable(array{0:int}[]):(int|null)'],
        ];
    }

    /**
     * @dataProvider typeDeclarations()
     * @param string $specification
     */
    public function testHoaParser(string $specification)
    {
        $compiler = Llk::load(new Read(__DIR__ . '/../../resources/grammar.pp'));
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

        // echo PHP_EOL;
        // echo $specification . PHP_EOL;
        // echo $type . PHP_EOL;

        Assert::assertNotNull($type);
    }

    public function phpDocDeclarations()
    {
        return [
            ['
                /** @var string $a */
            '],
            ['
                /***********
                 * @var int|string|null
                 ***********/
            '],
            ["
                /*
                 *
                 *
                 * This is the set of objects
                 * @psalm-var object|array{'*': int}
                 */
            "],
            ['
                /**
                 * Check if a given lexeme is matched at the beginning of the text.
                 *
                 * @param   string  $lexeme    Name of the lexeme.
                 * @param   string  $regex     Regular expression describing the lexeme.
                 * @param   int     $offset    Offset.
                 * @return  array
                 * @throws  \Hoa\Compiler\Exception\Lexer
                 */
            '],
            ['
                /**
                 * A summary informing the user what the associated element does.
                 *
                 * A *description*, that can span multiple lines, to go _in-depth_ into the details of this element
                 * and to provide some background information or textual references.
                 *
                 * @param string $myArgument With a *description* of this argument, these may also
                 *    span multiple lines.
                 *
                 * @return void
                 */  
            ']
        ];
    }

    /**
     * @dataProvider phpDocDeclarations()
     * @param string $specification
     */
    public function testPhpDocParser(string $specification)
    {
        $data = DocBlockParser::parseDoc($specification);
        Assert::assertNotNull($data);
    }

    /**
     * @throws ReflectionException
     */
    public function testNameResolver()
    {
        require_once __DIR__ . '/../../psalm-cases/TestClass.php';

        $type = new ReflectionClass(TestClass::class);
        $type->getProperty('a');

        $typeA = DocBlockParser::fromProperty($type, $type->getProperty('a'));
        $typeB = DocBlockParser::fromProperty($type, $type->getProperty('b'));

        Assert::assertEquals('array<int,array<array{0:DateTime}>>', (string) $typeA);
        Assert::assertEquals("'x'|'y'|'z'|Hamlet\Cast\CastException|DateTime|null", (string) $typeB);
    }

    /**
     * @dataProvider typeDeclarations()
     * @param string $specification
     */
    public function testSerialization(string $specification)
    {
        $type = Type::of($specification);

        $copy = eval('return ' . $type->serialize() . ';');
        $this->assertEquals((string) $type, (string) $copy);
    }

    public function testParsingOfUglyNestedStructures()
    {
        if (version_compare(phpversion(), '7.4') < 0) {
            $this->assertTrue(true);
            return;
        }

        require_once __DIR__ . '/../../psalm-cases/UglyNestedStructure.php';
        $typeA = new ReflectionClass(\Hamlet\Cast\Parser\A::class);
        $typeB = new ReflectionClass(\Hamlet\Cast\Parser\N0\N1\B::class);
        $typeC = new ReflectionClass(\C::class);

        $this->assertEquals(
            \DateTime::class,
            (string) DocBlockParser::fromProperty($typeA, $typeA->getProperty('c'))
        );
        $this->assertEquals(
            \Hamlet\Cast\Parser\A::class,
            (string) DocBlockParser::fromProperty($typeB, $typeB->getProperty('a'))
        );
        $this->assertEquals(
            \Hamlet\Cast\Parser\A::class,
            (string) DocBlockParser::fromProperty($typeC, $typeC->getProperty('a'))
        );
        $this->assertEquals(
            \Hamlet\Cast\Parser\N0\N1\B::class,
            (string) DocBlockParser::fromProperty($typeC, $typeC->getProperty('b'))
        );
    }
}
