<?php

namespace Hamlet\Type\Types;

use Error;
use Hamlet\Type\Type;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use function Hamlet\Type\_string;

class StringTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _string();
    }

    protected function baselineCast(mixed $value): string
    {
        try {
            return @(string)$value;
        } catch (Error) {
            throw new RuntimeException;
        }
    }
}