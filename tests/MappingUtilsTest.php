<?php

namespace Hamlet\Type;

use Hamlet\Type\Resolvers\MappingUtils;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class MappingUtilsTest extends TestCase
{
    public function testCheckMappingOnArrays()
    {
        $a = [
            'a' => 1,
            'b' => 1
        ];

        MappingUtils::checkMapping($a, ['a' => 1, 'b' => 1], _mixed());

        $this->expectException(CastException::class);
        $this->expectExceptionMessage('Property [b] not mapped');
        MappingUtils::checkMapping($a, ['a' => 1], _mixed());
    }

    public function testCheckMappingOnStdClass()
    {
        $a = (object) [
            'a' => 1,
            'b' => 1
        ];

        MappingUtils::checkMapping($a, ['a' => 1, 'b' => 1], _mixed());

        $this->expectException(CastException::class);
        $this->expectExceptionMessage('Property [b] not mapped');
        MappingUtils::checkMapping($a, ['a' => 1], _mixed());
    }

    public function testCheckMappingOnClasses()
    {
        require_once __DIR__ . '/../psalm-cases/classes/User.php';
        require_once __DIR__ . '/../psalm-cases/classes/UserWithHobby.php';
        $a = new UserWithHobby;

        MappingUtils::checkMapping($a, ['id' => 1, 'hobby' => 1, 'address' => 1, 'name' => 1], _mixed());

        $this->expectException(CastException::class);
        $this->expectExceptionMessage('Property [address] not mapped');
        MappingUtils::checkMapping($a, ['id' => 1, 'hobby' => 1, 'name' => 1], _mixed());
    }

    public function testExceptionIsThrownOnInvalidInput()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unexpected value type: boolean');
        MappingUtils::checkMapping(false, [], _mixed());
    }
}
