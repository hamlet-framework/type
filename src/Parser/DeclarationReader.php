<?php declare(strict_types=1);

namespace Hamlet\Type\Parser;

use Hamlet\Type\Type;
use PhpParser\NameContext;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;

final class DeclarationReader
{
    private static ?self $instance;

    public static function instance(): self
    {
        return self::$instance ??= new self;
    }

    private function __construct() {}

    public function read(string $declaration, ?NameContext $nameContext = null): Type
    {
        $lexer = new Lexer;
        $constExprParser = new ConstExprParser(true, true, []);
        $typeParser = new TypeParser($constExprParser, true, []);

        $tokens = new TokenIterator($lexer->tokenize($declaration));
        $node = $typeParser->parse($tokens);

        return (new DocBlockAstTraverser)->traverse($node, $nameContext);
    }

//    public function parse_(TreeNode $node, ?NameContext $nameContext = null): Type
//    {
//        if ($node->isToken() && $node->getValueToken() == 'built_in') {
//            return $this->fromBuiltIn($node);
//        }
//        switch ($node->getId()) {
//            case '#literal':
//                return $this->fromLiteral($node);
//            case '#class_name':
//                return $this->fromClassName($node);
//            case '#union':
//                $subTypes = [];
//                for ($i = 0; $i < $node->getChildrenNumber(); $i++) {
//                    $child = $node->getChild($i);
//                    assert($child !== null);
//                    $subTypes[] = $this->parse($child);
//                }
//                return _union(...$subTypes);
//            case '#array':
//                return $this->fromArray($node);
//            case '#object_like_array':
//                return $this->fromObjectLikeArray($node);
//            case '#basic_type':
//                $firstChild = $node->getChild(0);
//                assert($firstChild !== null);
//                $type = $this->parse($firstChild);
//                for ($i = 1; $i < $node->getChildrenNumber(); $i++) {
//                    $child = $node->getChild($i);
//                    assert($child !== null);
//                    if ($child->getId() == '#brackets') {
//                        $type = new ArrayType($type);
//                    }
//                }
//                return $type;
//            case '#callable':
//                return $this->fromCallable($node);
//            case '#generic':
//                return $this->fromGeneric($node);
//        }
//        throw new RuntimeException('Cannot convert node ' . print_r($node, true));
//    }
//
//    private function fromLiteral(TreeNode $node): Type
//    {
//        $firstChild = $node->getChild(0);
//        assert($firstChild !== null);
//        if ($firstChild->isToken()) {
//            switch ($firstChild->getValueToken()) {
//                case 'true':
//                    return new LiteralType(true);
//                case 'false':
//                    return new LiteralType(false);
//                case 'null':
//                    return new NullType;
//                case 'int_number':
//                    return new LiteralType((int) $firstChild->getValueValue());
//                case 'float_number':
//                    return new LiteralType((float) $firstChild->getValueValue());
//                case 'string':
//                    return new LiteralType($firstChild->getValueValue());
//            }
//        }
//        throw new RuntimeException('Cannot convert node ' . print_r($node, true));
//    }
//
//    private function fromBuiltIn(TreeNode $node): Type
//    {
//        switch ($node->getValueValue()) {
//            case 'boolean':
//            case 'bool':
//                return new BoolType;
//            case 'integer':
//            case 'int':
//                return new IntType;
//            case 'float':
//            case 'double':
//                return new FloatType;
//            case 'numeric-string':
//                return new NumericStringType;
//            case 'numeric':
//                return new NumericType;
//            case 'array-key':
//                return new ArrayKeyType;
//            case 'string':
//                return new StringType;
//            case 'object':
//                return new ObjectType;
//            case 'mixed':
//                return new MixedType;
//            case 'resource':
//                return new ResourceType;
//        }
//        throw new RuntimeException('Cannot convert node ' . print_r($node, true));
//    }
//
//    /**
//     * @param TreeNode $node
//     * @return Type
//     * @psalm-suppress RedundantCondition
//     */
//    private function fromClassName(TreeNode $node): Type
//    {
//        $path = '';
//        for ($i = 0; $i < $node->getChildrenNumber(); $i++) {
//            $child = $node->getChild($i);
//            assert($child !== null);
//            if ($child->getId() === '#backslash') {
//                $path .= '\\';
//            } elseif ($child->getId() === 'token' && $child->getValueToken() === 'id') {
//                $valueValue = $child->getValueValue();
//                assert($valueValue !== null);
//                $path .= $valueValue;
//            } else {
//                throw new RuntimeException('Unexpected ID ' . print_r($child, true));
//            }
//        }
//
//        if ($this->nameContext) {
//            if ($path[0] == '\\') {
//                $className = new Name\FullyQualified($path);
//            } else {
//                $className = new Name($path);
//            }
//            $resolvedClassName = $this->nameContext->getResolvedClassName($className)->toString();
//        } else {
//            $resolvedClassName = $path;
//        }
//        if (!class_exists($resolvedClassName) && !interface_exists($resolvedClassName)) {
//            throw new RuntimeException('Unknown type ' . $path);
//        }
//        return new ClassType($resolvedClassName);
//    }
//
//    private function fromArray(TreeNode $node): Type
//    {
//        $firstChild = $node->getChild(0);
//        assert($firstChild !== null);
//        $tag = $firstChild->getValueValue();
//        $list = $tag == 'list' || $tag == 'non-empty-list';
//
//        /**
//         * @psalm-suppress MixedArgumentTypeCoercion
//         */
//        switch ($node->getChildrenNumber()) {
//            case 1:
//                if ($list) {
//                    return new ListType(new MixedType());
//                } else {
//                    return new MapType(new ArrayKeyType(), new MixedType());
//                }
//            case 2:
//                $secondChild = $node->getChild(1);
//                assert($secondChild !== null);
//                if ($list) {
//                    return new ListType($this->parse($secondChild));
//                } else {
//                    return new MapType(new ArrayKeyType(), $this->parse($secondChild));
//                }
//            case 3:
//                if (!$list) {
//                    $secondChild = $node->getChild(1);
//                    $thirdChild = $node->getChild(2);
//                    assert($secondChild !== null && $thirdChild !== null);
//                    return new MapType($this->parse($secondChild), $this->parse($thirdChild));
//                }
//        }
//        throw new RuntimeException('Cannot convert node ' . print_r($node, true));
//    }
//
//    /**
//     * @param TreeNode $node
//     * @return array{0:string,1:bool,2:Type}
//     */
//    private function fromProperty(TreeNode $node): array
//    {
//        switch ($node->getChildrenNumber()) {
//            case 1:
//                $firstChild = $node->getChild(0);
//                assert($firstChild !== null);
//                return ['', true, $this->parse($firstChild)];
//            case 2:
//                $name = $node->getChild(0)?->getChild(0)?->getValueValue();
//                assert($name !== null);
//                $secondChild = $node->getChild(1);
//                assert($secondChild !== null);
//                return [$name, true, $this->parse($secondChild)];
//            case 3:
//                $name = $node->getChild(0)?->getChild(0)?->getValueValue();
//                assert($name !== null);
//                $thirdChild = $node->getChild(2);
//                assert($thirdChild !== null);
//                return [$name, false, $this->parse($thirdChild)];
//        }
//        throw new RuntimeException('Cannot convert node ' . print_r($node, true));
//    }
//
//    private function fromObjectLikeArray(TreeNode $node): Type
//    {
//        $properties = [];
//        for ($i = 1; $i < $node->getChildrenNumber(); $i++) {
//            $child = $node->getChild($i);
//            assert($child !== null);
//            if ($child->getId() == '#property') {
//                list($name, $required, $type) = $this->fromProperty($child);
//                $properties[$name . ($required ? '' : '?')] = $type;
//            } else {
//                throw new RuntimeException('Cannot convert node ' . print_r($child, true));
//            }
//        }
//        return _object_like($properties);
//    }
//
//    private function fromCallable(TreeNode $node): Type
//    {
//        $firstChild = $node->getChild(0);
//        assert($firstChild !== null);
//        $tag = $firstChild->getValueValue();
//        $types = [];
//        for ($i = 1; $i < $node->getChildrenNumber(); $i++) {
//            $child = $node->getChild($i);
//            assert($child !== null);
//            $types[] = $this->parse($child);
//        }
//        if (empty($types)) {
//            $returnType = null;
//        } else {
//            $returnType = array_pop($types);
//        }
//        assert($tag !== null);
//        return new CallableType($tag, $returnType, $types);
//    }
//
//    private function fromGeneric(TreeNode $node): Type
//    {
//        $firstChild = $node->getChild(0);
//        assert($firstChild !== null);
//        return $this->parse($firstChild);
//    }
}
