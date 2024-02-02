<?php

namespace Hamlet\Type\Types;

use Hamlet\Type\Type;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use function Hamlet\Type\_scalar;

class ScalarTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _scalar();
    }

    protected function baselineCast(mixed $value): string|int|bool|float
    {
        if (is_scalar($value)) {
            return $value;
        } else {
            throw new RuntimeException;
        }
    }
}
