<?php

namespace Hamlet\Type\Parser;

use Hamlet\Type\MixedType;
use Hamlet\Type\Type;
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
     * @psalm-var array<string,Type>
     */
    private $properties = [];

    /**
     * @var string|null
     */
    private $currentClass = null;

    /**
     * @var array|true|null
     * @psalm-var Type|true|null
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
                    $this->currentProperty = Type::of($typeDeclaration, $this->getNameContext());
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
                    $reflectionType = (new ReflectionClass($this->currentClass))->getProperty($node->name)->getType();
                    if ($reflectionType !== null) {
                        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                        $typeDeclaration = (string) $reflectionType->getName();
                        if ($reflectionType->allowsNull()) {
                            $typeDeclaration .= '|null';
                        }
                        $this->properties[$key] = Type::of($typeDeclaration);
                    }
                } else {
                    $this->properties[$key] = new MixedType;
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
     * @psalm-return array<string,Type>
     */
    public function properties(): array
    {
        return $this->properties;
    }
}
