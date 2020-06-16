<?php declare(strict_types=1);

namespace Hamlet\Type;

/**
 * @extends Type<numeric-string>
 */
class NumericStringType extends Type
{
    public function matches($value): bool
    {
        return is_string($value) && is_numeric($value);
    }

    /**
     * @param mixed $value
     * @return string
     * @psalm-return numeric-string
     */
    public function cast($value): string
    {
        if (is_array($value)) {
            throw new CastException($value, $this);
        }
        if (is_object($value) && !method_exists($value, '__toString')) {
            throw new CastException($value, $this);
        }
        $string = (string) $value;
        if (!is_numeric($string)) {
            throw new CastException($value, $this);
        }
        return $string;
    }

    public function __toString(): string
    {
        return 'numeric-string';
    }
}
