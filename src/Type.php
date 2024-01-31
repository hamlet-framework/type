<?php declare(strict_types=1);

namespace Hamlet\Type;

use Hamlet\Type\Parser\DeclarationReader;
use Hamlet\Type\Resolvers\DefaultResolver;
use Hamlet\Type\Resolvers\Resolver;
use PhpParser\NameContext;

/**
 * @template T
 */
abstract readonly class Type
{
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
        return match ($declaration) {
            'string'    => new StringType,
            'int'       => new IntType,
            'float'     => new FloatType,
            'bool'      => new BoolType,
            'mixed'     => new MixedType,
            'resource'  => new ResourceType,
            default     => DeclarationReader::instance()->read($declaration, $nameContext),
        };
    }
}
