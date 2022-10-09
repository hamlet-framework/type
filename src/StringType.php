<?php declare(strict_types=1);

namespace Hamlet\Type;

/**
 * @extends Type<string>
 */
class StringType extends Type
{
    /**
     * @psalm-assert-if-true string $value
     */
    public function matches(mixed $value): bool
    {
        return is_string($value);
    }

    public function cast(mixed $value): string
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
