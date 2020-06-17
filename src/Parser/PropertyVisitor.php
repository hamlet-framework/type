<?php

namespace Hamlet\Type\Parser;

use PhpParser\Node;
use PhpParser\NodeVisitor\NameResolver;
use ReflectionClass;

class PropertyVisitor extends NameResolver
{
    /** @var ReflectionClass */
    private $reflectionClass;

    private $properties = [];

    public function __construct(ReflectionClass $reflectionClass)
    {
        parent::__construct(null, [
            'preserveOriginalNames' => false,
            'replaceNodes' => false,
        ]);
        $this->reflectionClass = $reflectionClass;
    }


    public function enterNode(Node $node)
    {
        parent::enterNode($node);
        if ($node instanceof Node\Stmt\Property) {
            // @todo this is by the way very stupid thing to do!
            $propertyName = $node->props[0]->name->name;
            $docComment = $node->getDocComment();
            if ($docComment) {
                $fields = DocBlockParser::parse($docComment->getText());
                foreach ($fields as $field) {
                    if ($field['tag'] == '@psalm-var') {
                        $this->properties[$propertyName] = [$field['type'], $this->getNameContext()];
                        return;
                    }
                }
                foreach ($fields as $field) {
                    if ($field['tag'] == '@var') {
                        $this->properties[$propertyName] = [$field['type'], $this->getNameContext()];
                    }
                }
            } elseif (version_compare(PHP_VERSION, '7.4.0') >= 0) {
                /** @noinspection PhpElementIsNotAvailableInCurrentPhpVersionInspection */
                $reflectionType = $this->reflectionClass->getProperty($propertyName)->getType();
                if ($reflectionType !== null) {
                    $type = $reflectionType->getName();
                    if ($reflectionType->allowsNull()) {
                        $type .= '|null';
                    }
                    $this->properties[$propertyName] = [$type, null];
                }
            } else {
                $this->properties[$propertyName] = ['mixed', $this->getNameContext()];
            }
        }
    }

    public function properties(): array
    {
        return $this->properties;
    }
}
