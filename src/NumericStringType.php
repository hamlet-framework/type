<?php declare(strict_types=1);

namespace Hamlet\Cast;

/**
 * @extends Type<numeric-string>
 */
class NumericStringType extends Type
{
    public function matches(mixed $value): bool
    {
        return is_string($value) && is_numeric($value);
    }

    /**
     * @return numeric-string
     */
    public function cast(mixed $value): string
    {
        if (is_array($value)) {
            throw new CastException($value, $this);
        }
        if ($value === true) {
            return '1';
        } elseif ($value === false || $value === null) {
            return '0';
        } else {
            if (is_object($value) && !method_exists($value, '__toString')) {
                throw new CastException($value, $this);
            }
            $string = (string) $value;
        }
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
