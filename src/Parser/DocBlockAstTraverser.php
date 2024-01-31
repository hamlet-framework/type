<?php

namespace Hamlet\Type\Parser;

use Hamlet\Type\ArrayKeyType;
use Hamlet\Type\ArrayType;
use Hamlet\Type\BoolType;
use Hamlet\Type\CallableType;
use Hamlet\Type\ClassType;
use Hamlet\Type\FloatType;
use Hamlet\Type\IntType;
use Hamlet\Type\ListType;
use Hamlet\Type\LiteralType;
use Hamlet\Type\MapType;
use Hamlet\Type\MixedType;
use Hamlet\Type\NullType;
use Hamlet\Type\NumericStringType;
use Hamlet\Type\NumericType;
use Hamlet\Type\ObjectLikeType;
use Hamlet\Type\ObjectType;
use Hamlet\Type\ResourceType;
use Hamlet\Type\StringType;
use Hamlet\Type\Type;
use Hamlet\Type\Union2Type;
use Hamlet\Type\Union3Type;
use Hamlet\Type\Union4Type;
use Hamlet\Type\Union5Type;
use Hamlet\Type\Union6Type;
use Hamlet\Type\Union7Type;
use Hamlet\Type\Union8Type;
use PhpParser\NameContext;
use PhpParser\Node\Name;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprFloatNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprIntegerNode;
use PHPStan\PhpDocParser\Ast\ConstExpr\QuoteAwareConstExprStringNode;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\CallableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ConstTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use RuntimeException;

class DocBlockAstTraverser
{
    public function traverse(Node $node, ?NameContext $nameContext): Type
    {
        if ($node instanceof GenericTypeNode) {
            if ($node->type->name == 'array') {
                return match (count($node->genericTypes)) {
                    1 => new ArrayType($this->traverse($node->genericTypes[0], $nameContext)),
                    2 => new MapType(
                        $this->traverse($node->genericTypes[0], $nameContext),
                        $this->traverse($node->genericTypes[1], $nameContext),
                    ),
                    default => throw new RuntimeException('Unknown array type: ' . count($node->genericTypes)),
                };
            } elseif ($node->type->name == 'list') {
                return match (count($node->genericTypes)) {
                    1 => new ListType($this->traverse($node->genericTypes[0], $nameContext)),
                    default => throw new RuntimeException('Unknown list type: ' . count($node->genericTypes)),
                };
            }
            throw new RuntimeException('Unknown generic type: ' . print_r($node, true));
        } elseif ($node instanceof IdentifierTypeNode) {
            return match ($node->name) {
                'string' => new StringType,
                'int' => new IntType,
                'float', 'double' => new FloatType,
                'bool' => new BoolType,
                'mixed' => new MixedType,
                'resource' => new ResourceType,
                'array-key' => new ArrayKeyType,
                'numeric' => new NumericType,
                'numeric-string' => new NumericStringType,
                'array' => new ArrayType(new MixedType),
                'object' => new ObjectType,
                'null' => new NullType,
                'true' => new LiteralType(true),
                'false' => new LiteralType(false),
                default => $this->traverseUserTypeNode($node, $nameContext),
            };
        } elseif ($node instanceof UnionTypeNode) {
            $types = [];
            foreach ($node->types as $type) {
                $types[] = $this->traverse($type, $nameContext);
            }
            return match(count($types)) {
                2 => new Union2Type($types[0], $types[1]),
                3 => new Union3Type($types[0], $types[1], $types[2]),
                4 => new Union4Type($types[0], $types[1], $types[2], $types[3]),
                5 => new Union5Type($types[0], $types[1], $types[2], $types[3], $types[4]),
                6 => new Union6Type($types[0], $types[1], $types[2], $types[3], $types[4], $types[5]),
                7 => new Union7Type($types[0], $types[1], $types[2], $types[3], $types[4], $types[5], $types[6]),
                8 => new Union8Type($types[0], $types[1], $types[2], $types[3], $types[4], $types[5], $types[6], $types[7]),
                default => throw new RuntimeException('Unsupported union type: ' . count($types)),
            };
        } elseif ($node instanceof ConstTypeNode) {
            if ($node->constExpr instanceof QuoteAwareConstExprStringNode) {
                return new LiteralType($node->constExpr->value);
            } elseif ($node->constExpr instanceof ConstExprIntegerNode) {
                return new LiteralType($node->constExpr->value);
            } elseif ($node->constExpr instanceof ConstExprFloatNode) {
                return new LiteralType($node->constExpr->value);
            }
            throw new RuntimeException('Unknown const type: ' . print_r($node, true));
        } elseif ($node instanceof ArrayTypeNode) {
            return new ArrayType($this->traverse($node->type, $nameContext));
        } elseif ($node instanceof ArrayShapeNode) {
            return $this->traverseObjectLikeArrayNode($node, $nameContext);
        } elseif ($node instanceof CallableTypeNode) {
            return $this->traverseCallableNode($node, $nameContext);
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
        return new ClassType($resolvedClassName);
    }

    private function traverseObjectLikeArrayNode(ArrayShapeNode $node, ?NameContext $nameContext): Type
    {
        $properties = [];
        foreach ($node->items as $item) {
            $key = $item->keyName->name ?? '0';
            $optional = $item->optional;
            $type = $this->traverse($item->valueType, $nameContext);

            $properties[$key . ($optional ? '?' : '')] = $type;
        }
        return new ObjectLikeType($properties);
    }

    private function traverseCallableNode(CallableTypeNode $node, ?NameContext $nameContext): Type
    {
        $tag = $node->identifier->name;
        $parameterTypes = [];
        foreach ($node->parameters as $parameter) {
            $parameterTypes[] = $this->traverse($parameter->type, $nameContext);
        }
        $returnType = $this->traverse($node->returnType, $nameContext);

        return new CallableType($tag, $returnType, $parameterTypes);
    }
}
