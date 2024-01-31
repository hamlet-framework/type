<?php declare(strict_types=1);

namespace Hamlet\Type;

/**
 * @template T as callable
 * @extends Type<T>
 *
 * @todo add more logic into return type and argument types
 */
readonly class CallableType extends Type
{
    private string $tag;

    private ?Type $returnType;

    /**
     * @var array<Type>
     */
    private array $argumentTypes;

    /**
     * @param string $tag
     * @param ?Type $returnType
     * @param array<Type> $argumentTypes
     */
    public function __construct(string $tag, ?Type $returnType = null, array $argumentTypes = [])
    {
        $this->tag = $tag;
        $this->returnType = $returnType;
        $this->argumentTypes = $argumentTypes;
    }

    /**
     * @psalm-assert-if-true callable $value
     */
    public function matches(mixed $value): bool
    {
        return is_callable($value);
    }

    /**
     * @return T
     * @todo think about wrapping the value into an asserting closure here and in the assert methods
     * @psalm-suppress InvalidReturnStatement not sure we can do more than that
     * @psalm-suppress InvalidReturnType
     */
    public function cast(mixed $value): callable
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
