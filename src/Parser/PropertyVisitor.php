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
     * @var string|null
     */
    private $currentClass = null;

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
        if ($node instanceof Node\Stmt\Class_) {
            $className = (string) $node->name;
            $this->currentClass = $this->nameContext->getResolvedClassName(new Node\Name($className))->toString();
        } elseif ($node instanceof Node\Stmt\Property) {
            $this->currentProperty = true;
            $docComment = $node->getDocComment();
            if ($docComment) {
                $typeDeclaration = DocBlockParser::varTypeDeclarationFrom($docComment->getText());
                if ($typeDeclaration) {
                    $this->currentProperty = [$typeDeclaration, $this->getNameContext()];
                }
            }
        } elseif ($node instanceof VarLikeIdentifier) {
            assert($this->currentClass !== null);
            $key = $this->currentClass . '::' . $node->name;
            if ($this->currentProperty === true) {
                /**
                 * @psalm-suppress MixedAssignment
                 * @psalm-suppress MixedMethodCall
                 * @psalm-suppress MixedOperand
                 * @psalm-suppress UndefinedMethod
                 */
                if (version_compare(phpversion(), '7.4') >= 0) {
                    /** @noinspection PhpElementIsNotAvailableInCurrentPhpVersionInspection */
                    $reflectionType = $this->reflectionClass->getProperty($node->name)->getType();
                    if ($reflectionType !== null) {
                        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                        $type = (string) $reflectionType->getName();
                        if ($reflectionType->allowsNull()) {
                            $type .= '|null';
                        }
                        $this->properties[$key] = [$type, null];
                    }
                } else {
                    $this->properties[$key] = ['mixed', null];
                }
                $this->currentProperty = null;
            } elseif ($this->currentProperty !== null) {
                $this->properties[$key] = $this->currentProperty;
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
