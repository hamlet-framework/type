<?php declare(strict_types=1);

namespace Hamlet\Type\Types;

use Hamlet\Type\CastException;
use Hamlet\Type\Resolvers\Resolver;
use Hamlet\Type\Type;
use Override;

/**
 * @psalm-internal Hamlet\Type
 * @template T
 * @extends Type<list<T>>
 */
readonly class ListType extends Type
{
    /**
     * @var Type<T>
     */
    private Type $elementType;

    /**
     * @param Type<T> $elementType
     */
    public function __construct(Type $elementType)
    {
        $this->elementType = $elementType;
    }

    #[Override] public function matches(mixed $value): bool
    {
        if (!is_array($value) || !array_is_list($value)) {
            return false;
        }
        foreach ($value as $element) {
            if (!$this->elementType->matches($element)) {
                return false;
            }
        }
        return true;
    }

    #[Override] public function resolveAndCast(mixed $value, Resolver $resolver): array
    {
        if ($this->matches($value)) {
            return $value;
        }
        if (!is_array($value) || !array_is_list($value)) {
            throw new CastException($value, $this);
        }
        $result = [];
        foreach ($value as $v) {
            $result[] = $this->elementType->resolveAndCast($v, $resolver);
        }
        return $result;
    }

    #[Override] public function __toString(): string
    {
        return 'list<' . $this->elementType . '>';
    }

    #[Override] public function serialize(): string
    {
        return 'new ' . static::class . '(' . $this->elementType->serialize() . ')';
    }
}