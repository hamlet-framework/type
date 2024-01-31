<?php declare(strict_types=1);

namespace Hamlet\Type;

/**
 * @template T
 * @extends Type<T>
 */
readonly class LiteralType extends Type
{
    /**
     * @var array<T>
     */
    private array $values;

    /**
     * @param T ...$values
     */
    public function __construct(mixed ...$values)
    {
        $this->values = $values;
    }

    /**
     * @psalm-assert-if-true T $value
     */
    public function matches(mixed $value): bool
    {
        foreach ($this->values as $v) {
            if ($value === $v) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return T
     */
    public function cast(mixed $value): mixed
    {
        if ($this->matches($value)) {
            return $value;
        }
        foreach ($this->values as $v) {
            if (is_scalar($value) && $v == $value || $v === $value) {
                return $v;
            }
        }
        throw new CastException($value, $this);
    }

    public function __toString(): string
    {
        $escape =
            function (mixed $a): string {
                if (is_string($a)) {
                    return "'$a'";
                }
                if (is_null($a)) {
                    return 'null';
                }
                if (is_bool($a)) {
                    return $a ? 'true' : 'false';
                }
                return (string) $a;
            };

        if (count($this->values) > 1) {
            return '(' . join('|', array_map($escape, $this->values)) . ')';
        } else {
            return $escape($this->values[0]);
        }
    }

    public function serialize(): string
    {
        $properties = [];
        foreach ($this->values as $value) {
            $properties[] = var_export($value, true);
        }
        return 'new ' . static::class . '(' . join(', ', $properties) . ')';
    }
}
