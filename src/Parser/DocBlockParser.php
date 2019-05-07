<?php

namespace Hamlet\Cast\Parser;

use Hamlet\Cast\MixedType;
use Hamlet\Cast\Type;
use ReflectionProperty;

class DocBlockParser
{
    public static function fromProperty(ReflectionProperty $property): Type
    {
        $doc = $property->getDocComment();
        $fields = self::parse($doc);
        foreach ($fields as $field) {
            if ($field['tag'] == '@psalm-var') {
                return Type::of($field['type']);
            }
        }
        foreach ($fields as $field) {
            if ($field['tag'] == '@var') {
                return Type::of($field['type']);
            }
        }
        return new MixedType();
    }

    /**
     * @param string $specification
     * @return array
     * @psalm-return array<int, array{tag:string, type:string, variable?:string}>
     */
    public static function parse(string $specification)
    {
        $lines = preg_split(
            '|$\R?^|m',
            preg_replace(
                '|^\s*[*]|m',
                '',
                preg_replace(
                    '|[*]+/\s*$|',
                    '',
                    preg_replace(
                        '|^/[*]+\s*|',
                        '',
                        trim(
                            $specification
                        )
                    )
                )
            )
        );

        $sections = [];
        $section = null;
        foreach ($lines as $line) {
            if (preg_match('|\s*(@[a-zA-Z0-9_-]+)\s+(.*)|', $line, $matches)) {
                if ($section) {
                    $sections[] = $section;
                }
                $section = [$matches[1], trim($matches[2])];
            } elseif ($section) {
                $trimmedLine = trim($line);
                if ($trimmedLine) {
                    $section[1] .= PHP_EOL . $trimmedLine;
                }
            }
        }
        if ($section) {
            $sections[] = $section;
        }

        $entries = [];
        foreach ($sections as list($tag, $body)) {
            if (preg_match('|(.*)\s+(\$[_a-zA-Z][_a-zA-Z0-9]*)|', $body, $matches)) {
                $entries[] = [
                    'tag' => $tag,
                    'variable' => trim($matches[2]),
                    'type' => $matches[1]
                ];
            } else {
                $entries[] = [
                    'tag' => $tag,
                    'type' => $body
                ];
            }
        }

        return $entries;
    }
}
