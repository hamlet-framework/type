<?php declare(strict_types=1);

namespace Hamlet\Cast;

/**
 * @template T as object
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

    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true T $value
     */
    public function matches($value): bool
    {
        return is_object($value) && is_a($value, $this->type);
    }

    /**
     * @param mixed $value
     * @return object
     * @psalm-return T
     */
    public function cast($value)
    {
        if (!$this->matches($value)) {
            throw new CastException($value, $this);
        }
        return $value;
    }

    public function __toString(): string
    {
        return $this->type;
    }
}
