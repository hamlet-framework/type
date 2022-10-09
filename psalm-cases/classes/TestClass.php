<?php

namespace Hamlet\Type\Parser;

use DateTime as AliasDateTime;
use Hamlet\Type\CastException;

class TestClass
{
    /**
     * @var array
     * @psalm-var array<int,array{0:AliasDateTime}[]>
     */
    private $a;

    /**
     * @var string|object|null
     * @psalm-var 'x'|'y'|'z'|CastException|\DateTime|null
     */
    private $b;
}
