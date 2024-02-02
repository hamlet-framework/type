<?php

use function Hamlet\Type\_class;
use function Hamlet\Type\_int;
use function Hamlet\Type\_string;
use function Hamlet\Type\_tuple;
use function Hamlet\Type\_union;

require __DIR__ . '/vendor/autoload.php';

# $type = DeclarationReader::instance()->read('array<int,array<true|1|0.4>|false>');

$u = _union(_int(), _string());

echo $u, PHP_EOL;
echo $u->serialize(), PHP_EOL;

$t = _tuple(_int(), _string(), _class(DateTime::class));

echo $t, PHP_EOL;
echo $t->serialize(), PHP_EOL;