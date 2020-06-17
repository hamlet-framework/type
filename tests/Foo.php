<?php

namespace Hamlet\Type;

use DateTime as DateTimeAlias;

class Foo
{
    public int $a;
    public ?string $b = 'foo';
    private Parser\TestClass $prop;
    private ?DateTimeAlias $date;
    protected static string $static = 'default';
}
