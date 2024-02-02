<?php

namespace Hamlet\Type\Types;

use Hamlet\Type\Type;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use TypeError;
use function Hamlet\Type\_float;

class FloatTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _float();
    }

    protected function baselineCast(mixed $value): mixed
    {
        try {
            return (float) $value;
        } catch (TypeError) {
            throw new RuntimeException;
        }
    }
}
