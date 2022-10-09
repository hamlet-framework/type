<?php

namespace Hamlet\Type\Types;

use DateTime;
use Exception;
use Hamlet\Type\CastException;
use Hamlet\Type\Union2Type;
use Hamlet\Type\Union3Type;
use Hamlet\Type\Union4Type;
use Hamlet\Type\Union5Type;
use Hamlet\Type\Union6Type;
use Hamlet\Type\Union7Type;
use Hamlet\Type\Union8Type;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use function Hamlet\Type\_bool;
use function Hamlet\Type\_callable;
use function Hamlet\Type\_float;
use function Hamlet\Type\_int;
use function Hamlet\Type\_literal;
use function Hamlet\Type\_null;
use function Hamlet\Type\_resource;
use function Hamlet\Type\_string;
use function Hamlet\Type\_union;

class UnionTypeTest extends TestCase
{
    public function matchCases()
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

    /**
     * @dataProvider matchCases
     * @param mixed $value
     * @param bool $success1
     * @param bool $success2
     * @param bool $success3
     */
    public function testMatch($value, bool $success1, bool $success2, bool $success3)
    {
        $type1 = _union(_null(), _int());
        $this->assertEquals($success1, $type1->matches($value));

        $type2 = _union(_null(), _int(), _string());
        $this->assertEquals($success2, $type2->matches($value));

        $type3 = _union(_null(), _int(), _string(), _bool());
        $this->assertEquals($success3, $type3->matches($value));
    }

    /**
     * @dataProvider matchCases()
     * @param mixed $value
     * @param bool $success
     */
    public function testAssert($value, bool $success)
    {
        $exceptionThrown = false;
        try {
            _union(_int(), _null())->assert($value);
        } catch (Exception $error) {
            $exceptionThrown = true;
        }
        $this->assertEquals(!$success, $exceptionThrown);
    }

    public function castCases()
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
            [true,          null,       true ],
            [false,         null,       false],
            [0,             null,       false],
            [1,             null,       true ],
            [-1,            null,       true ],
            ['',            null,       false],
            ['0',           null,       true ],
            ['x1',          null,       true ],
            [[],            null,       false],
            [[false],       null,       true ],
            [[1],           null,       true ],
            [[1, 3],        null,       true ],
            [new stdClass,  null,       true ],
            [$object,       null,       true ],
            [new DateTime,  null,       true ],
            ['abs',         null,       true ],
            [$callable,     null,       true ],
            [$invokable,    null,       true ],
            [$resource,     $resource,  false],
            [null,          null,       false],
        ];
    }

    /**
     * @dataProvider castCases
     * @param mixed $value
     * @param mixed $result
     * @param bool $exceptionThrown
     */
    public function testCast($value, $result, bool $exceptionThrown)
    {
        $type = _union(_resource(), _null());
        if ($exceptionThrown) {
            $this->expectException(CastException::class);
            $type->cast($value);
        } else {
            $this->assertSame($result, $type->cast($value));
        }
    }

    public function testFactoryMethod() {
        $type2 = _union(_literal(1), _literal(2));
        $this->assertInstanceOf(Union2Type::class, $type2);
        $this->assertTrue($type2->matches(2));

        $type3 = _union(_literal(1), _literal(2), _literal(3));
        $this->assertInstanceOf(Union3Type::class, $type3);
        $this->assertTrue($type3->matches(3));

        $type4 = _union(_literal(1), _literal(2), _literal(3), _literal(4));
        $this->assertInstanceOf(Union4Type::class, $type4);
        $this->assertTrue($type4->matches(4));

        $type5 = _union(_literal(1), _literal(2), _literal(3), _literal(4), _literal(5));
        $this->assertInstanceOf(Union5Type::class, $type5);
        $this->assertTrue($type5->matches(5));

        $type6 = _union(_literal(1), _literal(2), _literal(3), _literal(4), _literal(5), _literal(6));
        $this->assertInstanceOf(Union6Type::class, $type6);
        $this->assertTrue($type6->matches(6));

        $type7 = _union(_literal(1), _literal(2), _literal(3), _literal(4), _literal(5), _literal(6), _literal(7));
        $this->assertInstanceOf(Union7Type::class, $type7);
        $this->assertTrue($type7->matches(7));

        $type8 = _union(_literal(1), _literal(2), _literal(3), _literal(4), _literal(5), _literal(6), _literal(7), _literal(8));
        $this->assertInstanceOf(Union8Type::class, $type8);
        $this->assertTrue($type8->matches(8));

        $this->expectException(RuntimeException::class);
        _union(_literal(1), _literal(2), _literal(3), _literal(4), _literal(5), _literal(6), _literal(7), _literal(8), _literal(9));
    }

    public function testNullableTailsFail3()
    {
        $this->expectException(RuntimeException::class);
        _union(_literal(1), _literal(2), null);
    }

    public function testNullableTailsFail4()
    {
        $this->expectException(RuntimeException::class);
        _union(_literal(1), _literal(2), _literal(3), null);
    }

    public function testNullableTailsFail5()
    {
        $this->expectException(RuntimeException::class);
        _union(_literal(1), _literal(2), _literal(3), _literal(4), null);
    }

    public function testNullableTailsFail6()
    {
        $this->expectException(RuntimeException::class);
        _union(_literal(1), _literal(2), _literal(3), _literal(4), _literal(5), null);
    }

    public function testNullableTailsFail7()
    {
        $this->expectException(RuntimeException::class);
        _union(_literal(1), _literal(2), _literal(3), _literal(4), _literal(5), _literal(6), null);
    }

    public function testNullableTailsFail8()
    {
        $this->expectException(RuntimeException::class);
        _union(_literal(1), _literal(2), _literal(3), _literal(4), _literal(5), _literal(6), _literal(7), null);
    }
}
