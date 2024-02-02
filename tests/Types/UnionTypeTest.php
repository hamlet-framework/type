<?php

namespace Hamlet\Type\Types;

use DateTime;
use Exception;
use Hamlet\Type\CastException;
use Hamlet\Type\Type;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use function Hamlet\Type\_bool;
use function Hamlet\Type\_int;
use function Hamlet\Type\_literal;
use function Hamlet\Type\_null;
use function Hamlet\Type\_resource;
use function Hamlet\Type\_string;
use function Hamlet\Type\_union;

class UnionTypeTest extends TestCase
{
    use CastCasesTrait;

    protected function type(): Type
    {
        return _union(_int(), _resource(), _null());
    }

    public static function matchCases(): array
    {
        $resource = fopen(__FILE__, 'r');
        $object = new class ()
        {
            public function __toString()
            {
                return 'a';
            }
        };
        $callable = function () {
        };
        $invokable = new class()
        {
            public function __invoke()
            {
            }
        };

        return [
            [true,          false,  false,  true ],
            [false,         false,  false,  true ],
            [0,             true,   true,   true ],
            [1,             true,   true,   true ],
            [-1,            true,   true,   true ],
            ['',            false,  true,   true ],
            ['0',           false,  true,   true ],
            ['abc',         false,  true,   true ],
            ['strtoupper',  false,  true,   true ],
            [[],            false,  false,  false],
            [[1],           false,  false,  false],
            [new stdClass,  false,  false,  false],
            [$object,       false,  false,  false],
            [new DateTime,  false,  false,  false],
            [$callable,     false,  false,  false],
            [$invokable,    false,  false,  false],
            [$resource,     false,  false,  false],
            [null,          true,   true,   true ],
        ];
    }

    #[DataProvider('matchCases')] public function testMatch(mixed $value, bool $success1, bool $success2, bool $success3): void
    {
        $type1 = _union(_null(), _int());
        $this->assertEquals($success1, $type1->matches($value));

        $type2 = _union(_null(), _int(), _string());
        $this->assertEquals($success2, $type2->matches($value));

        $type3 = _union(_null(), _int(), _string(), _bool());
        $this->assertEquals($success3, $type3->matches($value));
    }

    #[DataProvider('matchCases')] public function testAssert(mixed $value, bool $expectedSuccess): void
    {
        $exceptionThrown = false;
        try {
            _union(_int(), _null())->assert($value);
        } catch (Exception) {
            $exceptionThrown = true;
        }
        $this->assertEquals(!$expectedSuccess, $exceptionThrown);
    }

    #[DataProvider('castCases')] public function testCast(mixed $value): void
    {
        $expectedExceptionThrown = !is_int($value) && !is_resource($value) && !is_null($value);

        try {
            _union(_int(), _resource(), _null())->cast($value);
            $this->assertFalse($expectedExceptionThrown, 'Expected exception not thrown');
        } catch (CastException) {
            $this->assertTrue($expectedExceptionThrown, "Thrown an excessive exception");
        }
    }

    public function testFactoryMethod(): void
    {
        $type2 = _union(_literal(1), _literal(2));
        $this->assertTrue($type2->matches(2));

        $type3 = _union(_literal(1), _literal(2), _literal(3));
        $this->assertTrue($type3->matches(3));

        $type4 = _union(_literal(1), _literal(2), _literal(3), _literal(4));
        $this->assertTrue($type4->matches(4));

        $type5 = _union(_literal(1), _literal(2), _literal(3), _literal(4), _literal(5));
        $this->assertTrue($type5->matches(5));

        $type6 = _union(_literal(1), _literal(2), _literal(3), _literal(4), _literal(5), _literal(6));
        $this->assertTrue($type6->matches(6));

        $type7 = _union(_literal(1), _literal(2), _literal(3), _literal(4), _literal(5), _literal(6), _literal(7));
        $this->assertTrue($type7->matches(7));

        $type8 = _union(_literal(1), _literal(2), _literal(3), _literal(4), _literal(5), _literal(6), _literal(7), _literal(8));
        $this->assertTrue($type8->matches(8));

        $this->expectException(InvalidArgumentException::class);
        _union(_literal(1), _literal(2), _literal(3), _literal(4), _literal(5), _literal(6), _literal(7), _literal(8), _literal(9));
    }

    public function testNullableTailsFail3(): void
    {
        $this->expectException(InvalidArgumentException::class);
        _union(_literal(1), _literal(2), null);
    }

    public function testNullableTailsFail4(): void
    {
        $this->expectException(InvalidArgumentException::class);
        _union(_literal(1), _literal(2), _literal(3), null);
    }

    public function testNullableTailsFail5(): void
    {
        $this->expectException(InvalidArgumentException::class);
        _union(_literal(1), _literal(2), _literal(3), _literal(4), null);
    }

    public function testNullableTailsFail6(): void
    {
        $this->expectException(InvalidArgumentException::class);
        _union(_literal(1), _literal(2), _literal(3), _literal(4), _literal(5), null);
    }

    public function testNullableTailsFail7(): void
    {
        $this->expectException(InvalidArgumentException::class);
        _union(_literal(1), _literal(2), _literal(3), _literal(4), _literal(5), _literal(6), null);
    }

    public function testNullableTailsFail8(): void
    {
        $this->expectException(InvalidArgumentException::class);
        _union(_literal(1), _literal(2), _literal(3), _literal(4), _literal(5), _literal(6), _literal(7), null);
    }
}
