<?php

namespace Hamlet\Type;

class Foo
{
    public int $a;
    public ?string $b = 'foo';
    private Foo $prop;
    protected static string $static = 'default';
}
