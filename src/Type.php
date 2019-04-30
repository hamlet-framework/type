<?php

namespace Hamlet\Cast;

use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use RuntimeException;

/**
 * @template T
 */
abstract class Type
{
    /**
     * @param mixed $value
     * @return bool
     */
    abstract public function matches($value): bool;

    /**
     * @param mixed $value
     * @return mixed
     * @psalm-return T
     * @psalm-assert T $value
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public function assert($value)
    {
        assert($this->matches($value));
        return $value;
    }

    /**
     * @param mixed $value
     * @return mixed
     * @psalm-return T
     * @psalm-assert T $a
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedReturnTypeCoercion
     */
    abstract public function cast($value);

    /**
     * @return string
     */
    abstract public function __toString();

    public static function of(string $declaration): Type
    {
        static $lexer;
        static $parser;
        if (!isset($lexer) || !isset($parser)) {
            $lexer = new Lexer();
            $parser = new TypeParser();
        }
        $tokens = new TokenIterator($lexer->tokenize($declaration));
        return static::fromNode($parser->parse($tokens));
    }

    private static function fromNode(TypeNode $node): Type
    {
        if ($node instanceof IdentifierTypeNode) {
            switch ($node->name) {
                case 'bool':
                    return _bool();
                case 'float':
                    return _float();
                case 'int':
                    return _int();
                case 'string':
                    return _string();
                case 'null':
                    return _null();
                default:
                    if (class_exists($node->name)) {
                        return _class($node->name);
                    }
                    throw new RuntimeException('Unknown type ' . $node->name);
            }
        } elseif ($node instanceof UnionTypeNode) {
            $enum = [];
            foreach ($node->types as $typeNode) {
                $enum[] = static::fromNode($typeNode);
            }
            return _union(...$enum);
        } elseif ($node instanceof GenericTypeNode) {
            switch ($node->type->name) {
                case 'array':
                    if (count($node->genericTypes) == 2) {
                        return _map(
                            static::fromNode($node->genericTypes[0]),
                            static::fromNode($node->genericTypes[1])
                        );
                    } else {
                        return _list(
                            static::fromNode($node->genericTypes[0])
                        );
                    }
            }
        }
    }
}
