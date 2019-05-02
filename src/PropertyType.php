<?php

namespace Hamlet\Cast;

/**
 * @template N as array-key
 * @template T
 * @extends Type<array{N:T}>
 */
class PropertyType extends Type
{
    /**
     * @var int|string
     * @psalm-var N
     */
    private $name;

    /**
     * @var bool
     */
    private $required;

    /**
     * @var Type
     * @psalm-var Type<T>
     */
    private $type;

    /**
     * @param string $name
     * @psalm-param N $name
     * @param bool $required
     * @param Type $type
     * @psalm-param Type<T> $type
     */
    public function __construct(string $name, bool $required, Type $type)
    {
        $this->name = $name;
        $this->required = $required;
        $this->type = $type;
    }

    /**
     * @return int|string
     * @psalm-return N
     */
    public function name()
    {
        return $this->name;
    }

    public function required(): bool
    {
        return $this->required;
    }

    public function optional(): bool
    {
        return !$this->required;
    }

    /**
     * @return Type
     * @psalm-return Type<T>
     */
    public function type(): Type
    {
        return $this->type;
    }

    public function matches($value): bool
    {
        if (!is_array($value)) {
            return false;
        }
        if (array_key_exists($this->name, $value)) {
            return $this->type->matches($value[$this->name]);
        } else {
            return !$this->required;
        }
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function cast($value)
    {
        if (!is_array($value)) {
            throw new CastException($value, $this);
        }
        if ($this->required && !array_key_exists($this->name, $value)) {
            throw new CastException($value, $this);
        }
        $value[$this->name] = $this->type->cast($value[$this->name]);
        return $value;
    }

    public function __toString()
    {
        return 'array{' . ((string) $this->name) . ($this->required ? '' : '?') . ':' . $this->type . '}';
    }
}
