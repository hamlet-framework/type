<?php

namespace Hamlet\Type\Types;

use Hamlet\Type\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function Hamlet\Type\_mixed;

class MixedTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _mixed();
    }

    #[DataProvider('castCases')] public function testMatch(mixed $value): void
    {
        $this->assertTrue(_mixed()->matches($value));
    }

    #[DataProvider('castCases')] public function testCast(mixed $value): void
    {
        _mixed()->cast($value);
        $this->assertTrue(true);
    }
}
