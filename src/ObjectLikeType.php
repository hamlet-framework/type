<?php

namespace Hamlet\Cast;

/**
 * @template T
 * @extends Type<array<T>>
 */
class ObjectLikeType extends Type
{
    /**
     * @var Type[]
     * @psalm-var array<string,Type<T>>
     */
    private $fields;

    /**
     * @param Type[] $fields
     * @psalm-param array<string,Type<T>> $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true array $value
     */
    public function matches($value): bool
    {
        if (!is_array($value)) {
            return false;
        }
        foreach ($this->fields as $field => $type) {
            $tokens = explode('?', $field, 2);
            if (!array_key_exists($tokens[0], $value)) {
                if (count($tokens) == 1) {
                    return false;
                } else {
                    continue;
                }
            }
            if (!$type->matches($value[$tokens[0]])) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param mixed $value
     * @return array
     * @psalm-return array<T>
     */
    public function cast($value): array
    {
        if (!is_array($value)) {
            throw new CastException($value, $this);
        }
        foreach ($this->fields as $field => $type) {
            $tokens = explode('?', $field, 2);
            if (!array_key_exists($tokens[0], $value)) {
                if (count($tokens) == 1) {
                    throw new CastException($value, $this);
                } else {
                    continue;
                }
            }
            $value[$tokens[0]] = $type->cast($value[$tokens[0]]);
        }
        return $value;
    }

    public function __toString(): string
    {
        $keys = [];
        foreach ($this->fields as $name => $type) {
            $keys[] = $name . ':' . $type;
        }
        return 'array{' . join(',', $keys) . '}';
    }
}
