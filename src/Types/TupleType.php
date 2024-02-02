<?php declare(strict_types=1);

namespace Hamlet\Type\Types;

use Hamlet\Type\CastException;
use Hamlet\Type\Resolvers\Resolver;
use Hamlet\Type\Type;
use Override;

/**
 * @psalm-internal Hamlet\Type
 * @template A
 * @extends Type<A>
 */
readonly class TupleType extends Type
{
    /**
     * @var list<Type<A>>
     */
    private array $fields;

    /**
     * @param list<Type<A>> $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    #[Override] public function matches(mixed $value): bool
    {
        if (!is_array($value) || count($value) != count($this->fields)) {
            return false;
        }
        for ($i = 0; $i < count($this->fields); $i++) {
            if (!array_key_exists($i, $value)) {
                return false;
            }
            $v = $value[$i];
            if (!$this->fields[$i]->matches($v)) {
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
        if (!is_array($value) || count($value) != count($this->fields)) {
            throw new CastException($value, $this);
        }
        $result = [];
        foreach ($this->fields as $i => $field) {
            $result[] = $field->resolveAndCast($value[$i], $resolver);
        }
        return $result;
    }

    #[Override] public function serialize(): string
    {
        return 'new ' . static::class . '([' . join(', ', array_map(fn (Type $a) => $a->serialize(), $this->fields)) . '])';
    }

    #[Override] public function __toString(): string
    {
        return 'list{' . join(',', $this->fields) . '}';
    }
}
