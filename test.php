<?php

use Hamlet\Type\Parser\DeclarationReader;

require __DIR__ . '/vendor/autoload.php';

$type = DeclarationReader::instance()->read('array<int,array<true|1|0.4>|false>');

print_r($type->serialize());