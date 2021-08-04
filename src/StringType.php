<?php declare(strict_types=1);

namespace Hamlet\Cast;

/**
 * @extends Type<string>
 */
class StringType extends Type
{
    /**
     * @param mixed $value
     * @return bool
     * @psalm-assert-if-true string $value
     */
    public function matches($value): bool
    {
        return is_string($value);
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function cast($value): string
    {
        if (is_array($value)) {
            throw new CastException($value, $this);
        }
        if (is_object($value) && !method_exists($value, '__toString')) {
            throw new CastException($value, $this);
        }
        return (string) $value;
    }

    public function __toString(): string
    {
        return 'string';
    }
}
