<?php declare(strict_types=1);

namespace Hamlet\Cast;

use Hamlet\Cast\Resolvers\Resolver;

/**
 * @template T
 * @extends Type<list<T>>
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
     * @psalm-assert-if-true list<T> $value
     */
    public function matches($value): bool
    {
        if (!is_array($value)) {
            return false;
        }
        /**
         * @psalm-suppress MixedAssignment
         */
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
