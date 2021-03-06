<?php

namespace Hamlet\Cast\Parser;

use Hamlet\Cast\MixedType;
use Hamlet\Cast\Type;
use PhpParser\Node;
use PhpParser\Node\VarLikeIdentifier;
use PhpParser\NodeVisitor\NameResolver;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

class PropertyVisitor extends NameResolver
{
    /**
     * @var ReflectionClass
     */
    private $reflectionClass;

    /**
     * @var ReflectionClass[]
     * @psalm-var array<class-string,ReflectionClass>
     */
    private static $reflectionClasses = [];

    /**
     * @var array
     * @psalm-var array<string,Type>
     */
    private $properties = [];

    /**
     * @var string|null
     * @psalm-var class-string|null
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
        self::$reflectionClasses[$reflectionClass->getName()] = $reflectionClass;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_) {
            $className = (string) $node->name;
            /** @psalm-suppress PropertyTypeCoercion */
            $this->currentClass = $this->getNameContext()->getResolvedClassName(new Node\Name($className))->toString();
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
            if ($this->currentClass === null) {
                throw new RuntimeException('Invalid class context');
            }
            $key = $this->currentClass . '::' . $node->name;
            if ($this->currentProperty === true) {
                /**
                 * @psalm-suppress MixedAssignment
                 * @psalm-suppress MixedMethodCall
                 * @psalm-suppress MixedOperand
                 * @psalm-suppress UndefinedMethod
                 */
                if (version_compare(phpversion(), '7.4') >= 0) {
                    $currentReflectionClass = $this->reflectionClassByName($this->currentClass);
                    /** @noinspection PhpElementIsNotAvailableInCurrentPhpVersionInspection */
                    $reflectionType = $currentReflectionClass->getProperty($node->name)->getType();
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

    /**
     * @param string|null $type
     * @psalm-param class-string|null $type
     * @return ReflectionClass
     */
    private function reflectionClassByName($type): ReflectionClass
    {
        if ($type === null) {
            throw new RuntimeException('Type information missing');
        }
        if (isset(self::$reflectionClasses[$type])) {
            return self::$reflectionClasses[$type];
        } else {
            try {
                return self::$reflectionClasses[$type] = new ReflectionClass($type);
            } catch (ReflectionException $exception) {
                throw new RuntimeException('Cannot load class ' . $type, 0, $exception);
            }
        }
    }
}
