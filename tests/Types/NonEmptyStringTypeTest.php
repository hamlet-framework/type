<?php

namespace Hamlet\Type\Types;

use Error;
use Hamlet\Type\Type;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use function Hamlet\Type\_non_empty_string;

class NonEmptyStringTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _non_empty_string();
    }

    protected function baselineCast(mixed $value): string
    {
        try {
            $stringValue = @(string)$value;
            if ($stringValue !== '') {
                return $stringValue;
            } else {
                throw new RuntimeException;
            }
        } catch (Error) {
            throw new RuntimeException;
        }
    }
}
