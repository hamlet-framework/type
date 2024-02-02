<?php

namespace Hamlet\Type\Types;

use Hamlet\Type\Type;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use function Hamlet\Type\_numeric;

class NumericTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _numeric();
    }

    protected function baselineCast(mixed $value): float|int|string
    {
        if (is_int($value) || is_float($value) || (is_string($value) && (is_numeric($value)))) {
            return $value;
        } else {
            throw new RuntimeException;
        }
    }
}
