<?php

namespace Hamlet\Type\Types;

use Error;
use Hamlet\Type\Type;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use function Hamlet\Type\_int;
use function Hamlet\Type\_string;
use function Hamlet\Type\_tuple;

class TupleTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _tuple(_int(), _string());
    }

    protected function baselineCast(mixed $value): array
    {
        try {
            $arrayValue = array_values(is_array($value) ? $value : (array) $value);
            if (count($arrayValue) == 2) {
                return [@(int)$arrayValue[0], @(string)$arrayValue[1]];
            }
        } catch (Error) {
            throw new RuntimeException;
        }
        throw new RuntimeException;
    }
}
