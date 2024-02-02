<?php declare(strict_types=1);

namespace Hamlet\Type\Parser;

use Hamlet\Type\Type;
use PhpParser\NameContext;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;

/**
 * @psalm-internal Hamlet\Type
 */
final class DeclarationReader
{
    private static ?self $instance;

    public static function instance(): self
    {
        return self::$instance ??= new self;
    }

    private function __construct()
    {
    }

    public function read(string $declaration, ?NameContext $nameContext = null): Type
    {
        $lexer = new Lexer;
        $constExprParser = new ConstExprParser(true, true, []);
        $typeParser = new TypeParser($constExprParser, true, []);

        $tokens = new TokenIterator($lexer->tokenize($declaration));
        $node = $typeParser->parse($tokens);

        return (new DocBlockAstTraverser)->traverse($node, $nameContext);
    }
}
