<?php declare(strict_types=1);

namespace Hamlet\Cast;

use Hamlet\Cast\Parser\TypeParser;
use Hamlet\Cast\Resolvers\DefaultResolver;
use Hamlet\Cast\Resolvers\Resolver;
use Hoa\Compiler\Llk\Llk;
use Hoa\Compiler\Llk\Parser;
use PhpParser\NameContext;

/**
 * @template T
 */
abstract class Type
{
    private static ?Parser $compiler = null;

    /**
     * @psalm-assert-if-true T $value
     */
    abstract public function matches(mixed $value): bool;

    /**
     * @return T
     * @psalm-assert T $value
     */
    public function assert(mixed $value): mixed
    {
        assert($this->matches($value), new CastException($value, $this));
        return $value;
    }

    /**
     * @return T
     */
    public function cast(mixed $value): mixed
    {
        $resolver = new DefaultResolver;
        return $this->resolveAndCast($value, $resolver);
    }

    /**
     * @return T
     */
    public function resolveAndCast(mixed $value, Resolver $resolver): mixed
    {
        return $this->cast($value);
    }

    abstract public function __toString(): string;

    public function serialize(): string
    {
        return 'new ' . static::class;
    }

    public static function of(string $declaration, ?NameContext $nameContext = null): Type
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
            self::$compiler = Llk::load(__DIR__ . '/../resources/grammar.pp');
        }
        $node = self::$compiler->parse($declaration, 'expression');
        $parser = new TypeParser($nameContext);
        return $parser->parse($node);
    }
}
