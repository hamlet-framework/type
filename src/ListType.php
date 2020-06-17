<?php declare(strict_types=1);

namespace Hamlet\Type;

use Hamlet\Type\Resolvers\Resolver;

/**
 * @template T
 * @extends Type<array<T>>
 */
class ListType extends Type
{
    /**
     * @var Type
     * @psalm-var Type<T>
     */
    private $elementType;

    /**
     * @param Type $elementType
     * @psalm-param Type<T> $elementType
     */
    public function __construct(Type $elementType)
    {
        $this->elementType = $elementType;
    }

    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true array<T> $value
     */
    public function matches($value): bool
    {
        if (!is_array($value)) {
            return false;
        }
        /**
         * @psalm-suppress MixedAssignment
         */
        foreach ($value as $v) {
            if (!$this->elementType->matches($v)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param mixed $value
     * @param Resolver $resolver
     * @return array
     * @psalm-return array<T>
     */
    public function resolveAndCast($value, Resolver $resolver): array
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
        return 'array<' . $this->elementType . '>';
    }

    public function serialize(): string
    {
        return 'new ' . static::class . '(' . $this->elementType->serialize() . ')';
    }
}
