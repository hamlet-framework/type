<?php

namespace Hamlet\Type;

use Hamlet\Type\Parser\DocBlockParser;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

if (version_compare(PHP_VERSION, '7.4.0') >= 0) {

    class Foo
    {
        public int $a;
        public ?string $b = 'foo';
        private Foo $prop;
        protected static string $static = 'default';
    }

    class Php74PropertiesTest extends TestCase
    {
        public function testTypedProperties()
        {
            $reflectionClass = new ReflectionClass(Foo::class);

            $typeOfA = DocBlockParser::fromProperty($reflectionClass->getProperty('a'));
            $this->assertEquals('int', (string) $typeOfA);

            $typeOfB = DocBlockParser::fromProperty($reflectionClass->getProperty('b'));
            $this->assertEquals('string|null', (string) $typeOfB);

            $typeOfProp = DocBlockParser::fromProperty($reflectionClass->getProperty('prop'));
            $this->assertEquals('Hamlet\Type\Foo', (string) $typeOfProp);

            $typeOfStatic = DocBlockParser::fromProperty($reflectionClass->getProperty('static'));
            $this->assertEquals('string', (string) $typeOfStatic);
        }
    }
}
