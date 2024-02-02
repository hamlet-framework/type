<?php declare(strict_types=1);

namespace Hamlet\Type\Parser;

use Exception;
use Hamlet\Type\Type;
use Hamlet\Type\Types\MixedType;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use ReflectionClass;
use ReflectionProperty;
use RuntimeException;

/**
 * @psalm-internal Hamlet\Type
 */
final class DocBlockParser
{
    public static function fromProperty(ReflectionClass $reflectionClass, ReflectionProperty $reflectionProperty): Type
    {
        $declaringReflectionClass = $reflectionProperty->getDeclaringClass();
        $cacheKey = $declaringReflectionClass->getName() . '::' . $reflectionProperty->getName();
        $fileName = $declaringReflectionClass->getFileName();
        if ($fileName === false) {
            throw new RuntimeException('Cannot find declaring file name');
        }

        try {
            $propertyType = Cache::get($cacheKey, filemtime($fileName));
            if ($propertyType !== null) {
                return $propertyType;
            }
        } catch (Exception $exception) {
            error_log($exception->getTraceAsString());
            Cache::remove($cacheKey);
        }


        $body = file_get_contents($fileName);

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $traverser = new NodeTraverser;
        $visitor = new PropertyVisitor($reflectionProperty->getDeclaringClass());
        $traverser->addVisitor($visitor);
        $statements = $parser->parse($body);
        if ($statements !== null) {
            $traverser->traverse($statements);
        }

        $result = null;
        foreach ($visitor->properties() as $key => $propertyType) {
            Cache::set($key, $propertyType);
            if ($cacheKey == $key) {
                $result = $propertyType;
            }
        }
        if ($result === null) {
            $result = new MixedType;
        }
        return $result;
    }

    public static function varTypeDeclarationFrom(string $doc): ?string
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
     * @return array<int, array{tag:string, type:string, variable?:string}>
     */
    public static function parseDoc(string $doc): array
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

        /**
         * @var array<array{string,string}> $sections
         */
        $entries = [];
        foreach ($sections as [$tag, $body]) {
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
