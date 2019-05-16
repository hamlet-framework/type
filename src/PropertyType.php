<?php

namespace Hamlet\Cast;

/**
 * @template N as int|string
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
     * @param int|string $name
     * @psalm-param N $name
     * @param bool $required
     * @param Type $type
     * @psalm-param Type<T> $type
     */
    public function __construct($name, bool $required, Type $type)
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

    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true array<N,T> $value
     */
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
     * @param mixed $value
     * @return array
     * @psalm-return array<N,T>
     * @psalm-suppress MixedTypeCoercion
     * @psalm-suppress LessSpecificImplementedReturnType
     */
    public function cast($value)
    {
        if (!is_array($value)) {
            throw new CastException($value, $this);
        }
        if (array_key_exists($this->name, $value)) {
            $value[$this->name] = $this->type->cast($value[$this->name]);
        } elseif ($this->required) {
            throw new CastException($value, $this);
        }
        return $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $id = ((string) $this->name) . ($this->required ? '' : '?') . ':';
        return 'array{' . $id . $this->type . '}';
    }
}
