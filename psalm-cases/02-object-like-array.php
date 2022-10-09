<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Hamlet\Type\Type;

/**
 * @param array{x:string,y:int,z:('a'|'b')} $a
 * @return void
 */
function f(array $a) {}

$b = [];

/** @var Type<array{x:string,y:int,z:('a'|'b')}> $type */
$type = Type::of("array{x:string,y:int,z:('a'|'b')}");

f($type->cast($b));
