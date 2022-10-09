<?php

# todo the generated file is bad, ugly and inefficient. major potential for improvement

use Hoa\Compiler\Llk\Llk;

require_once __DIR__ . '/../vendor/autoload.php';

$parser = Llk::load(__DIR__ . '/../resources/grammar.pp');
$code = Llk::save($parser, 'HoaParser');

$code = "<?php

namespace Hamlet\Type\Parser;

$code
";

file_put_contents(__DIR__ . '/../src/Parser/HoaParser.php', $code);
