<?php

namespace Hamlet\Cast;

use Hoa\Compiler\Llk\TreeNode;
use RuntimeException;

class TypeParser
{
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
                return new UnionType(...$subTypes);
            case '#intersection':
                $subTypes = [];
                for ($i = 0; $i < $node->getChildrenNumber(); $i++) {
                    $subTypes[] = $this->parse($node->getChild($i));
                }
                return _intersection(...$subTypes);
            case '#array':
                return $this->fromArray($node);
            case '#object_like_array':
                return $this->fromObjectLikeArray($node);
            case '#basic_type':
                $type = $this->parse($node->getChild(0));
                for ($i = 1; $i < $node->getChildrenNumber(); $i++) {
                    if ($node->getChild($i)->getId() == '#brackets') {
                        $type = _list($type);
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
                    return new NullType();
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
                return new BoolType();
            case 'integer':
            case 'int':
                return new IntType();
            case 'float':
            case 'double':
                return new FloatType();
            case 'string':
                return new StringType();
            case 'object':
                return new ObjectType();
        }
        throw new RuntimeException('Cannot convert node ' . print_r($node, true));
    }

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
        /** @psalm-suppress ArgumentTypeCoercion */
        return new ClassType($path);
    }

    private function fromArray(TreeNode $node): Type
    {
        switch ($node->getChildrenNumber()) {
            case 1:
                return new ListType(new MixedType());
            case 2:
                return new ListType($this->parse($node->getChild(1)));
            case 3:
                /** @psalm-suppress MixedArgumentTypeCoercion */
                return new MapType($this->parse($node->getChild(1)), $this->parse($node->getChild(2)));
        }
        throw new RuntimeException('Cannot convert node ' . print_r($node, true));
    }

    private function fromProperty(TreeNode $node): Type
    {
        switch ($node->getChildrenNumber()) {
            case 1:
                return _property(null, true, $this->parse($node->getChild(0)));
            case 2:
                $name = $node->getChild(0)->getChild(0)->getValueValue();
                return _property($name, true, $this->parse($node->getChild(1)));
            case 3:
                $name = $node->getChild(0)->getChild(0)->getValueValue();
                return _property($name, false, $this->parse($node->getChild(2)));
        }
        throw new RuntimeException('Cannot convert node ' . print_r($node, true));
    }

    private function fromObjectLikeArray(TreeNode $node): Type
    {
        $properties = [];
        for ($i = 1; $i < $node->getChildrenNumber(); $i++) {
            $child = $node->getChild($i);
            if ($child->getId() == '#property') {
                $properties[] = $this->fromProperty($child);
            } else {
                throw new RuntimeException('Cannot convert node ' . print_r($child, true));
            }
        }
        return _intersection(...$properties);
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
