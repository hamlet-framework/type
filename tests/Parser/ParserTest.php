<?php

namespace Hamlet\Type\Parser;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use function Hamlet\Type\type_of;

class ParserTest extends TestCase
{
    #[Before] public function _setUp(): void
    {
        Cache::purge();
    }

    public static function typeDeclarations(): array
    {
        return [
            ['int'],
            ['string'],
            ['false'],
            ['float'],
            ['double'],
            ['numeric'],
            ['numeric-string'],
            ['bool'],
            ['mixed'],
            ['resource'],
            ['array-key'],
            ['1'],
            ['"a"'],
            ["'a'"],
            ['0.5'],
            ['null'],
            ['array'],
            ['array<string>'],
            ['array<mixed>'],
            ['array<resource|numeric-string|numeric>'],
            ['array<array-key,int|float|double>'],
            ['array<int,array<true|1|0.4>|false>'],
            ["'a'|'b'"],
            ['Hamlet\\Type\\Type'],
            ['\\DateTime'],
            ["array<string, array<string, int|'a'|false>>"],
            ['array|null|false|1|1.1'],
            ["('a'|'b'|'c')"],
            ['string[][]'],
            ['(1|false)[]'],
            ['int[]|string'],
            ['array<string,int[]|object>[]'],
            ['int[]'],
            ['array|list{int}'],
            ['array<string, array<string, list{DateTime}>>'],
            ['list{int|null,?string}'],
            ['list{string,string,\stdClass,false}'],
            ['list{string,string,non-empty-array,boolean}'],
            ['array<string,DateTime>'],
            ['list<array<array<int,string|null>>|bool|null>'],
        ];
    }

    #[DataProvider('typeDeclarations')] public function testTypeParser(string $specification): void
    {
        $type = type_of($specification);
        Assert::assertNotNull($type);
    }

    public static function phpDocDeclarations(): array
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
                 * @psalm-var object|list{'*': int}
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

    #[DataProvider('phpDocDeclarations')] public function testPhpDocParser(string $specification)
    {
        $data = DocBlockParser::parseDoc($specification);
        Assert::assertNotNull($data);
    }

    /**
     * @throws ReflectionException
     */
    public function testNameResolver(): void
    {
        require_once __DIR__ . '/../../psalm-cases/classes/TestClass.php';

        $type = new ReflectionClass(TestClass::class);
        $type->getProperty('a');

        $typeA = DocBlockParser::fromProperty($type, $type->getProperty('a'));
        $typeB = DocBlockParser::fromProperty($type, $type->getProperty('b'));

        Assert::assertEquals('array<int,array<list{DateTime}>>', (string) $typeA);
        Assert::assertEquals("'x'|'y'|'z'|Hamlet\Type\CastException|DateTime|null", (string) $typeB);
    }

    #[DataProvider('typeDeclarations')] public function testSerialization(string $specification): void
    {
        $type = type_of($specification);

        $copy = eval('return ' . $type->serialize() . ';');
        $this->assertEquals((string) $type, (string) $copy, 'Failed on ' . $specification);
    }

    public function testParsingOfUglyNestedStructures(): void
    {
        if (version_compare(phpversion(), '7.4') < 0) {
            $this->assertTrue(true);
            return;
        }

        require_once __DIR__ . '/../../psalm-cases/classes/UglyNestedStructure.php';
        $typeA = new ReflectionClass(\Hamlet\Type\Parser\A::class);
        $typeB = new ReflectionClass(\Hamlet\Type\Parser\N0\N1\B::class);
        $typeC = new ReflectionClass(\C::class);

        $this->assertEquals(
            \DateTime::class,
            (string) DocBlockParser::fromProperty($typeA, $typeA->getProperty('c'))
        );
        $this->assertEquals(
            \Hamlet\Type\Parser\A::class,
            (string) DocBlockParser::fromProperty($typeB, $typeB->getProperty('a'))
        );
        $this->assertEquals(
            \Hamlet\Type\Parser\A::class,
            (string) DocBlockParser::fromProperty($typeC, $typeC->getProperty('a'))
        );
        $this->assertEquals(
            \Hamlet\Type\Parser\N0\N1\B::class,
            (string) DocBlockParser::fromProperty($typeC, $typeC->getProperty('b'))
        );
    }
}
