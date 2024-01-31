<?php declare(strict_types=1);

namespace Hamlet\Type;

use Hamlet\Type\Resolvers\Resolver;

/**
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

    /**
     * @psalm-assert-if-true list<T> $value
     */
    public function matches(mixed $value): bool
    {
        if (!is_array($value)) {
            return false;
        }
        $i = 0;
        foreach ($value as $k => $v) {
            if ($i !== $k) {
                return false;
            }
            if (!$this->elementType->matches($v)) {
                return false;
            }
            $i++;
        }
        return true;
    }

    /**
     * @return list<T>
     */
    public function resolveAndCast(mixed $value, Resolver $resolver): array
    {
        if (!is_array($value)) {
            throw new CastException($value, $this);
        }
        $result = [];
        /**
         * @psalm-suppress MixedAssignment
         */
        foreach ($value as $v) {
            $result[] = $this->elementType->resolveAndCast($v, $resolver);
        }
        return $result;
    }

    public function __toString(): string
    {
        return 'list<' . $this->elementType . '>';
    }

    public function serialize(): string
    {
        return 'new ' . static::class . '(' . $this->elementType->serialize() . ')';
    }
}
