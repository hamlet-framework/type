<?php

namespace Hamlet\Type\Parser;

class HoaParser extends \Hoa\Compiler\Llk\Parser
{
    public function __construct()
    {
        parent::__construct(
            [
                'default' => [
                    'skip' => '\s',
                    'built_in' => '(integer|int|string|float|boolean|bool|object|iterable|mixed|numeric-string|double|real|resource|self|static|scalar|numeric|array-key|class-string)',
                    'array' => '(array|non-empty-array|list|non-empty-list)',
                    'callable' => '(callable|Closure)',
                    'false' => 'false',
                    'true' => 'true',
                    'null' => 'null',
                    'quote_:string' => '\'',
                    'parenthesis_' => '\(',
                    '_parenthesis' => '\)',
                    'brace_' => '\{',
                    '_brace' => '\}',
                    'bracket_' => '\[',
                    '_bracket' => '\]',
                    'angular_' => '<',
                    '_angular' => '>',
                    'question' => '\?',
                    'namespace' => '::',
                    'colon' => ':',
                    'comma' => ',',
                    'or' => '\|',
                    'and' => '&',
                    'float_number' => '(\d+)?\.\d+',
                    'int_number' => '\d+',
                    'id' => '[a-zA-Z_][a-zA-Z0-9_]*',
                    'backslash' => '\\\\',
                    'word' => '[^\\s]+',
                ],
                'string' => [
                    'string' => '[^\']+',
                    '_quote:default' => '\'',
                ],
            ],
            [
                0 => new \Hoa\Compiler\Llk\Rules\TokenRule(0, 'word', null, -1, false),
                1 => new \Hoa\Compiler\Llk\Rules\RepetitionRule(1, 0, -1, 0, null),
                'expression' => new \Hoa\Compiler\Llk\Rules\ConcatenationRule('expression', ['type', 1], null),
                'type' => new \Hoa\Compiler\Llk\Rules\ChoiceRule('type', ['basic_type', 'derived_type'], null),
                'derived_type' => new \Hoa\Compiler\Llk\Rules\ChoiceRule('derived_type', ['union', 'intersection'], null),
                5 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(5, ['escaped_type'], '#basic_type'),
                6 => new \Hoa\Compiler\Llk\Rules\TokenRule(6, 'built_in', null, -1, true),
                7 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(7, [6], '#basic_type'),
                8 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(8, ['literal'], '#basic_type'),
                9 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(9, ['object_like_array'], '#basic_type'),
                10 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(10, ['array'], '#basic_type'),
                11 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(11, ['generic'], '#basic_type'),
                12 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(12, ['callable'], '#basic_type'),
                13 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(13, ['class_name'], '#basic_type'),
                14 => new \Hoa\Compiler\Llk\Rules\ChoiceRule(14, [5, 7, 8, 9, 10, 11, 12, 13], null),
                15 => new \Hoa\Compiler\Llk\Rules\RepetitionRule(15, 0, -1, 'brackets', null),
                'basic_type' => new \Hoa\Compiler\Llk\Rules\ConcatenationRule('basic_type', [14, 15], null),
                17 => new \Hoa\Compiler\Llk\Rules\TokenRule(17, 'quote_', null, -1, false),
                18 => new \Hoa\Compiler\Llk\Rules\TokenRule(18, 'string', null, -1, true),
                19 => new \Hoa\Compiler\Llk\Rules\TokenRule(19, '_quote', null, -1, false),
                'quoted_string' => new \Hoa\Compiler\Llk\Rules\ConcatenationRule('quoted_string', [17, 18, 19], null),
                21 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(21, ['quoted_string'], '#literal'),
                22 => new \Hoa\Compiler\Llk\Rules\TokenRule(22, 'int_number', null, -1, true),
                23 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(23, [22], '#literal'),
                24 => new \Hoa\Compiler\Llk\Rules\TokenRule(24, 'float_number', null, -1, true),
                25 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(25, [24], '#literal'),
                26 => new \Hoa\Compiler\Llk\Rules\TokenRule(26, 'true', null, -1, true),
                27 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(27, [26], '#literal'),
                28 => new \Hoa\Compiler\Llk\Rules\TokenRule(28, 'false', null, -1, true),
                29 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(29, [28], '#literal'),
                30 => new \Hoa\Compiler\Llk\Rules\TokenRule(30, 'null', null, -1, true),
                31 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(31, [30], '#literal'),
                32 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(32, ['const'], '#literal'),
                'literal' => new \Hoa\Compiler\Llk\Rules\ChoiceRule('literal', [21, 23, 25, 27, 29, 31, 32], null),
                34 => new \Hoa\Compiler\Llk\Rules\TokenRule(34, 'namespace', null, -1, false),
                35 => new \Hoa\Compiler\Llk\Rules\TokenRule(35, 'id', null, -1, true),
                'const' => new \Hoa\Compiler\Llk\Rules\ConcatenationRule('const', ['class_name', 34, 35], '#const'),
                37 => new \Hoa\Compiler\Llk\Rules\TokenRule(37, 'array', null, -1, true),
                38 => new \Hoa\Compiler\Llk\Rules\TokenRule(38, 'angular_', null, -1, false),
                39 => new \Hoa\Compiler\Llk\Rules\TokenRule(39, 'comma', null, -1, false),
                40 => new \Hoa\Compiler\Llk\Rules\TokenRule(40, '_angular', null, -1, false),
                41 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(41, [37, 38, 'type', 39, 'type', 40], '#array'),
                42 => new \Hoa\Compiler\Llk\Rules\TokenRule(42, 'array', null, -1, true),
                43 => new \Hoa\Compiler\Llk\Rules\TokenRule(43, 'angular_', null, -1, false),
                44 => new \Hoa\Compiler\Llk\Rules\TokenRule(44, '_angular', null, -1, false),
                45 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(45, [42, 43, 'type', 44], '#array'),
                46 => new \Hoa\Compiler\Llk\Rules\TokenRule(46, 'array', null, -1, true),
                47 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(47, [46], '#array'),
                'array' => new \Hoa\Compiler\Llk\Rules\ChoiceRule('array', [41, 45, 47], null),
                49 => new \Hoa\Compiler\Llk\Rules\TokenRule(49, 'array', null, -1, true),
                50 => new \Hoa\Compiler\Llk\Rules\TokenRule(50, 'brace_', null, -1, false),
                51 => new \Hoa\Compiler\Llk\Rules\TokenRule(51, 'comma', null, -1, false),
                52 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(52, [51, 'property'], '#object_like_array'),
                53 => new \Hoa\Compiler\Llk\Rules\RepetitionRule(53, 0, -1, 52, null),
                54 => new \Hoa\Compiler\Llk\Rules\TokenRule(54, '_brace', null, -1, false),
                'object_like_array' => new \Hoa\Compiler\Llk\Rules\ConcatenationRule('object_like_array', [49, 50, 'property', 53, 54], null),
                56 => new \Hoa\Compiler\Llk\Rules\TokenRule(56, 'id', null, -1, true),
                57 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(57, [56], '#property_name'),
                58 => new \Hoa\Compiler\Llk\Rules\TokenRule(58, 'int_number', null, -1, true),
                59 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(59, [58], '#property_name'),
                60 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(60, ['quoted_string'], '#property_name'),
                'property_name' => new \Hoa\Compiler\Llk\Rules\ChoiceRule('property_name', [57, 59, 60], null),
                62 => new \Hoa\Compiler\Llk\Rules\RepetitionRule(62, 0, 1, 'property_option', null),
                63 => new \Hoa\Compiler\Llk\Rules\TokenRule(63, 'colon', null, -1, false),
                64 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(64, ['property_name', 62, 63, 'type'], '#property'),
                65 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(65, ['type'], '#property'),
                'property' => new \Hoa\Compiler\Llk\Rules\ChoiceRule('property', [64, 65], null),
                67 => new \Hoa\Compiler\Llk\Rules\TokenRule(67, 'question', null, -1, false),
                'property_option' => new \Hoa\Compiler\Llk\Rules\ConcatenationRule('property_option', [67], '#property_option'),
                69 => new \Hoa\Compiler\Llk\Rules\TokenRule(69, 'parenthesis_', null, -1, false),
                70 => new \Hoa\Compiler\Llk\Rules\TokenRule(70, '_parenthesis', null, -1, false),
                'escaped_type' => new \Hoa\Compiler\Llk\Rules\ConcatenationRule('escaped_type', [69, 'type', 70], null),
                72 => new \Hoa\Compiler\Llk\Rules\TokenRule(72, 'or', null, -1, false),
                'union' => new \Hoa\Compiler\Llk\Rules\ConcatenationRule('union', ['basic_type', 72, 'type'], '#union'),
                74 => new \Hoa\Compiler\Llk\Rules\TokenRule(74, 'and', null, -1, false),
                'intersection' => new \Hoa\Compiler\Llk\Rules\ConcatenationRule('intersection', ['basic_type', 74, 'type'], '#intersection'),
                76 => new \Hoa\Compiler\Llk\Rules\TokenRule(76, 'angular_', null, -1, false),
                77 => new \Hoa\Compiler\Llk\Rules\TokenRule(77, 'comma', null, -1, false),
                78 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(78, [77, 'type'], '#generic'),
                79 => new \Hoa\Compiler\Llk\Rules\RepetitionRule(79, 0, -1, 78, null),
                80 => new \Hoa\Compiler\Llk\Rules\TokenRule(80, '_angular', null, -1, false),
                'generic' => new \Hoa\Compiler\Llk\Rules\ConcatenationRule('generic', ['class_name', 76, 'type', 79, 80], null),
                82 => new \Hoa\Compiler\Llk\Rules\TokenRule(82, 'callable', null, -1, true),
                83 => new \Hoa\Compiler\Llk\Rules\TokenRule(83, 'parenthesis_', null, -1, false),
                84 => new \Hoa\Compiler\Llk\Rules\RepetitionRule(84, 0, 1, 'callable_parameters', null),
                85 => new \Hoa\Compiler\Llk\Rules\TokenRule(85, '_parenthesis', null, -1, false),
                86 => new \Hoa\Compiler\Llk\Rules\TokenRule(86, 'colon', null, -1, false),
                87 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(87, [86, 'type'], '#callable'),
                88 => new \Hoa\Compiler\Llk\Rules\RepetitionRule(88, 0, 1, 87, null),
                'callable' => new \Hoa\Compiler\Llk\Rules\ConcatenationRule('callable', [82, 83, 84, 85, 88], null),
                90 => new \Hoa\Compiler\Llk\Rules\TokenRule(90, 'comma', null, -1, false),
                91 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(91, [90, 'type'], null),
                92 => new \Hoa\Compiler\Llk\Rules\RepetitionRule(92, 0, -1, 91, null),
                'callable_parameters' => new \Hoa\Compiler\Llk\Rules\ConcatenationRule('callable_parameters', ['type', 92], null),
                94 => new \Hoa\Compiler\Llk\Rules\RepetitionRule(94, 0, 1, 'backslash', null),
                95 => new \Hoa\Compiler\Llk\Rules\TokenRule(95, 'id', null, -1, true),
                96 => new \Hoa\Compiler\Llk\Rules\TokenRule(96, 'id', null, -1, true),
                97 => new \Hoa\Compiler\Llk\Rules\ConcatenationRule(97, ['backslash', 96], '#class_name'),
                98 => new \Hoa\Compiler\Llk\Rules\RepetitionRule(98, 0, -1, 97, null),
                'class_name' => new \Hoa\Compiler\Llk\Rules\ConcatenationRule('class_name', [94, 95, 98], null),
                100 => new \Hoa\Compiler\Llk\Rules\TokenRule(100, 'backslash', null, -1, false),
                'backslash' => new \Hoa\Compiler\Llk\Rules\ConcatenationRule('backslash', [100], '#backslash'),
                102 => new \Hoa\Compiler\Llk\Rules\TokenRule(102, 'bracket_', null, -1, false),
                103 => new \Hoa\Compiler\Llk\Rules\TokenRule(103, '_bracket', null, -1, false),
                'brackets' => new \Hoa\Compiler\Llk\Rules\ConcatenationRule('brackets', [102, 103], '#brackets'),
            ],
            [
            ]
        );

        $this->getRule('expression')->setPPRepresentation(' type() ::word::*');
        $this->getRule('type')->setPPRepresentation(' basic_type() | derived_type()');
        $this->getRule('derived_type')->setPPRepresentation(' union() | intersection()');
        $this->getRule('basic_type')->setDefaultId('#basic_type');
        $this->getRule('basic_type')->setPPRepresentation(' ( escaped_type() | <built_in> | literal() | object_like_array() | array() | generic() | callable() | class_name() ) brackets()*');
        $this->getRule('quoted_string')->setPPRepresentation(' ::quote_:: <string> ::_quote::');
        $this->getRule('literal')->setDefaultId('#literal');
        $this->getRule('literal')->setPPRepresentation(' quoted_string() | <int_number> | <float_number> | <true> | <false> | <null> | const()');
        $this->getRule('const')->setDefaultId('#const');
        $this->getRule('const')->setPPRepresentation(' class_name() ::namespace:: <id>');
        $this->getRule('array')->setDefaultId('#array');
        $this->getRule('array')->setPPRepresentation(' <array> ::angular_:: type() ::comma:: type() ::_angular:: | <array> ::angular_:: type() ::_angular:: | <array>');
        $this->getRule('object_like_array')->setDefaultId('#object_like_array');
        $this->getRule('object_like_array')->setPPRepresentation(' <array> ::brace_:: property() (::comma:: property())* ::_brace::');
        $this->getRule('property_name')->setDefaultId('#property_name');
        $this->getRule('property_name')->setPPRepresentation(' <id> | <int_number> | quoted_string()');
        $this->getRule('property')->setDefaultId('#property');
        $this->getRule('property')->setPPRepresentation(' property_name() property_option()? ::colon:: type() | type()');
        $this->getRule('property_option')->setDefaultId('#property_option');
        $this->getRule('property_option')->setPPRepresentation(' ::question::');
        $this->getRule('escaped_type')->setPPRepresentation(' ::parenthesis_:: type() ::_parenthesis::');
        $this->getRule('union')->setDefaultId('#union');
        $this->getRule('union')->setPPRepresentation(' basic_type() ::or:: type()');
        $this->getRule('intersection')->setDefaultId('#intersection');
        $this->getRule('intersection')->setPPRepresentation(' basic_type() ::and:: type()');
        $this->getRule('generic')->setDefaultId('#generic');
        $this->getRule('generic')->setPPRepresentation(' class_name() ::angular_:: type() (::comma:: type())* ::_angular::');
        $this->getRule('callable')->setDefaultId('#callable');
        $this->getRule('callable')->setPPRepresentation(' <callable> ::parenthesis_:: callable_parameters()? ::_parenthesis:: (::colon:: type())?');
        $this->getRule('callable_parameters')->setPPRepresentation(' type() (::comma:: type())*');
        $this->getRule('class_name')->setDefaultId('#class_name');
        $this->getRule('class_name')->setPPRepresentation(' backslash()? <id> (backslash() <id>)*');
        $this->getRule('backslash')->setDefaultId('#backslash');
        $this->getRule('backslash')->setPPRepresentation(' ::backslash::');
        $this->getRule('brackets')->setDefaultId('#brackets');
        $this->getRule('brackets')->setPPRepresentation(' ::bracket_:: ::_bracket::');
    }
}

