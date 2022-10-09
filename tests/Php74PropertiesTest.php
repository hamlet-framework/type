<?php

namespace Hamlet\Type;

use Hamlet\Type\Parser\DocBlockParser;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class Php74PropertiesTest extends TestCase
{
    public function testTypedProperties()
    {
        if (version_compare(phpversion(), '7.4') < 0) {
            $this->assertTrue(true);
            return;
        }

        require_once __DIR__ . '/../psalm-cases/classes/Foo.php';
        $reflectionClass = new ReflectionClass(Foo::class);

        $typeOfA = DocBlockParser::fromProperty($reflectionClass, $reflectionClass->getProperty('a'));
        $this->assertEquals('int', (string) $typeOfA);

        $typeOfB = DocBlockParser::fromProperty($reflectionClass, $reflectionClass->getProperty('b'));
        $this->assertEquals('string|null', (string) $typeOfB);

        $typeOfProp = DocBlockParser::fromProperty($reflectionClass, $reflectionClass->getProperty('prop'));
        $this->assertEquals('Hamlet\Type\Parser\TestClass', (string) $typeOfProp);

        $typeOfDate = DocBlockParser::fromProperty($reflectionClass, $reflectionClass->getProperty('date'));
        $this->assertEquals('DateTime|null', (string) $typeOfDate);

        $typeOfStatic = DocBlockParser::fromProperty($reflectionClass, $reflectionClass->getProperty('static'));
        $this->assertEquals('string', (string) $typeOfStatic);
    }
}
