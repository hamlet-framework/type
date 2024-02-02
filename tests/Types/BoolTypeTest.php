<?php

namespace Hamlet\Type\Types;

use Hamlet\Type\Type;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use TypeError;
use function Hamlet\Type\_bool;

class BoolTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _bool();
    }

    protected function baselineCast(mixed $value): bool
    {
        try {
            return (bool) $value;
        } catch (TypeError) {
            throw new RuntimeException;
        }
    }
}
