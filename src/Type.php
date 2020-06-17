<?php declare(strict_types=1);

namespace Hamlet\Type;

use Hamlet\Type\Parser\TypeParser;
use Hamlet\Type\Resolvers\DefaultResolver;
use Hamlet\Type\Resolvers\Resolver;
use Hoa\Compiler\Llk\Llk;
use Hoa\Compiler\Llk\Parser;
use Hoa\Compiler\Llk\TreeNode;
use Hoa\File\Read;
use PhpParser\NameContext;
use PhpParser\NodeVisitor\NameResolver;

/**
 * @template T
 */
abstract class Type
{
    /**
     * @var Parser|null
     */
    private static $compiler = null;

    /**
     * @var Type[]
     * @psalm-var array<string,Type>
     */
    private static $typeCache = [];

    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true T $value
     */
    abstract public function matches($value): bool;

    /**
     * @param mixed $value
     * @return mixed
     * @psalm-return T
     * @psalm-assert T $value
     */
    public function assert($value)
    {
        assert($this->matches($value), new CastException($value, $this));
        return $value;
    }

    /**
     * @param mixed $value
     * @return mixed
     * @psalm-return T
     */
    public function cast($value)
    {
        $resolver = new DefaultResolver;
        return $this->resolveAndCast($value, $resolver);
    }

    /**
     * @param mixed $value
     * @param Resolver $resolver
     * @return mixed
     * @psalm-return T
     */
    public function resolveAndCast($value, Resolver $resolver)
    {
        return $this->cast($value);
    }

    abstract public function __toString(): string;

    /**
     * @param string $declaration
     * @param NameContext|null $nameContext
     * @return Type
     */
    public static function of(string $declaration, NameContext $nameContext = null): Type
    {
        switch ($declaration) {
            case 'string':
                return new StringType;
            case 'int':
                return new IntType;
            case 'float':
                return new FloatType;
            case 'bool':
            case 'boolean':
                return new BoolType;
            case 'mixed':
                return new MixedType;
            case 'resource':
                return new ResourceType;
        }
        if (self::$compiler === null) {
            self::$compiler = Llk::load(new Read(__DIR__ . '/../resources/grammar.pp'));
        }
        /** @var TreeNode $node */
        $node = self::$compiler->parse($declaration, 'expression');
        $parser = new TypeParser($nameContext);
        return $parser->parse($node);
    }
}
