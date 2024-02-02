<?php

namespace Hamlet\Type\Parser;

use Hamlet\Type\Type;
use Hamlet\Type\Types;
use PhpParser\NameContext;
use PhpParser\Node\Name;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprFloatNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprIntegerNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\QuoteAwareConstExprStringNode;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ConstTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use RuntimeException;

/**
 * @psalm-internal Hamlet\Type
 */
final class DocBlockAstTraverser
{
    public function traverse(Node $node, ?NameContext $nameContext): Type
    {
        if ($node instanceof GenericTypeNode) {
            if ($node->type->name == 'array') {
                return match (count($node->genericTypes)) {
                    1 => new Types\ArrayType($this->traverse($node->genericTypes[0], $nameContext)),
                    2 => new Types\MapType(
                        $this->traverse($node->genericTypes[0], $nameContext),
                        $this->traverse($node->genericTypes[1], $nameContext),
                    ),
                    default => throw new RuntimeException('Unknown array type: ' . count($node->genericTypes)),
                };
            } elseif ($node->type->name == 'list') {
                return match (count($node->genericTypes)) {
                    1 => new Types\ListType($this->traverse($node->genericTypes[0], $nameContext)),
                    default => throw new RuntimeException('Unknown list type: ' . count($node->genericTypes)),
                };
            } elseif ($node->type->name == 'non-empty-array') {
                return match (count($node->genericTypes)) {
                    1 => new Types\NonEmptyArrayType($this->traverse($node->genericTypes[0], $nameContext)),
                    default => throw new RuntimeException('Unknown non-empty-array type: ' . count($node->genericTypes)),
                };
            } elseif ($node->type->name == 'non-empty-list') {
                return match (count($node->genericTypes)) {
                    1 => new Types\NonEmptyListType($this->traverse($node->genericTypes[0], $nameContext)),
                    default => throw new RuntimeException('Unknown list type: ' . count($node->genericTypes)),
                };
            }
            throw new RuntimeException('Unknown generic type: ' . print_r($node, true));
        } elseif ($node instanceof IdentifierTypeNode) {
            return match ($node->name) {
                'string' => new Types\StringType,
                'int' => new Types\IntType,
                'float', 'double' => new Types\FloatType,
                'bool', 'boolean' => new Types\BoolType,
                'mixed' => new Types\MixedType,
                'resource' => new Types\ResourceType,
                'array-key' => new Types\ArrayKeyType,
                'numeric' => new Types\NumericType,
                'numeric-string' => new Types\NumericStringType,
                'array' => new Types\ArrayType(new Types\MixedType),
                'object' => new Types\ObjectType,
                'null' => new Types\NullType,
                'true' => new Types\LiteralType(true),
                'false' => new Types\LiteralType(false),
                'non-empty-array' => new Types\NonEmptyArrayType(new Types\MixedType),
                'non-empty-list' => new Types\NonEmptyListType(new Types\MixedType),
                'non-empty-string' => new Types\NonEmptyStringType,
                default => $this->traverseUserTypeNode($node, $nameContext),
            };
        } elseif ($node instanceof UnionTypeNode) {
            $options = array_map(fn ($item) => $this->traverse($item, $nameContext), $node->types);
            return new Types\UnionType($options);
        } elseif ($node instanceof ConstTypeNode) {
            if ($node->constExpr instanceof QuoteAwareConstExprStringNode) {
                return new Types\LiteralType($node->constExpr->value);
            } elseif ($node->constExpr instanceof ConstExprIntegerNode) {
                return new Types\LiteralType($node->constExpr->value);
            } elseif ($node->constExpr instanceof ConstExprFloatNode) {
                return new Types\LiteralType($node->constExpr->value);
            }
            throw new RuntimeException('Unknown const type: ' . print_r($node, true));
        } elseif ($node instanceof ArrayTypeNode) {
            return new Types\ArrayType($this->traverse($node->type, $nameContext));
        } elseif ($node instanceof ArrayShapeNode) {
            // @todo assert the node name
            return $this->traverseTupleNode($node, $nameContext);
        } elseif ($node instanceof NullableTypeNode) {
            return new Types\UnionType([$this->traverse($node->type, $nameContext), new Types\NullType]);
        }
        throw new RuntimeException('Unknown type: ' . print_r($node, true));
    }

    private function resolveClassName(String $name, ?NameContext $nameContext): string
    {
        if ($nameContext !== null) {
            if ($name[0] == '\\') {
                $className = new Name\FullyQualified($name);
            } else {
                $className = new Name($name);
            }
            return $nameContext->getResolvedClassName($className)->toString();
        } else {
            return $name;
        }
    }

    private function traverseUserTypeNode(IdentifierTypeNode $node, ?NameContext $nameContext): Type
    {
        $resolvedClassName = $this->resolveClassName($node->name, $nameContext);

        if (!class_exists($resolvedClassName) && !interface_exists($resolvedClassName)) {
            throw new RuntimeException('Unknown user type ' . $resolvedClassName);
        }
        return new Types\ClassType($resolvedClassName);
    }

    private function traverseTupleNode(ArrayShapeNode $node, ?NameContext $nameContext): Type
    {
        $fields = [];
        foreach ($node->items as $item) {
            if ($item->keyName->name) {
                throw new RuntimeException('Unsupported object like arrays: ' . $node);
            }
            $fields[] = $this->traverse($item->valueType, $nameContext);
        }
        return new Types\TupleType($fields);
    }
}
