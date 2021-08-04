<?php declare(strict_types=1);

namespace Hamlet\Cast;

/**
 * @template T as callable
 * @extends Type<T>
 *
 * @todo add more logic into return type and argument types
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
    public function __construct(string $tag, $returnType = null, array $argumentTypes = [])
    {
        $this->tag = $tag;
        $this->returnType = $returnType;
        $this->argumentTypes = $argumentTypes;
    }

    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true callable $value
     */
    public function matches($value): bool
    {
        return is_callable($value);
    }

    /**
     * @param mixed $value
     * @return callable
     * @psalm-return T
     * @psalm-suppress InvalidReturnStatement not sure we can do more than that
     * @psalm-suppress InvalidReturnType
     */
    public function cast($value)
    {
        if (!is_callable($value)) {
            throw new CastException($value, $this);
        }
        return $value;
    }

    public function __toString(): string
    {
        $arguments = [];
        foreach ($this->argumentTypes as $argumentType) {
            if ($argumentType instanceof Union2Type) {
                $arguments[] = '(' . $argumentType . ')';
            } else {
                $arguments[] = (string) $argumentType;
            }
        }
        if ($this->returnType) {
            if ($this->returnType instanceof Union2Type) {
                $return = ':(' . $this->returnType . ')';
            } else {
                $return = ':' . $this->returnType;
            }
        } else {
            $return = '';
        }

        return $this->tag . '(' . join(',', $arguments) . ')' . $return;
    }

    public function serialize(): string
    {
        $line = var_export($this->tag, true);
        if ($this->returnType) {
            $line .= ', ' . $this->returnType->serialize();
            if ($this->argumentTypes) {
                $arguments = [];
                foreach ($this->argumentTypes as $argumentType) {
                    $arguments[] = $argumentType->serialize();
                }
                $line .= ', [' . join(', ', $arguments) . ']';
            }
        }
        return 'new ' . static::class . '(' . $line . ')';
    }
}
