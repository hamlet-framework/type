<?php

require_once __DIR__ . '/../vendor/autoload.php';

use function Hamlet\Cast\_int;
use function Hamlet\Cast\_map;
use function Hamlet\Cast\_mixed;
use function Hamlet\Cast\_string;

/**
 * @param array<string,mixed> $a
 * @return void
 */
function f(array $a) {}

/**
 * @param array<int,mixed> $a
 * @return void
 */
function g(array $a) {}

/**
 * @var array<array-key,mixed> $b
 */
$b = [];
f(_map(_string(), _mixed())->cast($b));
g(_map(_int(), _mixed())->cast($b));
