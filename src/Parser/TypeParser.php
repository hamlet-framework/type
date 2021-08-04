<?php declare(strict_types=1);

namespace Hamlet\Cast\Parser;

use Hamlet\Cast\ArrayKeyType;
use Hamlet\Cast\ArrayType;
use Hamlet\Cast\BoolType;
use Hamlet\Cast\CallableType;
use Hamlet\Cast\ClassType;
use Hamlet\Cast\FloatType;
use Hamlet\Cast\IntType;
use Hamlet\Cast\ListType;
use Hamlet\Cast\LiteralType;
use Hamlet\Cast\MapType;
use Hamlet\Cast\MixedType;
use Hamlet\Cast\NullType;
use Hamlet\Cast\NumericStringType;
use Hamlet\Cast\NumericType;
use Hamlet\Cast\ObjectType;
use Hamlet\Cast\ResourceType;
use Hamlet\Cast\StringType;
use Hamlet\Cast\Type;
use Hoa\Compiler\Llk\TreeNode;
use PhpParser\NameContext;
use PhpParser\Node\Name;
use RuntimeException;
use function Hamlet\Cast\_object_like;
use function Hamlet\Cast\_union;

class TypeParser
{
    /**
     * @var NameContext|null
     */
    private $nameContext;

    /**
     * @param NameContext|null $nameContext
     */
    public function __construct(NameContext $nameContext = null)
    {
        $this->nameContext = $nameContext;
    }

    public function parse(TreeNode $node): Type
    {
        if ($node->isToken() && $node->getValueToken() == 'built_in') {
            return $this->fromBuiltIn($node);
        }
        switch ($node->getId()) {
            case '#literal':
                return $this->fromLiteral($node);
            case '#class_name':
                return $this->fromClassName($node);
            case '#union':
                $subTypes = [];
                for ($i = 0; $i < $node->getChildrenNumber(); $i++) {
                    $subTypes[] = $this->parse($node->getChild($i));
                }
                return _union(...$subTypes);
            case '#array':
                return $this->fromArray($node);
            case '#object_like_array':
                return $this->fromObjectLikeArray($node);
            case '#basic_type':
                $type = $this->parse($node->getChild(0));
                for ($i = 1; $i < $node->getChildrenNumber(); $i++) {
                    if ($node->getChild($i)->getId() == '#brackets') {
                        $type = new ArrayType($type);
                    }
                }
                return $type;
            case '#callable':
                return $this->fromCallable($node);
            case '#generic':
                return $this->fromGeneric($node);
        }
        throw new RuntimeException('Cannot convert node ' . print_r($node, true));
    }

    private function fromLiteral(TreeNode $node): Type
    {
        $firstChild = $node->getChild(0);
        if ($firstChild->isToken()) {
            switch ($node->getChild(0)->getValueToken()) {
                case 'true':
                    return new LiteralType(true);
                case 'false':
                    return new LiteralType(false);
                case 'null':
                    return new NullType;
                case 'int_number':
                    return new LiteralType((int) $firstChild->getValueValue());
                case 'float_number':
                    return new LiteralType((float) $firstChild->getValueValue());
                case 'string':
                    return new LiteralType($firstChild->getValueValue());
            }
        }
        throw new RuntimeException('Cannot convert node ' . print_r($node, true));
    }

    private function fromBuiltIn(TreeNode $node): Type
    {
        switch ($node->getValueValue()) {
            case 'boolean':
            case 'bool':
                return new BoolType;
            case 'integer':
            case 'int':
                return new IntType;
            case 'float':
            case 'double':
                return new FloatType;
            case 'numeric-string':
                return new NumericStringType;
            case 'numeric':
                return new NumericType;
            case 'array-key':
                return new ArrayKeyType;
            case 'string':
                return new StringType;
            case 'object':
                return new ObjectType;
            case 'mixed':
                return new MixedType;
            case 'resource':
                return new ResourceType;
        }
        throw new RuntimeException('Cannot convert node ' . print_r($node, true));
    }

    /**
     * @param TreeNode $node
     * @return Type
     */
    private function fromClassName(TreeNode $node): Type
    {
        $path = '';
        for ($i = 0; $i < $node->getChildrenNumber(); $i++) {
            $child = $node->getChild($i);
            if ($child->getId() === '#backslash') {
                $path .= '\\';
            } elseif ($child->getId() === 'token' && $child->getValueToken() === 'id') {
                $path .= $child->getValueValue();
            } else {
                throw new RuntimeException('Unexpected ID ' . print_r($child, true));
            }
        }

        if ($this->nameContext) {
            if ($path[0] == '\\') {
                $className = new Name\FullyQualified($path);
            } else {
                $className = new Name($path);
            }
            $resolvedClassName = $this->nameContext->getResolvedClassName($className)->toString();
        } else {
            $resolvedClassName = $path;
        }
        if (!class_exists($resolvedClassName) && !interface_exists($resolvedClassName)) {
            throw new RuntimeException('Unknown type ' . $path);
        }
        return new ClassType($resolvedClassName);
    }

    private function fromArray(TreeNode $node): Type
    {
        $tag = $node->getChild(0)->getValueValue();
        $list = $tag == 'list' || $tag == 'non-empty-list';

        /**
         * @psalm-suppress MixedArgumentTypeCoercion
         */
        switch ($node->getChildrenNumber()) {
            case 1:
                if ($list) {
                    return new ListType(new MixedType());
                } else {
                    return new MapType(new ArrayKeyType(), new MixedType());
                }
            case 2:
                if ($list) {
                    return new ListType($this->parse($node->getChild(1)));
                } else {
                    return new MapType(new ArrayKeyType(), $this->parse($node->getChild(1)));
                }
            case 3:
                if (!$list) {
                    return new MapType($this->parse($node->getChild(1)), $this->parse($node->getChild(2)));
                }
        }
        throw new RuntimeException('Cannot convert node ' . print_r($node, true));
    }

    /**
     * @param TreeNode $node
     * @return array{0:string,1:bool,2:Type}
     */
    private function fromProperty(TreeNode $node): array
    {
        switch ($node->getChildrenNumber()) {
            case 1:
                return ['', true, $this->parse($node->getChild(0))];
            case 2:
                $name = $node->getChild(0)->getChild(0)->getValueValue();
                return [$name, true, $this->parse($node->getChild(1))];
            case 3:
                $name = $node->getChild(0)->getChild(0)->getValueValue();
                return [$name, false, $this->parse($node->getChild(2))];
        }
        throw new RuntimeException('Cannot convert node ' . print_r($node, true));
    }

    private function fromObjectLikeArray(TreeNode $node): Type
    {
        $properties = [];
        for ($i = 1; $i < $node->getChildrenNumber(); $i++) {
            $child = $node->getChild($i);
            if ($child->getId() == '#property') {
                list($name, $required, $type) = $this->fromProperty($child);
                $properties[$name . ($required ? '' : '?')] = $type;
            } else {
                throw new RuntimeException('Cannot convert node ' . print_r($child, true));
            }
        }
        return _object_like($properties);
    }

    private function fromCallable(TreeNode $node): Type
    {
        $tag = $node->getChild(0)->getValueValue();
        $types = [];
        for ($i = 1; $i < $node->getChildrenNumber(); $i++) {
            $types[] = $this->parse($node->getChild($i));
        }
        if (empty($types)) {
            $returnType = null;
        } else {
            $returnType = array_pop($types);
        }
        return new CallableType($tag, $returnType, $types);
    }

    private function fromGeneric(TreeNode $node): Type
    {
        return $this->parse($node->getChild(0));
    }
}
