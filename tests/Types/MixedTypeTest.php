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

    protected function baselineCast(mixed $value): mixed
    {
        return $value;
    }

    #[DataProvider('castCases')] public function testMatch(mixed $value): void
    {
        $this->assertTrue($this->type()->matches($value));
    }
}
