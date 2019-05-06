<?php

namespace Hamlet\Cast;

/**
 * @template T as callable
 * @extends Type<T>
 */
class CallableType extends Type
{
    /**
     * @var string
     */
    private $tag;

    /**
     * @var Type|null
     */
    private $returnType;

    /**
     * @var Type[]
     */
    private $argumentTypes;

    /**
     * @param string $tag
     * @param Type|null $returnType
     * @param Type[] $argumentTypes
     */
    public function __construct(string $tag, $returnType, array $argumentTypes)
    {
        $this->tag = $tag;
        $this->returnType = $returnType;
        $this->argumentTypes = $argumentTypes;
    }

    // @todo reflection can actually look into it
    public function matches($value): bool
    {
        return is_callable($value);
    }

    public function cast($value)
    {
        if (!is_callable($value)) {
            throw new CastException($value, $this);
        }
        return $value;
    }

    public function __toString()
    {
        $arguments = [];
        foreach ($this->argumentTypes as $argumentType) {
            if ($argumentType instanceof UnionType || $argumentType instanceof IntersectionType) {
                $arguments[] = '(' . $argumentType . ')';
            } else {
                $arguments[] = (string) $argumentType;
            }
        }
        if ($this->returnType) {
            if ($this->returnType instanceof UnionType || $this->returnType instanceof IntersectionType) {
                $return = ':(' . $this->returnType . ')';
            } else {
                $return = ':' . $this->returnType;
            }
        } else {
            $return = '';
        }

        return $this->tag . '(' . join(',', $arguments) . ')' . $return;
    }
}
