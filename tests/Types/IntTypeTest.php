<?php

namespace Hamlet\Type\Types;

use Hamlet\Type\Type;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use TypeError;
use function Hamlet\Type\_int;

class IntTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _int();
    }

    protected function baselineCast(mixed $value): int
    {
        try {
            return (int) $value;
        } catch (TypeError) {
            throw new RuntimeException;
        }
    }
}