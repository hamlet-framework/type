<?php

namespace Hamlet\Type\Parser;

use PhpParser\Node;
use PhpParser\Node\VarLikeIdentifier;
use PhpParser\NodeVisitor\NameResolver;
use ReflectionClass;

class PropertyVisitor extends NameResolver
{
    /**
     * @var ReflectionClass
     */
    private $reflectionClass;

    /**
     * @var array
     * @psalm-var array<string,array{0:string,1:\PhpParser\NameContext|null}>
     */
    private $properties = [];

    /**
     * @var array|true|null
     * @psalm-var array{0:string,1:\PhpParser\NameContext|null}|true|null
     */
    private $currentProperty = null;

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
        if ($node instanceof Node\Stmt\Property) {
            $docComment = $node->getDocComment();
            if ($docComment) {
                $fields = DocBlockParser::parse($docComment->getText());
                foreach ($fields as $field) {
                    if ($field['tag'] == '@psalm-var') {
                        $this->currentProperty = [$field['type'], $this->getNameContext()];
                    }
                }
                if ($this->currentProperty === null) {
                    foreach ($fields as $field) {
                        if ($field['tag'] == '@var') {
                            $this->currentProperty = [$field['type'], $this->getNameContext()];
                        }
                    }
                }
            } else {
                $this->currentProperty = true;
            }
        } elseif ($node instanceof VarLikeIdentifier) {
            if ($this->currentProperty === true) {
                /**
                 * @psalm-suppress MixedAssignment
                 * @psalm-suppress MixedMethodCall
                 * @psalm-suppress MixedOperand
                 */
                if (version_compare(PHP_VERSION, '7.4.0') >= 0) {
                    /** @noinspection PhpElementIsNotAvailableInCurrentPhpVersionInspection */
                    $reflectionType = $this->reflectionClass->getProperty($node->name)->getType();
                    if ($reflectionType !== null) {
                        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                        $type = (string) $reflectionType->getName();
                        if ($reflectionType->allowsNull()) {
                            $type .= '|null';
                        }
                        $this->properties[$node->name] = [$type, null];
                    }
                } else {
                    $this->properties[$node->name] = ['mixed', null];
                }
                $this->currentProperty = null;
            } elseif ($this->currentProperty !== null) {
                $this->properties[$node->name] = $this->currentProperty;
                $this->currentProperty = null;
            }
        }
        return parent::enterNode($node);
    }

    /**
     * @return array
     * @psalm-return array<string,array{0:string,1:\PhpParser\NameContext|null}>
     */
    public function properties(): array
    {
        return $this->properties;
    }
}
