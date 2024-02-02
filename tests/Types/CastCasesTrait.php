<?php

namespace Hamlet\Type\Types;

use DateTime;
use Hamlet\Type\CastException;
use Hamlet\Type\Type;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;

trait CastCasesTrait
{
    abstract protected function type(): Type;

    #[DataProvider('castCases')] public function testCastMatchInvariant(mixed $value): void
    {
        $type = $this->type();
        if ($type->matches($value)) {
            $this->assertSame($value, $type->cast($value));
        } else {
            try {
                $this->assertNotSame($value, $type->cast($value));
            } catch (CastException) {
                $this->assertTrue(true);
            }
        }
    }

    #[DataProvider('castCases')] public function testMatchCastInvariant(mixed $value): void
    {
        $type = $this->type();
        try {
            $castValue = $type->cast($value);
            $this->assertTrue($type->matches($castValue));
        } catch (CastException) {
            $this->assertFalse($type->matches($value));
        }
    }

    public static function castCases(): array
    {
        $resource = fopen(__FILE__, 'r');
        $stringableObject = new class ()
        {
            public function __toString()
            {
                return 'a';
            }
        };
        $object = new class()
        {
        };
        $callable = function () {
        };
        $intGenerator = function () {
            yield 1;
        };
        $stringGenerator = function() {
            yield "hello";
        };

        return [
            [true],
            [false],
            [0],
            [1],
            [-1],
            [''],
            ['0'],
            ['1'],
            ['1.22'],
            [7.5],
            ['1apple'],
            ['string'],
            [[]],
            [[1]],
            [[1, 3]],
            [new stdClass],
            [$object],
            [$stringableObject],
            [new DateTime],
            [$callable],
            [$resource],
            [null],
            [$intGenerator()],
            [$stringGenerator()],
        ];
    }
}
