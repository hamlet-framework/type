<?php

namespace Hamlet\Type\Types;

use Error;
use Hamlet\Type\Type;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use function Hamlet\Type\_numeric_string;

class NumericStringTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _numeric_string();
    }

    protected function baselineCast(mixed $value): string
    {
        try {
            $stringValue = @(string)$value;
            if (is_numeric($stringValue)) {
                return $stringValue;
            } else {
                throw new RuntimeException;
            }
        } catch (Error) {
            throw new RuntimeException;
        }
    }
}