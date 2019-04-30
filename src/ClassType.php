<?php

namespace Hamlet\Cast;

/**
 * @template T
 * @extends Type<T>
 */
class ClassType extends Type
{
    /**
     * @var string
     * @psalm-var class-string<T>
     */
    private $type;

    /**
     * @param string $type
     * @psalm-param class-string<T> $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function matches($value): bool
    {
        return is_object($value) && is_a($value, $this->type);
    }

    public function cast($value)
    {
        if (!$this->matches($value)) {
            throw new CastException($value, $this);
        }
        return $value;
    }

    public function __toString()
    {
        return $this->type;
    }
}
