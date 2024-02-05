<?php declare(strict_types=1);

namespace Hamlet\Type\Parser;

use Hamlet\Type\Type;
use Override;
use PhpParser\Node;
use PhpParser\Node\VarLikeIdentifier;
use PhpParser\NodeVisitor\NameResolver;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use function Hamlet\Type\type_of;

/**
 * @psalm-internal Hamlet\Type
 */
final class PropertyVisitor extends NameResolver
{
    /**
     * @var array<class-string,ReflectionClass>
     */
    private static array $reflectionClasses = [];

    /**
     * @var array<string,Type>
     */
    private array $properties = [];

    /**
     * @var class-string|null
     */
    private ?string $currentClass = null;

    private Type|bool|null $currentProperty = null;

    public function __construct(ReflectionClass $reflectionClass)
    {
        parent::__construct(null, [
            'preserveOriginalNames' => false,
            'replaceNodes' => false,
        ]);
        self::$reflectionClasses[$reflectionClass->getName()] = $reflectionClass;
    }

    /**
     * @throws ReflectionException
     */
    #[Override] public function enterNode(Node $node): Node|int|null
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
                if ($typeDeclaration !== null) {
                    $this->currentProperty = type_of($typeDeclaration, $this->getNameContext());
                }
            }
        } elseif ($node instanceof VarLikeIdentifier) {
            if ($this->currentClass === null) {
                throw new RuntimeException('Invalid class context');
            }
            $key = $this->currentClass . '::' . $node->name;
            if ($this->currentProperty === true) {
                $currentReflectionClass = $this->reflectionClassByName($this->currentClass);
                $reflectionType = $currentReflectionClass->getProperty($node->name)->getType();
                if ($reflectionType !== null) {
                    /**
                     * @psalm-suppress UndefinedMethod
                     */
                    $typeDeclaration = (string)$reflectionType->getName();
                    if ($reflectionType->allowsNull()) {
                        $typeDeclaration .= '|null';
                    }
                    $this->properties[$key] = type_of($typeDeclaration);
                }
                $this->currentProperty = null;
            } elseif ($this->currentProperty) {
                $this->properties[$key] = $this->currentProperty;
                $this->currentProperty = null;
            }
        }
        return parent::enterNode($node);
    }

    /**
     * @return array<string,Type>
     */
    public function properties(): array
    {
        return $this->properties;
    }

    /**
     * @param class-string|null $type
     */
    private function reflectionClassByName(?string $type): ReflectionClass
    {
        if ($type === null) {
            throw new RuntimeException('AbstractType information missing');
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
