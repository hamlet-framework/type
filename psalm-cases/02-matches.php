<?php

use function Hamlet\Type\_class;
use function Hamlet\Type\_int;
use function Hamlet\Type\_null;
use function Hamlet\Type\_tuple;
use function Hamlet\Type\_union;

$date = _class(DateTime::class)->cast($GLOBALS['a']);
if ($date->getOffset() > 0) {
    echo 'positive offset', PHP_EOL;
}

$union = _union(_class(DateTime::class), _int(), _null())->cast($GLOBALS['b']);
if (is_null($union)) {
    echo 'null', PHP_EOL;
} elseif (is_int($union)) {
    echo 'integer', PHP_EOL;
} else {
    echo $union->format('Y-m-d'), PHP_EOL;
}

$tuple = _tuple(_int(), _class(DateTime::class))->cast($GLOBALS['c']);
if ($tuple[0] > 0) {
    echo 'positive integer', PHP_EOL;
}
if ($tuple[1]->getOffset() > 0) {
    echo 'positive offset', PHP_EOL;
}
