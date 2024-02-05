<?php

namespace Hamlet\Type\Types;

use Error;
use Hamlet\Type\Type;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use function Hamlet\Type\_int;
use function Hamlet\Type\_non_empty_array;

class NonEmptyArrayTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _non_empty_array(_int());
    }

    protected function baselineCast(mixed $value): array
    {
        try {
            $arrayValue = is_array($value) ? $value : (array)$value;
            if (count($arrayValue) == 0) {
                throw new RuntimeException;
            } else {
                $expectedResult = [];
                foreach ($arrayValue as $key => $property) {
                    $expectedResult[$key] = @(int)$property;
                }
                return $expectedResult;
            }
        } catch (Error) {
            throw new RuntimeException;
        }
    }
}
