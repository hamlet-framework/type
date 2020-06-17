<?php declare(strict_types=1);

namespace Hamlet\Type\Parser;

use Hamlet\Type\MixedType;
use Hamlet\Type\Type;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use ReflectionProperty;
use RuntimeException;

class DocBlockParser
{
    public static function fromProperty(ReflectionProperty $property): Type
    {
        $fileName = $property->getDeclaringClass()->getFileName();
        if ($fileName === false) {
            throw new RuntimeException('Cannot find declaring file name');
        }
        $body = file_get_contents($fileName);

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $traverser = new NodeTraverser;
        $visitor = new PropertyVisitor($property->getDeclaringClass());
        $traverser->addVisitor($visitor);
        $statements = $parser->parse($body);
        if ($statements) {
            $traverser->traverse($statements);
        }

        $properties = $visitor->properties();
        if (isset($properties[$property->getName()])) {
            list($declaration, $nameResolver) = $properties[$property->getName()];
            return Type::of($declaration, $nameResolver);
        } else {
            return new MixedType;
        }
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
