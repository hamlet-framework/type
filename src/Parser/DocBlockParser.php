<?php declare(strict_types=1);

namespace Hamlet\Type\Parser;

use Hamlet\Type\Type;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use ReflectionProperty;
use RuntimeException;

class DocBlockParser
{
    /**
     * @param ReflectionProperty $reflectionProperty
     * @return Type
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public static function fromProperty(ReflectionProperty $reflectionProperty): Type
    {
        $reflectionClass = $reflectionProperty->getDeclaringClass();
        $cacheKey = $reflectionClass->getName() . '::' . $reflectionProperty->getName();
        $fileName = $reflectionClass->getFileName();
        if ($fileName === false) {
            throw new RuntimeException('Cannot find declaring file name');
        }

        /** @psalm-suppress MixedAssignment */
        $propertyType = Cache::get($cacheKey, filemtime($fileName));
        if ($propertyType !== null) {
            return $propertyType;
        }

        $body = file_get_contents($fileName);

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $traverser = new NodeTraverser;
        $visitor = new PropertyVisitor($reflectionProperty->getDeclaringClass());
        $traverser->addVisitor($visitor);
        $statements = $parser->parse($body);
        if ($statements) {
            $traverser->traverse($statements);
        }

        $result = null;
        foreach ($visitor->properties() as $key => list($declaration, $nameResolver)) {
            $propertyType = Type::of($declaration, $nameResolver);
            Cache::set($key, $propertyType);
            if ($cacheKey == $key) {
                $result = $propertyType;
            }
        }
        assert($result !== null);
        return $result;
    }

    /**
     * @param string $doc
     * @return string|null
     */
    public static function varTypeDeclarationFrom(string $doc)
    {
        $fields = self::parseDoc($doc);
        foreach ($fields as $field) {
            if ($field['tag'] == '@psalm-var') {
                return $field['type'];
            }
        }
        foreach ($fields as $field) {
            if ($field['tag'] == '@var') {
                return $field['type'];
            }
        }
        return null;
    }

    /**
     * @param string $doc
     * @return array
     * @psalm-return array<int, array{tag:string, type:string, variable?:string}>
     */
    public static function parseDoc(string $doc)
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
                            $doc
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
