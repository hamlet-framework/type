<?php

namespace Hamlet\Type\Types;

use Error;
use Hamlet\Type\Type;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use function Hamlet\Type\_object;

class ObjectTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _object();
    }

    protected function strictCastResultComparison(): bool
    {
        return false;
    }

    protected function baselineCast(mixed $value): object
    {
        try {
            return (object) $value;
        } catch (Error) {
            throw new RuntimeException;
        }
    }
}