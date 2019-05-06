<?php

namespace Hamlet\Cast;

use Hoa\Compiler\Llk\Llk;
use Hoa\Compiler\Llk\Parser;
use Hoa\Compiler\Llk\TreeNode;
use Hoa\File\Read;

/**
 * @template T
 */
abstract class Type
{
    /** @var Parser|null */
    private static $compiler = null;

    /**
     * @var array
     * @psalm-var array<string,Type>
     */
    private static $typeCache = [];

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
        if (!isset(self::$typeCache[$declaration])) {
            if (self::$compiler === null) {
                self::$compiler = Llk::load(new Read(__DIR__ . '/../resources/grammar.pp'));
            }
            /** @var TreeNode $node */
            $node = self::$compiler->parse($declaration, 'expression');
            $parser = new TypeParser();
            return self::$typeCache[$declaration] = $parser->parse($node);
        } else {
            return self::$typeCache[$declaration];
        }
    }
}
